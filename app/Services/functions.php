<?php

namespace App\Services;

/**
 * Override file_get_contents for testing purposes.
 * PHP resolves namespaced functions before global ones.
 */
function file_get_contents(string $filename): string|false
{
    if (isset($GLOBALS['__test_file_get_contents'][$filename])) {
        return $GLOBALS['__test_file_get_contents'][$filename];
    }

    return \file_get_contents($filename);
}
