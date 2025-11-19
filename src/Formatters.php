<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\format as formatStylish;
use function Differ\Formatters\Plain\format as formatPlain;

function format(array $diff, string $format): string
{
    return match ($format) {
        'stylish' => formatStylish($diff),
        'plain' => formatPlain($diff),
        default => throw new \Exception("Unknown format: {$format}"),
    };
}
