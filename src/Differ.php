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
                if (is_array($data1[$key]) && is_array($data2[$key])) {
                    $acc[] = array('key' => $key,
                                   'state' => 'NESTED',
                                   'value' => generateAST($data1[$key], $data2[$key]));
                } else {
                    if ($data1[$key] === $data2[$key]) {
                        $acc[] = array('key' => $key, 'state' => 'UNCHANGED', 'value' => $data1[$key]);
                    } else {
                        $acc[] = array('key' => $key, 'state' => 'ADDED', 'value' => $data2[$key]);
                        $acc[] = array('key' => $key, 'state' => 'DELETED', 'value' => $data1[$key]);
                    }
                }
            } else {
                $acc[] = array('key' => $key, 'state' => 'DELETED', 'value' => $data1[$key]);
            }
        } else {
            $acc[] = array('key' => $key, 'state' => 'ADDED', 'value' => $data2[$key]);
        }

        return $acc;
    }, []);
}

function convertToStringToPrettyReport($value, $depth)
{
    if (is_bool($value)) {
        $result = $value ? 'true' : 'false';
    } elseif (is_array($value)) {
        // $value = json_encode($value, JSON_PRETTY_PRINT);
        $result = "{\n";
        $result .= array_reduce(array_keys($value), function ($acc, $key) use ($depth, $value) {
            $acc .= str_repeat(" ", 4 * $depth + 4);
            $acc .=  "\"" . $key . "\": \"" . $value[$key] . "\"\n";
            return $acc;
        }, "");
        $result .= str_repeat(" ", 4 * ($depth - 1) + 4) . "}";
    } else {
        $result = "\"" . $value . "\"";
    }

    return $result;
}

function buildReport($reportFormat, $AST)
{
    switch ($reportFormat) {
        case 'pretty':
            return "{\n" . reportPretty($AST) . "}";
            break;
        default:
            throw new \InvalidArgumentException("Unknown report format: " . $reportFormat . "!");
    }
}

function reportPretty($AST, $depth = 0)
{
    return array_reduce($AST, function ($report, $item) use ($depth) {
        switch ($item['state']) {
            case 'NESTED':
                $report .= str_repeat(' ', 4 * $depth + 4);
                $report .= "\""
                        . $item['key']
                        . "\": {\n"
                        . reportPretty($item['value'], $depth + 1);
                $report .= str_repeat(' ', 4 * $depth + 4) . "}\n";
                break;
            case 'UNCHANGED':
                $report .= str_repeat(' ', 4 * $depth + 4);
                $report .= "\""
                        . $item['key']
                        . "\": "
                        . convertToStringToPrettyReport($item['value'], $depth + 1)
                        . "\n";
                break;
            case 'ADDED':
                $report .= str_repeat(' ', 4 * $depth + 2) . '+ ';
                $report .= "\""
                        . $item['key'] . "\": "
                        . convertToStringToPrettyReport($item['value'], $depth + 1)
                        . "\n";
                break;
            case 'DELETED':
                $report .= str_repeat(' ', 4 * $depth + 2) .  '- ';
                $report .= "\""
                        . $item['key'] . "\": "
                        . convertToStringToPrettyReport($item['value'], $depth + 1)
                        . "\n";
                break;
        }

        return $report;
    }, "");
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
