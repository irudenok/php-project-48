<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\format as formatStylish;
use function Differ\Formatters\Plain\format as formatPlain;
use function Differ\Formatters\Json\format as formatJson;

function format(array $diff, string $format): string
{
    return match ($format) {
        'stylish' => formatStylish($diff),
        'plain' => formatPlain($diff),
        'json' => formatJson($diff),
        default => throw new \Exception("Unknown format: {$format}"),
    };
}
