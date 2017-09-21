<?php

namespace Differ\Reporters\Pretty;

function report($AST, $depth = 0)
{
    return array_reduce($AST, function ($report, $item) use ($depth) {
        switch ($item['type']) {
            case 'NESTED':
                $report .= str_repeat(' ', 4 * $depth + 4);
                $report .= "\""
                        . $item['key']
                        . "\": {\n"
                        . report($item['children'], $depth + 1);
                $report .= str_repeat(' ', 4 * $depth + 4) . "}\n";
                break;
            case 'UNCHANGED':
                $report .= str_repeat(' ', 4 * $depth + 4);
                $report .= "\""
                        . $item['key']
                        . "\": "
                        . convertToString($item['value'], $depth + 1)
                        . "\n";
                break;
            case 'ADDED':
                $report .= str_repeat(' ', 4 * $depth + 2) . '+ ';
                $report .= "\""
                        . $item['key'] . "\": "
                        . convertToString($item['value'], $depth + 1)
                        . "\n";
                break;
            case 'DELETED':
                $report .= str_repeat(' ', 4 * $depth + 2) .  '- ';
                $report .= "\""
                        . $item['key'] . "\": "
                        . convertToString($item['value'], $depth + 1)
                        . "\n";
                break;
            case 'CHANGED':
                $report .= str_repeat(' ', 4 * $depth + 2) . '+ ';
                $report .= "\""
                        . $item['key'] . "\": "
                        . convertToString($item['value'], $depth + 1)
                        . "\n";
                $report .= str_repeat(' ', 4 * $depth + 2) .  '- ';
                $report .= "\""
                        . $item['key'] . "\": "
                        . convertToString($item['old_value'], $depth + 1)
                        . "\n";
                break;
        }

        return $report;
    }, "");
}

function convertToString($value, $depth)
{
    if (is_bool($value)) {
        $result = $value ? 'true' : 'false';
    } elseif (is_array($value)) {
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
