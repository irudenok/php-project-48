<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\format as formatStylish;

function format(array $diff, string $format): string
{
    return match ($format) {
        'stylish' => formatStylish($diff),
        default => throw new \Exception("Unknown format: {$format}"),
    };
}
