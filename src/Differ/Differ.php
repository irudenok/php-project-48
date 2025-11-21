<?php

namespace Differ\Differ;

use function Funct\Collection\sortBy;
use function Differ\Reader\readFile;
use function Differ\Parser\parse;
use function Differ\Formatters\render;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    ['content' => $content1, 'extension' => $extension1] = readFile($pathToFile1);
    ['content' => $content2, 'extension' => $extension2] = readFile($pathToFile2);

    $data1 = parse($content1, $extension1);
    $data2 = parse($content2, $extension2);

    $diff = buildDiffTree($data1, $data2);

    return render($diff, $format);
}

function buildDiffTree(array $data1, array $data2): array
{
    $keys = array_unique(
        array_merge(
            array_keys($data1),
            array_keys($data2)
        )
    );

    $sortedKeys = sortBy($keys, fn ($key) => $key);

    $result = array_map(function (string $key) use ($data1, $data2): array {
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
