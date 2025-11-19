<?php

namespace Differ\Differ;

use Funct\Collection;

use function Differ\Parser\parseFile;

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $data1 = parseFile($pathToFile1);
    $data2 = parseFile($pathToFile2);

    return buildDiff($data1, $data2);
}

function buildDiff(array $data1, array $data2, int $depth = 1): string
{
    $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortedKeys = Collection\sortBy($keys, function ($key) {
        return $key;
    });

    $lines = [];
    $indent = str_repeat('  ', $depth);

    foreach ($sortedKeys as $key) {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;

        $hasInFirst = array_key_exists($key, $data1);
        $hasInSecond = array_key_exists($key, $data2);

        if ($hasInFirst && $hasInSecond) {
            if (is_array($value1) && is_array($value2)) {
                // Оба значения - массивы, рекурсивно сравниваем
                $nestedDiff = buildDiff($value1, $value2, $depth + 2);
                $lines[] = "{$indent}  {$key}: {$nestedDiff}";
            } elseif ($value1 === $value2) {
                // Простые значения равны
                $lines[] = "{$indent}  {$key}: " . formatValue($value1, $depth + 1);
            } else {
                // Простые значения разные
                $lines[] = "{$indent}- {$key}: " . formatValue($value1, $depth + 1);
                $lines[] = "{$indent}+ {$key}: " . formatValue($value2, $depth + 1);
            }
        } elseif ($hasInFirst) {
            // Ключ только в первом файле
            $lines[] = "{$indent}- {$key}: " . formatValue($value1, $depth + 1);
        } else {
            // Ключ только во втором файле
            $lines[] = "{$indent}+ {$key}: " . formatValue($value2, $depth + 1);
        }
    }

    $outerIndent = str_repeat('  ', $depth - 1);
    return "{\n" . implode("\n", $lines) . "\n{$outerIndent}}";
}

function buildNestedStructure(array $data, int $depth): string
{
    $indent = str_repeat('  ', $depth);
    $outerIndent = str_repeat('  ', $depth - 1);
    $lines = [];

    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $nested = buildNestedStructure($value, $depth + 2);
            $lines[] = "{$indent}  {$key}: {$nested}";
        } else {
            $lines[] = "{$indent}  {$key}: " . formatValue($value, $depth + 1);
        }
    }

    return "{\n" . implode("\n", $lines) . "\n{$outerIndent}}";
}

function formatValue($value, int $depth = 1): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value)) {
        return buildNestedStructure($value, $depth + 1);
    }

    return (string) $value;
}
