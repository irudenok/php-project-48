<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $pathToFile): array
{
    if (!file_exists($pathToFile)) {
        $path = __DIR__ . "/../../tests/fixtures/{$pathToFile}";
    } else {
        $path = $pathToFile;
    }

    $pathInfo = pathinfo($path);
    $extension = $pathInfo['extension'] ?? '';

    $parsedFile = match ($extension) {
        'json' => json_decode((string) file_get_contents($path), true),
        'yaml', 'yml' => Yaml::parse((string) file_get_contents($path)),
        default => 'Incorrect way!',
    };

    return $parsedFile;
}
