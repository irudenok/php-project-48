<?php

namespace Differ\Formatters\Plain;

function format(array $diffTree): string
{
    $lines = buildPlain($diffTree);
    return implode("\n", $lines);
}

function buildPlain(array $nodes, string $path = ''): array
{
    $lines = array_map(function (array $node) use ($path): array {
        $key = $node['key'];
        $property = $path === '' ? $key : "{$path}.{$key}";

        $nodeValue = stringifyValue($node['value'] ?? null);
        $nodeOldValue = stringifyValue($node['oldValue'] ?? null);
        $nodeNewValue = stringifyValue($node['newValue'] ?? null);

        return match ($node['type']) {
            'nested' => buildPlain($node['children'], $property),
            'added' => ["Property '{$property}' was added with value: {$nodeValue}"],
            'removed' => ["Property '{$property}' was removed"],
            'updated' => [
                "Property '{$property}' was updated. From {$nodeOldValue} to {$nodeNewValue}"
            ],
            'unchanged' => [],
            default => throw new InvalidArgumentException("Unknown node type: {$node['type']}")
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
        return "'{$value}'";
    }

    return (string) $value;
}
