<?php

if (! function_exists('urljoin')) {
    function urljoin(string $root, string $current, string $path) {
        // Empty links
        if (! $path) {
            return $current;
        }

        // External links
        if (strpos($path, '//')) {
            return $path;
        }

        // Internal links
        $base = $path[0] == '/' ? $root : $current;

        if ($path[0] == '.') {
            $path = substr($path, 1);
        }

        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}