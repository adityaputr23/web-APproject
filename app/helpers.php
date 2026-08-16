<?php

if (!function_exists('asset_url')) {
    /**
     * Generate asset URL supporting both Cloudinary full URLs and local public/images files.
     *
     * @param string|null $path
     * @param string $defaultFallback
     * @return string
     */
    function asset_url(?string $path, string $defaultFallback = ''): string
    {
        if (empty($path)) {
            if (empty($defaultFallback)) {
                return '';
            }
            return (str_starts_with($defaultFallback, 'http://') || str_starts_with($defaultFallback, 'https://'))
                ? $defaultFallback
                : asset('images/' . ltrim($defaultFallback, '/'));
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('images/' . ltrim($path, '/'));
    }
}
