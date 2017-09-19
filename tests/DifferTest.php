<?php

namespace Differ\Tests;

use \PHPUnit\Framework\TestCase;
use \Differ\genDiff;
use \Differ\getDataFromFile;

class DifferTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
    */
    public function testGenDiffWithUnknownReportFormat()
    {
        \Differ\genDiff('BIN', array(), array());
    }

    /**
     * @expectedException \InvalidArgumentException
    */
    public function testGetDataFromFileWithNotExistingFile()
    {
        \Differ\getDataFromFile('no_exists');
    }

    public function testGenDiffWithSampleJsonFiles()
    {
        $beforeJsonFilePath = __DIR__ . "/fixtures/before.json";
        $afterJsonFilePath = __DIR__ . "/fixtures/after.json";

        $data1 = \Differ\getDataFromFile($beforeJsonFilePath);
        $data2 = \Differ\getDataFromFile($afterJsonFilePath);

        $fileDifference = <<<EOL
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}
EOL;
        $this->assertEquals($fileDifference, \Differ\genDiff('pretty', $data1, $data2));
    }

    public function testGenDiffWithSampleYamlFiles()
    {
        $beforeYamlFilePath = __DIR__ . "/fixtures/before.yml";
        $afterYamlFilePath = __DIR__ . "/fixtures/after.yml";

        $data1 = \Differ\getDataFromFile($beforeYamlFilePath);
        $data2 = \Differ\getDataFromFile($afterYamlFilePath);

        $fileDifference = <<<EOL
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}
EOL;
        $this->assertEquals($fileDifference, \Differ\genDiff('pretty', $data1, $data2));
    }
}
