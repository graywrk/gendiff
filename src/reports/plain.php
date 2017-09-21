<?php

namespace Differ\Reports\Plain;

function report($AST, $propertyName = '')
{
    return array_reduce($AST, function ($report, $item) use ($propertyName) {
        if ($propertyName !== '') {
            $propertyName .= "." . $item['key'];
        } else {
            $propertyName = $item['key'];
        }
        $propertyValue = $item['value'];

        switch ($item['state']) {
            case 'NESTED':
                $report .= report($propertyValue, $propertyName);
                break;
            case 'ADDED':
                $propertyValue = is_array($item['value']) ? 'complex value' : $item['value'];
                $report .= "Property '"
                        . $propertyName
                        . "' was added with value: '"
                        . $propertyValue
                        . "'\n";
                break;
            case 'DELETED':
                $report .= "Property '"
                        . $propertyName
                        . "' was removed"
                        . "\n";
                break;
            case 'CHANGED':
                $report .= "Property '"
                        . $propertyName
                        . "' was changed."
                        . " From '" . $item['old_value'] . "'"
                        . " to '" . $propertyValue . "'"
                        . "\n";
                break;
        }

        return $report;
    }, "");
}
