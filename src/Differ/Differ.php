<?php

namespace Differ\Differ;

use Funct\Collection;

use function Differ\Parser\parseFile;
use function Differ\Formatters\Format as formatDiff;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $data1 = parseFile($pathToFile1);
    $data2 = parseFile($pathToFile2);

    $diff = buildDiffTree($data1, $data2);

    return formatDiff($diff, $format);
}

function buildDiffTree(array $data1, array $data2): array
{
    $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortedKeys = Collection\sortBy($keys, fn ($key) => $key);

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
