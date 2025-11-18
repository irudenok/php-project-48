<?php

namespace Differ\Differ;

use Funct\Collection;

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

function parseFile(string $filePath): array
{
    if (!file_exists($filePath)) {
        throw new \Exception("File not found: {$filePath}");
    }

    $content = file_get_contents($filePath);
    $data = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception("Invalid JSON in file: {$filePath}");
    }

    return $data;
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
