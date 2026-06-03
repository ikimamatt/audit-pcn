<?php

namespace App\Helpers;

class QueryHelper
{
    /**
     * Escape special LIKE characters (%, _, \) to prevent wildcard injection.
     * Use this before passing user input to a LIKE clause.
     */
    public static function escapeLike(string $value): string
    {
        return str_replace(['%', '_', '\\'], ['\\%', '\\_', '\\\\'], $value);
    }
}
