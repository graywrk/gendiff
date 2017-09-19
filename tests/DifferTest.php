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
        \Differ\genDiff('BIN', __DIR__ . '/fixtures/before.json', __DIR__ . '/fixtures/after.json');
    }

    /**
     * @expectedException \InvalidArgumentException
    */
    public function testGenDiffWithNotExistingFiles()
    {
        \Differ\genDiff('JSON', 'file1', 'file2');
    }

    public function testGenDiffWithSampleJsonFiles()
    {
        $beforeJsonFilePath = __DIR__ . "/fixtures/before.json";
        $afterJsonFilePath = __DIR__ . "/fixtures/after.json";

        $fileDifference = <<<EOL
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}
EOL;
        $this->assertEquals($fileDifference, \Differ\genDiff('pretty', $beforeJsonFilePath, $afterJsonFilePath));
    }

    public function testGenDiffWithSampleYamlFiles()
    {
        $beforeJsonFilePath = __DIR__ . "/fixtures/before.yml";
        $afterJsonFilePath = __DIR__ . "/fixtures/after.yml";

        $fileDifference = <<<EOL
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}
EOL;
        $this->assertEquals($fileDifference, \Differ\genDiff('pretty', $beforeJsonFilePath, $afterJsonFilePath));
    }
}
