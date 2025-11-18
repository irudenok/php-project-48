<?php

namespace Differ\Tests;

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $file1 = 'file1.json';
        $file2 = 'file2.json';

        $result = file_get_contents(__DIR__ . '/fixtures/test_result');
        $this->assertEquals($result, genDiff($file1, $file2));
    }

    public function testYaml(): void
    {
        $file1 = 'file1.yml';
        $file2 = 'file2.yml';

        $result = file_get_contents(__DIR__ . '/fixtures/test_result');
        $this->assertEquals($result, genDiff($file1, $file2));
    }
}
