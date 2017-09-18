<?php

namespace Differ\Tests;

use \PHPUnit\Framework\TestCase;
use \Differ\Differ;

class DifferTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
    */
    public function testGenDiffWithUnknownFormat()
    {
        Differ::genDiff('BIN', 'file1', 'file2');
    }

    /**
     * @expectedException \InvalidArgumentException
    */
    public function testGenDiffWithNotExistingFiles()
    {
        Differ::genDiff('JSON', 'file1', 'file2');
    }

    public function testGenDiffWithSampleJsonFiles()
    {
        $beforeJsonFilePath = "./fixtures/before.json";
        $afterJsonFilePath = "./fixtures/after.json";

        $fileDifference = <<<EOL
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}
EOL;
        $this->assertEquals($fileDifference, Differ::genDiff('JSON', $beforeJsonFilePath, $afterJsonFilePath));
    }
}
