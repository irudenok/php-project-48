<?php

namespace Differ\Differ;

use function Funct\Collection\sortBy;
use function Differ\Reader\readFile;
use function Differ\Parser\parse;
use function Differ\Formatters\render;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    [$firstFileContent, $firstFileExtension] = readFile($pathToFile1);
    [$secondFileContent, $secondFileExtension] = readFile($pathToFile2);

    $data1 = parse($firstFileContent, $firstFileExtension);
    $data2 = parse($secondFileContent, $secondFileExtension);

    $diff = buildDiffTree($data1, $data2);

    return render($diff, $format);
}

function buildDiffTree(array $data1, array $data2): array
{
    $data1Keys = array_keys($data1);
    $data2Keys = array_keys($data2);

    $mergedKeys = array_merge($data1Keys, $data2Keys);
    $keys = array_unique($mergedKeys);
    $sortedKeys = sortBy($keys, fn ($key) => $key);

    $result = array_map(function ($key) use ($data1, $data2) {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;

        $hasInFirst = array_key_exists($key, $data1);
        $hasInSecond = array_key_exists($key, $data2);

        if ($hasInFirst && $hasInSecond) {
            if (is_array($value1) && is_array($value2)) {
                return [
                    'key' => $key,
                    'type' => 'nested',
                    'children' => buildDiffTree($value1, $value2),
                ];
            }

            if ($value1 === $value2) {
                return [
                    'key' => $key,
                    'type' => 'unchanged',
                    'value' => $value1,
                ];
            }

            return [
                'key' => $key,
                'type' => 'updated',
                'oldValue' => $value1,
                'newValue' => $value2,
            ];
        }

        if ($hasInFirst) {
            return [
                'key' => $key,
                'type' => 'removed',
                'value' => $value1,
            ];
        }

        return [
            'key' => $key,
            'type' => 'added',
            'value' => $value2,
        ];
    }, $sortedKeys);

    return array_values($result);
}
