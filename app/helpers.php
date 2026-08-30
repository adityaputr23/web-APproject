<?php

if (!function_exists('asset_url')) {
    /**
     * Generate asset URL supporting both Cloudinary full URLs, external URLs, and local public/images files.
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

if (!function_exists('get_video_embed_url')) {
    /**
     * Convert YouTube/Vimeo/Google Drive URLs to clean embeddable iframe URLs.
     * Returns null if not an embeddable video URL.
     */
    function get_video_embed_url(?string $url): ?string
    {
        if (empty($url)) return null;

        // YouTube
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/i', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=1&rel=0';
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/i', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1] . '?autoplay=1';
        }

        // Google Drive
        if (preg_match('/drive\.google\.com\/(?:file\/d\/|open\?id=)([\w-]+)/i', $url, $matches)) {
            return 'https://drive.google.com/file/d/' . $matches[1] . '/preview';
        }

        return null;
    }
}

if (!function_exists('get_video_thumbnail')) {
    /**
     * Get poster image URL for YouTube if applicable.
     */
    function get_video_thumbnail(?string $url): ?string
    {
        if (empty($url)) return null;

        // YouTube thumbnail
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/i', $url, $matches)) {
            return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
        }

        return null;
    }
}

if (!function_exists('is_embed_video_url')) {
    /**
     * Check if a URL is an embed video (YouTube, Vimeo, Google Drive).
     */
    function is_embed_video_url(?string $url): bool
    {
        return !empty(get_video_embed_url($url));
    }
}
