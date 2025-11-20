<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parse(string $content, string $extension): array
{
    return match ($extension) {
        'json' => json_decode($content, true),
        'yaml', 'yml' => Yaml::parse($content),
        default => 'Incorrect way!',
    };
}
