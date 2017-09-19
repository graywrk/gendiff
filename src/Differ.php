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

    $data1 = json_decode(file_get_contents($pathToFile1), true);
    $data2 = json_decode(file_get_contents($pathToFile2), true);

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

    return makeReport($reportFormat, $diff);
}

function makeReport($reportFormat, $diff)
{
    if (!in_array($reportFormat, array('pretty'))) {
        throw new \InvalidArgumentException("Unknown report format: " . $reportFormat . "!");
    }

    if ($reportFormat === 'pretty') {
        $report = "{\n";
        foreach ($diff as $item) {
            switch ($item['state']) {
                case 'UNCHANGED':
                    $report .= "    ";
                    break;
                case 'ADDED':
                    $report .= "  + ";
                    break;
                case 'DELETED':
                    $report .= "  - ";
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
