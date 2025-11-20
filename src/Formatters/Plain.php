<?php

namespace Differ\Formatters\Plain;

function format(array $diffTree): string
{
    $lines = buildPlain($diffTree);
    return implode("\n", $lines);
}

function buildPlain(array $nodes, string $path = ''): array
{
    $lines = [];

    $lines = array_map(function ($node) use ($path, $lines) {
        $key = $node['key'];
        $property = $path === '' ? $key : "{$path}.{$key}";

        return match ($node['type']) {
            'nested' => buildPlain($node['children'], $property),
            'added' => ["Property '{$property}' was added with value: " . stringifyValue($node['value'])],
            'removed' => ["Property '{$property}' was removed"],
            'updated' => [
                "Property '{$property}' was updated. From " .
                stringifyValue($node['oldValue']) . " to " .
                stringifyValue($node['newValue'])
            ],
            'unchanged' => [],
            default => []
        };
    }, $nodes);

    $lines = array_merge(...$lines);

    return $lines;
}

function stringifyValue(mixed $value): string
{
    if (is_array($value)) {
        return '[complex value]';
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_string($value)) {
        return "'" . $value . "'";
    }

    return (string) $value;
}
