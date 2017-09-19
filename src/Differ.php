<?php

namespace Differ;

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

    $diff = array();

    foreach ($data1 as $k => $v) {
        if (array_key_exists($k, $data2)) {
            if ($v === $data2[$k]) {
                $diff[] = array('key' => $k, 'value' => convertToString($v), 'state' => 'UNCHANGED');
            } else {
                $diff[] = array('key' => $k, 'value' => convertToString($data2[$k]), 'state' => 'ADDED');
                $diff[] = array('key' => $k, 'value' => convertToString($v), 'state' => 'DELETED');
            }
        } else {
            $diff[] = array('key' => $k, 'value' => convertToString($v, true), 'state' => 'DELETED');
        }
    }

    foreach ($data2 as $k => $v) {
        if (!array_key_exists($k, $data1)) {
            $diff[] = array('key' => $k, 'value' => convertToString($v, true), 'state' => 'ADDED');
        }
    }

    return buildReport($reportFormat, $diff);
}

function buildReport($reportFormat, $diff)
{
    if (!in_array($reportFormat, array('pretty'))) {
        throw new \InvalidArgumentException("Unknown report format: " . $reportFormat . "!");
    }

    if ($reportFormat === 'pretty') {
        $report = "{\n";
        foreach ($diff as $item) {
            switch ($item['state']) {
                case 'UNCHANGED':
                    $report .= str_repeat(' ', 4);
                    break;
                case 'ADDED':
                    $report .= str_repeat(' ', 2) . '+ ';
                    break;
                case 'DELETED':
                    $report .= str_repeat(' ', 2) .  '- ';
                    break;
            }
            $report .= $item['key'] . ": " . $item['value'] . "\n";
        }

        $report .= "}";
    }

    return $report;
}

function convertToString($value)
{
    if (is_bool($value)) {
        $value = $value ? 'true' : 'false';
    }

    return $value;
}

function getDataFromFile($pathToFile)
{
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    $fileContent = file_get_contents($pathToFile);

    return parser($extension, $fileContent);
}
