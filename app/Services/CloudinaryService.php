<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

class CloudinaryService
{
    /**
     * Get Cloudinary upload signature data for direct client-side browser uploads.
     * Bypasses serverless payload size limits (e.g., Vercel's 4.5MB limit).
     */
    public function getSignature(string $folder = 'apvisuals'): array
    {
        $cloudinaryUrl = config('services.cloudinary.url')
            ?: (env('CLOUDINARY_URL')
            ?: (getenv('CLOUDINARY_URL')
            ?: ($_SERVER['CLOUDINARY_URL']
            ?: ($_ENV['CLOUDINARY_URL']
            ?: 'cloudinary://734915755182871:IEJROZnMx30vNa21EYcOSdyF6XE@ttyvzu53'))));

        $parsed    = parse_url($cloudinaryUrl);
        $apiKey    = $parsed['user'] ?? '734915755182871';
        $apiSecret = $parsed['pass'] ?? 'IEJROZnMx30vNa21EYcOSdyF6XE';
        $cloudName = $parsed['host'] ?? 'ttyvzu53';

        $timestamp    = time();
        $paramsToSign = [
            'folder'    => $folder,
            'timestamp' => $timestamp,
        ];
        ksort($paramsToSign);

        $toSign = [];
        foreach ($paramsToSign as $k => $v) {
            $toSign[] = "{$k}={$v}";
        }
        $stringToSign = implode('&', $toSign) . $apiSecret;
        $signature    = sha1($stringToSign);

        return [
            'cloud_name' => $cloudName,
            'api_key'    => $apiKey,
            'timestamp'  => $timestamp,
            'signature'  => $signature,
            'folder'     => $folder,
        ];
    }

    /**
     * Upload a file (image or video) to Cloudinary or fallback to local public/images directory.
     *
     * @param UploadedFile $file
     * @param string $folder Subfolder inside public/images or Cloudinary (e.g., '', 'skills')
     * @param string|null $customFilename Custom filename for local fallback
     * @return string Returns full Cloudinary HTTPS URL or relative local path (e.g., 'filename.jpg' or 'skills/filename.jpg')
     */
    public function upload(UploadedFile $file, string $folder = '', ?string $customFilename = null): string
    {
        $cloudinaryUrl = config('services.cloudinary.url')
            ?: (env('CLOUDINARY_URL')
            ?: (getenv('CLOUDINARY_URL')
            ?: ($_SERVER['CLOUDINARY_URL']
            ?: ($_ENV['CLOUDINARY_URL']
            ?: 'cloudinary://734915755182871:IEJROZnMx30vNa21EYcOSdyF6XE@ttyvzu53'))));

        $isVercel = (bool) (getenv('VERCEL') || isset($_SERVER['VERCEL']));

        // Check if Cloudinary URL is configured and valid (does not contain default placeholders)
        if (!empty($cloudinaryUrl) &&
            str_contains($cloudinaryUrl, 'cloudinary://') &&
            !str_contains($cloudinaryUrl, 'YOUR_API_SECRET') &&
            !str_contains($cloudinaryUrl, '**********')) {
            try {
                $cloudinary = new Cloudinary($cloudinaryUrl);

                $mimeType  = $file->getMimeType() ?? '';
                $extension = strtolower($file->getClientOriginalExtension());
                $isVideo   = str_contains($mimeType, 'video') || in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv', 'wmv', 'm4v']);

                $cloudinaryFolder = 'apvisuals' . ($folder ? '/' . trim($folder, '/') : '');

                $options = [
                    'folder'        => $cloudinaryFolder,
                    'resource_type' => $isVideo ? 'video' : 'auto',
                ];

                if ($isVideo) {
                    $options['chunk_size'] = 6000000; // 6MB chunks for videos
                }

                $filePath = $file->getRealPath() ?: $file->getPathname();

                $uploadResult = $cloudinary->uploadApi()->upload($filePath, $options);

                if (isset($uploadResult['secure_url'])) {
                    return $uploadResult['secure_url'];
                }

                throw new \RuntimeException('Cloudinary response does not contain secure_url');
            } catch (Throwable $e) {
                Log::error('Cloudinary upload failed: ' . $e->getMessage(), [
                    'file'  => $file->getClientOriginalName(),
                    'trace' => $e->getTraceAsString(),
                ]);

                if ($isVercel) {
                    throw new \RuntimeException('Upload ke Cloudinary gagal: ' . $e->getMessage());
                }
            }
        } elseif ($isVercel) {
            throw new \RuntimeException('Konfigurasi Cloudinary URL tidak ditemukan di server.');
        }

        // Fallback to local storage in public/images (for local dev)
        $targetDir = $folder ? public_path('images/' . trim($folder, '/')) : public_path('images');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $filename = $customFilename ?: (time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension());
        $file->move($targetDir, $filename);

        return $folder ? trim($folder, '/') . '/' . $filename : $filename;
    }

    /**
     * Upload the current SQLite database to Cloudinary raw storage.
     * Ensures all admin updates (projects, settings, skills) persist across Vercel restarts & redeploys.
     */
    public function syncDatabase(): bool
    {
        $cloudinaryUrl = config('services.cloudinary.url')
            ?: (env('CLOUDINARY_URL')
            ?: (getenv('CLOUDINARY_URL')
            ?: ($_SERVER['CLOUDINARY_URL']
            ?: ($_ENV['CLOUDINARY_URL']
            ?: 'cloudinary://734915755182871:IEJROZnMx30vNa21EYcOSdyF6XE@ttyvzu53'))));

        if (empty($cloudinaryUrl) || !str_contains($cloudinaryUrl, 'cloudinary://')) {
            return false;
        }

        try {
            $dbPath = (getenv('VERCEL') || isset($_SERVER['VERCEL']))
                ? '/tmp/database/database.sqlite'
                : (config('database.connections.sqlite.database') ?: database_path('database.sqlite'));

            if (!file_exists($dbPath) || filesize($dbPath) === 0) {
                return false;
            }

            $cloudinary = new Cloudinary($cloudinaryUrl);
            $cloudinary->uploadApi()->upload($dbPath, [
                'folder'        => 'apvisuals/db',
                'public_id'     => 'database',
                'resource_type' => 'raw',
                'overwrite'     => true,
                'invalidate'    => true,
            ]);

            Log::info('Cloudinary DB Auto-Sync successful.');
            return true;
        } catch (Throwable $e) {
            Log::error('Cloudinary DB Auto-Sync failed: ' . $e->getMessage());
            return false;
        }
    }
}
