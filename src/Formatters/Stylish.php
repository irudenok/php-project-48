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
    $lines = [];

    foreach ($nodes as $node) {
        $key = $node['key'];
        $type = $node['type'];

        switch ($type) {
            case 'nested':
                $childDiff = buildStylish($node['children'], $depth + 2);
                $lines[] = "{$indent}  {$key}: {$childDiff}";
                break;
            case 'added':
                $value = formatValue($node['value'], $depth + 1);
                $lines[] = "{$indent}+ {$key}: {$value}";
                break;
            case 'removed':
                $value = formatValue($node['value'], $depth + 1);
                $lines[] = "{$indent}- {$key}: {$value}";
                break;
            case 'unchanged':
                $value = formatValue($node['value'], $depth + 1);
                $lines[] = "{$indent}  {$key}: {$value}";
                break;
            case 'updated':
                $oldValue = formatValue($node['oldValue'], $depth + 1);
                $newValue = formatValue($node['newValue'], $depth + 1);
                $lines[] = "{$indent}- {$key}: {$oldValue}";
                $lines[] = "{$indent}+ {$key}: {$newValue}";
                break;
        }
    }

    return "{\n" . implode("\n", $lines) . "\n{$outerIndent}}";
}

function formatValue($value, int $depth): string
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

    foreach ($value as $key => $item) {
        if (is_array($item)) {
            $nested = formatComplexValue($item, $depth + 2);
            $lines[] = "{$indent}  {$key}: {$nested}";
        } else {
            $lines[] = "{$indent}  {$key}: " . formatValue($item, $depth + 1);
        }
    }

    return "{\n" . implode("\n", $lines) . "\n{$outerIndent}}";
}

