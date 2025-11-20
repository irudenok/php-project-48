<?php

namespace Differ\Reader;

function readFile(string $pathToFile): array
{
    if (!file_exists($pathToFile)) {
        $path = __DIR__ . "/../../tests/fixtures/{$pathToFile}";
    } else {
        $path = $pathToFile;
    }

    $pathInfo = pathinfo($path);
    $extension = $pathInfo['extension'] ?? '';

    return ['content' => (string) file_get_contents($path), 'extension' => $extension];
}
