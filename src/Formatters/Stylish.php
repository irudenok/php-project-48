<?php

namespace Differ\Formatters\Stylish;

function format(array $diffTree): string
{
    return buildStylish($diffTree, 1);
}

function buildStylish(array $nodes, int $depth): string
{
    $indent = str_repeat('  ', $depth);
    $outerIndent = str_repeat('  ', $depth - 1);

    $lines = array_map(function ($node) use ($indent, $depth) {
        $key = $node['key'];
        $type = $node['type'];

        return match ($type) {
            'nested' => [
                "{$indent}  {$key}: " . buildStylish($node['children'], $depth + 2)
            ],
            'added' => [
                "{$indent}+ {$key}: " . formatValue($node['value'], $depth + 1)
            ],
            'removed' => [
                "{$indent}- {$key}: " . formatValue($node['value'], $depth + 1)
            ],
            'unchanged' => [
                "{$indent}  {$key}: " . formatValue($node['value'], $depth + 1)
            ],
            'updated' => [
                "{$indent}- {$key}: " . formatValue($node['oldValue'], $depth + 1),
                "{$indent}+ {$key}: " . formatValue($node['newValue'], $depth + 1)
            ],
            default => []
        };
    }, $nodes);

    $lines = array_merge(...$lines);

    return "{\n" . implode("\n", $lines) . "\n{$outerIndent}}";
}

function formatValue(mixed $value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (!is_array($value)) {
        return (string) $value;
    }

    return formatComplexValue($value, $depth + 1);
}

function formatComplexValue(array $value, int $depth): string
{
    $indent = str_repeat('  ', $depth);
    $outerIndent = str_repeat('  ', $depth - 1);
    $lines = [];

    $lines = array_map(function ($item, $key) use ($indent, $depth) {
        if (is_array($item)) {
            $nested = formatComplexValue($item, $depth + 2);
            return "{$indent}  {$key}: {$nested}";
        } else {
            return "{$indent}  {$key}: " . formatValue($item, $depth + 1);
        }
    }, $value, array_keys($value));

    return "{\n" . implode("\n", $lines) . "\n{$outerIndent}}";
}
