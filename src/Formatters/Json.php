<?php

namespace Differ\Formatters\Json;

function format(array $diffTree): string
{
    return json_encode(
        $diffTree,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
    );
}
