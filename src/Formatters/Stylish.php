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

    $lines = array_map(function (array $node) use ($indent, $depth): array {
        $key = $node['key'];
        $type = $node['type'];

        $formattedValue = formatValue($node['value'] ?? null, $depth + 1);
        $formattedOldValue = formatValue($node['oldValue'] ?? null, $depth + 1);
        $formattedNewValue = formatValue($node['newValue'] ?? null, $depth + 1);
        $nestedChildren = buildStylish($node['children'] ?? [], $depth + 2);

        return match ($type) {
            'nested' => [
                "{$indent}  {$key}: {$nestedChildren}"
            ],
            'added' => [
                "{$indent}+ {$key}: {$formattedValue}"
            ],
            'removed' => [
                "{$indent}- {$key}: {$formattedValue}"
            ],
            'unchanged' => [
                "{$indent}  {$key}: {$formattedValue}"
            ],
            'updated' => [
                "{$indent}- {$key}: {$formattedOldValue}",
                "{$indent}+ {$key}: {$formattedNewValue}"
            ],
            default => throw new InvalidArgumentException("Unknown node type: {$type}")
        };
    }, $nodes);

    $lines = implode(
        "\n",
        array_merge(...$lines)
    );

    return "{\n{$lines}\n{$outerIndent}}";
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

    $lines = array_map(function (mixed $item, string $key) use ($indent, $depth): string {

        $formattedValue = formatValue($item, $depth + 1);

        if (is_array($item)) {
            $nested = formatComplexValue($item, $depth + 2);
            return "{$indent}  {$key}: {$nested}";
        } else {
            return "{$indent}  {$key}: {$formattedValue}";
        }
    }, $value, array_keys($value));

    $lines = implode("\n", $lines);

    return "{\n{$lines}\n{$outerIndent}}";
}
