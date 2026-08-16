<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Setting;
use App\Models\Skill;
use App\Services\CloudinaryService;
use Cloudinary\Cloudinary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateMediaToCloudinary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:migrate-cloudinary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all existing local photo and video assets to Cloudinary and update database paths';

    /**
     * Execute the console command.
     */
    public function handle(CloudinaryService $cloudinaryService)
    {
        $cloudinaryUrl = env('CLOUDINARY_URL');
        if (empty($cloudinaryUrl) || str_contains($cloudinaryUrl, 'YOUR_API_SECRET')) {
            $this->error('CLOUDINARY_URL belum dikonfigurasi dengan benar di file .env');
            return 1;
        }

        $cloudinary = new Cloudinary($cloudinaryUrl);

        $this->info('Starting media migration to Cloudinary...');
        $this->newLine();

        // 1. Projects
        $projects = Project::all();
        $this->info("Checking " . $projects->count() . " Projects...");
        foreach ($projects as $project) {
            $path = $project->asset_path;
            if ($path && !str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
                $localFilePath = public_path('images/' . ltrim($path, '/'));
                if (File::exists($localFilePath)) {
                    $this->output->write("Uploading project '{$project->title}' ({$path})... ");
                    try {
                        $extension = strtolower(pathinfo($localFilePath, PATHINFO_EXTENSION));
                        $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv', 'wmv', 'm4v']);

                        $options = [
                            'folder'        => 'apvisuals/projects',
                            'resource_type' => $isVideo ? 'video' : 'auto',
                        ];

                        if (filesize($localFilePath) > 90 * 1024 * 1024) {
                            $res = $cloudinary->uploadApi()->uploadLarge($localFilePath, $options);
                        } else {
                            $res = $cloudinary->uploadApi()->upload($localFilePath, $options);
                        }

                        if (isset($res['secure_url'])) {
                            $project->update(['asset_path' => $res['secure_url']]);
                            $this->info("SUCCESS -> " . $res['secure_url']);
                        } else {
                            $this->error("FAILED");
                        }
                    } catch (\Throwable $e) {
                        $this->error("ERROR: " . $e->getMessage());
                    }
                } else {
                    $this->warn("File lokal tidak ditemukan: {$localFilePath}");
                }
            } else {
                $this->line("Project '{$project->title}' sudah menggunakan Cloudinary/URL external.");
            }
        }

        $this->newLine();

        // 2. Skills
        $skills = Skill::whereNotNull('logo_path')->get();
        $this->info("Checking " . $skills->count() . " Skills...");
        foreach ($skills as $skill) {
            $path = $skill->logo_path;
            if ($path && !str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
                $localFilePath = public_path('images/' . ltrim($path, '/'));
                if (File::exists($localFilePath)) {
                    $this->output->write("Uploading skill logo '{$skill->name}' ({$path})... ");
                    try {
                        $res = $cloudinary->uploadApi()->upload($localFilePath, [
                            'folder'        => 'apvisuals/skills',
                            'resource_type' => 'image',
                        ]);

                        if (isset($res['secure_url'])) {
                            $skill->update(['logo_path' => $res['secure_url']]);
                            $this->info("SUCCESS -> " . $res['secure_url']);
                        } else {
                            $this->error("FAILED");
                        }
                    } catch (\Throwable $e) {
                        $this->error("ERROR: " . $e->getMessage());
                    }
                } else {
                    $this->warn("File logo tidak ditemukan: {$localFilePath}");
                }
            } else {
                $this->line("Skill '{$skill->name}' sudah menggunakan Cloudinary/URL external.");
            }
        }

        $this->newLine();

        // 3. Settings (profile_image, about_photo)
        $settingKeys = ['profile_image', 'about_photo'];
        $this->info("Checking Site Settings (" . implode(', ', $settingKeys) . ")...");
        foreach ($settingKeys as $key) {
            $setting = Setting::where('key', $key)->first();
            if ($setting && $setting->value) {
                $path = $setting->value;
                if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
                    $localFilePath = public_path('images/' . ltrim($path, '/'));
                    if (File::exists($localFilePath)) {
                        $this->output->write("Uploading setting '{$key}' ({$path})... ");
                        try {
                            $res = $cloudinary->uploadApi()->upload($localFilePath, [
                                'folder'        => 'apvisuals/settings',
                                'resource_type' => 'image',
                            ]);

                            if (isset($res['secure_url'])) {
                                $setting->update(['value' => $res['secure_url']]);
                                $this->info("SUCCESS -> " . $res['secure_url']);
                            } else {
                                $this->error("FAILED");
                            }
                        } catch (\Throwable $e) {
                            $this->error("ERROR: " . $e->getMessage());
                        }
                    } else {
                        $this->warn("File setting tidak ditemukan: {$localFilePath}");
                    }
                } else {
                    $this->line("Setting '{$key}' sudah menggunakan Cloudinary/URL external.");
                }
            }
        }

        $this->newLine();
        $this->info("Media migration completed!");
        return 0;
    }
}
