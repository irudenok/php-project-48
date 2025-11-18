<?php

namespace Differ\Differ;

use Funct\Collection;

use function Differ\Parser\parseFile;

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $data1 = parseFile($pathToFile1);
    $data2 = parseFile($pathToFile2);

    $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortedKeys = Collection\sortBy($keys, function ($key) {
        return $key;
    });

    $lines = [];
    foreach ($sortedKeys as $key) {
        $lines[] = buildDiffLine($key, $data1, $data2);
    }

    return "{\n" . implode("\n", $lines) . "\n}";
}

function buildDiffLine(string $key, array $data1, array $data2): string
{
    $value1 = $data1[$key] ?? null;
    $value2 = $data2[$key] ?? null;

    $hasInFirst = array_key_exists($key, $data1);
    $hasInSecond = array_key_exists($key, $data2);

    if ($hasInFirst && $hasInSecond) {
        if ($value1 === $value2) {
            return "    {$key}: " . formatValue($value1);
        } else {
            return "  - {$key}: " . formatValue($value1) . "\n  + {$key}: " . formatValue($value2);
        }
    } elseif ($hasInFirst) {
        return "  - {$key}: " . formatValue($value1);
    } else {
        return "  + {$key}: " . formatValue($value2);
    }
}

function formatValue($value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    return (string) $value;
}
