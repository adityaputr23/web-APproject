<?php

if (!function_exists('get_google_drive_id')) {
    /**
     * Extract Google Drive file ID from various Drive URL formats.
     */
    function get_google_drive_id(?string $url): ?string
    {
        if (empty($url)) return null;
        if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?(?:.*&)?id=)|drive\.usercontent\.google\.com\/download\?id=)([\w-]+)/i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}

if (!function_exists('get_google_drive_stream_url')) {
    /**
     * Get direct streamable MP4 URL for Google Drive video.
     */
    function get_google_drive_stream_url(?string $url): ?string
    {
        $id = get_google_drive_id($url);
        return $id ? "https://drive.usercontent.google.com/download?id={$id}&confirm=t" : null;
    }
}

if (!function_exists('asset_url')) {
    /**
     * Generate asset URL supporting both Cloudinary full URLs, external URLs, Google Drive, and local public/images files.
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
            // Google Drive link -> convert to direct CDN image thumbnail for <img> tags
            $driveId = get_google_drive_id($path);
            if ($driveId) {
                return 'https://lh3.googleusercontent.com/d/' . $driveId . '=w800';
            }

            // YouTube link -> convert to video thumbnail URL
            if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/i', $path, $matches)) {
                return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
            }

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

        // Google Drive preview URL (for iframe)
        $driveId = get_google_drive_id($url);
        if ($driveId) {
            return 'https://drive.google.com/file/d/' . $driveId . '/preview';
        }

        return null;
    }
}

if (!function_exists('get_video_thumbnail')) {
    /**
     * Get poster/thumbnail image URL for YouTube or Google Drive video.
     */
    function get_video_thumbnail(?string $url): ?string
    {
        if (empty($url)) return null;

        // YouTube thumbnail
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/i', $url, $matches)) {
            return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
        }

        // Google Drive direct CDN thumbnail (fast, unauthenticated, CORS enabled)
        $driveId = get_google_drive_id($url);
        if ($driveId) {
            return 'https://lh3.googleusercontent.com/d/' . $driveId . '=w800';
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
        return !empty(get_video_embed_url($url)) || !empty(get_google_drive_id($url));
    }
}
