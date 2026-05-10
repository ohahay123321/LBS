<?php

if (! function_exists('addlashes')) {
    function addlashes($string)
    {
        return addslashes($string ?? '');
    }
}
