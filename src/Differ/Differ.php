<?php

namespace Differ\Differ;

use function Funct\Collection\sortBy;
use function Differ\Reader\readFile;
use function Differ\Parser\parse;
use function Differ\Formatters\render;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $file1 = readFile($pathToFile1);
    $file2 = readFile($pathToFile2);

    $data1 = parse($file1);
    $data2 = parse($file2);

    $diff = buildDiffTree($data1, $data2);

    return render($diff, $format);
}

function buildDiffTree(array $data1, array $data2): array
{
    $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortedKeys = sortBy($keys, fn ($key) => $key);

    $diff = [];

    foreach ($sortedKeys as $key) {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;

        $hasInFirst = array_key_exists($key, $data1);
        $hasInSecond = array_key_exists($key, $data2);

        if ($hasInFirst && $hasInSecond) {
            if (is_array($value1) && is_array($value2)) {
                $diff[] = [
                    'key' => $key,
                    'type' => 'nested',
                    'children' => buildDiffTree($value1, $value2),
                ];
                continue;
            }

            if ($value1 === $value2) {
                $diff[] = [
                    'key' => $key,
                    'type' => 'unchanged',
                    'value' => $value1,
                ];
                continue;
            }

            $diff[] = [
                'key' => $key,
                'type' => 'updated',
                'oldValue' => $value1,
                'newValue' => $value2,
            ];
            continue;
        }

        if ($hasInFirst) {
            $diff[] = [
                'key' => $key,
                'type' => 'removed',
                'value' => $value1,
            ];
            continue;
        }

        $diff[] = [
            'key' => $key,
            'type' => 'added',
            'value' => $value2,
        ];
    }

    return $diff;
}
