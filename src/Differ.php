<?php

namespace Differ;

use Funct\Collection;

function genDiff($reportFormat, $pathToFile1, $pathToFile2)
{
    $data1 = getDataFromFile($pathToFile1);
    $data2 = getDataFromFile($pathToFile2);

    $AST = generateAST($data1, $data2);

    return buildReport($reportFormat, $AST);
}

function generateAST($data1, $data2)
{
    $keys = Collection\union(array_keys($data1), array_keys($data2));

    return array_reduce($keys, function ($acc, $key) use ($data1, $data2) {
        if (array_key_exists($key, $data1)) {
            if (array_key_exists($key, $data2)) {
                if (isAssocArray($data1[$key]) && isAssocArray($data2[$key])) {
                    $acc[] = array('key' => $key,
                                   'type' => 'NESTED',
                                   'children' => generateAST($data1[$key], $data2[$key]));
                } else {
                    if ($data1[$key] === $data2[$key]) {
                        $acc[] = array('key' => $key, 'type' => 'UNCHANGED', 'value' => $data1[$key]);
                    } else {
                        $acc[] = array('key' => $key, 'type' => 'CHANGED', 'value' => $data2[$key],
                                       'old_value' => $data1[$key]);
                    }
                }
            } else {
                $acc[] = array('key' => $key, 'type' => 'DELETED', 'value' => $data1[$key]);
            }
        } else {
            $acc[] = array('key' => $key, 'type' => 'ADDED', 'value' => $data2[$key]);
        }

        return $acc;
    }, []);
}

function buildReport($reportFormat, $AST)
{
    switch ($reportFormat) {
        case 'pretty':
            return "{\n" . Reporters\Pretty\report($AST) . "}";
            break;
        case 'plain':
            return Reporters\Plain\report($AST);
            break;
        case 'json':
            return Reporters\Json\report($AST);
            break;
        default:
            throw new \InvalidArgumentException("Unknown report format: " . $reportFormat . "!");
    }
}

function getDataFromFile($pathToFile)
{
    if (!file_exists($pathToFile)) {
        throw new \InvalidArgumentException("File " . $pathToFile . " not exist!");
    }

    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    $fileContent = file_get_contents($pathToFile);

    return parser($extension, $fileContent);
}

// Считаем массив ассоциативным если в нем присутствуют ключи с типом отличным от int
function isAssocArray($arr)
{
    if (!is_array($arr)) {
        return false;
    } else {
        return in_array(false, array_map('is_integer', array_keys($arr)));
    }
}
