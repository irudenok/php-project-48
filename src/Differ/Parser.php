<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parse(array $data): array
{
    return match ($data['extension']) {
        'json' => json_decode($data['content'], true),
        'yaml', 'yml' => Yaml::parse($data['content']),
        default => 'Incorrect way!',
    };
}
