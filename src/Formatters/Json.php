<?php

namespace Differ\Formatters\Json;

function format(array $diffTree): string
{
    $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
    $encoded = json_encode($diffTree, $options);

    if ($encoded === false) {
        throw new \RuntimeException('Failed to encode diff to JSON');
    }

    return $encoded;
}
