<?php

if (! function_exists('safe_addslashes')) {
    /**
     * Safely apply addslashes to a potentially null string.
     */
    function safe_addslashes(?string $string): string
    {
        return addslashes($string ?? '');
    }
}
