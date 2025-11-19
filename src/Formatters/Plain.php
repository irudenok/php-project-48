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

    foreach ($nodes as $node) {
        $key = $node['key'];
        $property = $path === '' ? $key : "{$path}.{$key}";

        switch ($node['type']) {
            case 'nested':
                $lines = array_merge($lines, buildPlain($node['children'], $property));
                break;
            case 'added':
                $value = stringifyValue($node['value']);
                $lines[] = "Property '{$property}' was added with value: {$value}";
                break;
            case 'removed':
                $lines[] = "Property '{$property}' was removed";
                break;
            case 'updated':
                $oldValue = stringifyValue($node['oldValue']);
                $newValue = stringifyValue($node['newValue']);
                $lines[] = "Property '{$property}' was updated. From {$oldValue} to {$newValue}";
                break;
            case 'unchanged':
                break;
        }
    }

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
