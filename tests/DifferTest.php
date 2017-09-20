<?php

namespace Differ\Tests;

use \PHPUnit\Framework\TestCase;
use \Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
    */
    public function testGenDiffWithUnknownReportFormat()
    {
        $beforeJsonFilePath = __DIR__ . "/fixtures/before.json";
        $afterJsonFilePath = __DIR__ . "/fixtures/after.json";

        \Differ\genDiff('BIN', $beforeJsonFilePath, $afterJsonFilePath);
    }

    /**
     * @expectedException \InvalidArgumentException
    */
    public function testGetDataFromFileWithNotExistingFile()
    {
        \Differ\genDiff('pretty', 'no_exist_file_1', 'no_exist_file_2');
    }

    public function testGenDiffWithSampleJsonFiles()
    {
        $beforeJsonFilePath = __DIR__ . "/fixtures/before.json";
        $afterJsonFilePath = __DIR__ . "/fixtures/after.json";

        $fileDifference = <<<EOL
{
    "host": "hexlet.io"
  + "timeout": "20"
  - "timeout": "50"
  - "proxy": "123.234.53.22"
  + "verbose": true
}
EOL;
        $this->assertEquals($fileDifference, \Differ\genDiff('pretty', $beforeJsonFilePath, $afterJsonFilePath));
    }

    public function testGenDiffWithSampleYamlFiles()
    {
        $beforeYamlFilePath = __DIR__ . "/fixtures/before.yml";
        $afterYamlFilePath = __DIR__ . "/fixtures/after.yml";

        $fileDifference = <<<EOL
{
    "host": "hexlet.io"
  + "timeout": "20"
  - "timeout": "50"
  - "proxy": "123.234.53.22"
  + "verbose": true
}
EOL;
        $this->assertEquals($fileDifference, \Differ\genDiff('pretty', $beforeYamlFilePath, $afterYamlFilePath));
    }

    public function testGenDiffWithSampleNestedJsonFiles()
    {
        $beforeJsonFilePath = __DIR__ . "/fixtures/beforeWithNested.json";
        $afterJsonFilePath = __DIR__ . "/fixtures/afterWithNested.json";

        $fileDifference = <<<EOL
{
    "common": {
        "setting1": "Value 1"
      - "setting2": "200"
        "setting3": true
      - "setting6": {
            "key": "value"
        }
      + "setting4": "blah blah"
      + "setting5": {
            "key5": "value5"
        }
    }
    "group1": {
      + "baz": "bars"
      - "baz": "bas"
        "foo": "bar"
    }
  - "group2": {
        "abc": "12345"
    }
  + "group3": {
        "fee": "100500"
    }
}
EOL;
        $this->assertEquals($fileDifference, \Differ\genDiff('pretty', $beforeJsonFilePath, $afterJsonFilePath));
    }

    public function testGenDiffWithPlainOutputFormat()
    {
        $beforeJsonFilePath = __DIR__ . "/fixtures/beforeWithNested.json";
        $afterJsonFilePath = __DIR__ . "/fixtures/afterWithNested.json";

        $fileDifference = <<<EOL
Property 'common.setting2' was removed
Property 'common.setting6' was removed
Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: 'complex value'
Property group1.baz was changed. From 'bas' to 'bars'
Property 'group2' was removed
Property 'group3' was added with value: 'complex value'

EOL;
        $this->assertEquals($fileDifference, \Differ\genDiff('plain', $beforeJsonFilePath, $afterJsonFilePath));
    }
}
