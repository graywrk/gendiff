<?php

namespace Differ;

use Symfony\Component\Yaml\Yaml;

function parser($format, $content)
{
    switch ($format) {
        case 'yml':
            $data = Yaml::parse($content);
            break;
        case 'json':
            $data = json_decode($content, true);
            break;
        default:
            throw new Exception("Unknown file format");
    }

    return $data;
}
