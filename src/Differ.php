<?php

namespace Differ;

use Funct\Collection;

function genDiff($reportFormat, $pathToFile1, $pathToFile2)
{
    if (!file_exists($pathToFile1)) {
        throw new \InvalidArgumentException("File " . $pathToFile1 . " not exist!");
    }

    if (!file_exists($pathToFile2)) {
        throw new \InvalidArgumentException("File " . $pathToFile2 . " not exist!");
    }

    $data1 = getDataFromFile($pathToFile1);
    $data2 = getDataFromFile($pathToFile2);

    $data = Collection\union(array_keys($data1), array_keys($data2));

    $diff = array_map(function ($key) use ($data1, $data2) {
        $convertToString = function ($value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            return $value;
        };

        if (array_key_exists($key, $data1)) {
            if (array_key_exists($key, $data2)) {
                if ($data1[$key] === $data2[$key]) {
                    return array('key' => $key, 'value' => $convertToString($data1[$key]), 'state' => 'UNCHANGED');
                } else {
                    return array('key' => $key, 'value' => $convertToString($data2[$key]),
                                 'old_value' => $convertToString($data1[$key]), 'state' => 'CHANGED');
                }
            } else {
                return array('key' => $key, 'value' => $convertToString($data1[$key]), 'state' => 'DELETED');
            }
        } else {
            return array('key' => $key, 'value' => $convertToString($data2[$key]), 'state' => 'ADDED');
        }
    }, $data);

    return buildReport($reportFormat, $diff);
}

function buildReport($reportFormat, $diff)
{
    if (!in_array($reportFormat, array('pretty'))) {
        throw new \InvalidArgumentException("Unknown report format: " . $reportFormat . "!");
    }

    if ($reportFormat === 'pretty') {
        $report = array_reduce($diff, function ($report, $item) {
            switch ($item['state']) {
                case 'UNCHANGED':
                    $report .= str_repeat(' ', 4);
                    $report .= $item['key'] . ": " . $item['value'] . "\n";
                    break;
                case 'ADDED':
                    $report .= str_repeat(' ', 2) . '+ ';
                    $report .= $item['key'] . ": " . $item['value'] . "\n";
                    break;
                case 'DELETED':
                    $report .= str_repeat(' ', 2) .  '- ';
                    $report .= $item['key'] . ": " . $item['value'] . "\n";
                    break;
                case 'CHANGED':
                    $report .= str_repeat(' ', 2) . '+ ';
                    $report .= $item['key'] . ": " . $item['value'] . "\n";
                    $report .= str_repeat(' ', 2) .  '- ';
                    $report .= $item['key'] . ": " . $item['old_value'] . "\n";
                    break;
            }

            return $report;
        }, "{\n");

        $report .= "}";
    }

    return $report;
}

function getDataFromFile($pathToFile)
{
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    $fileContent = file_get_contents($pathToFile);

    return parser($extension, $fileContent);
}
