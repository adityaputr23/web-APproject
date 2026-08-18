<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    /**
     * Show the settings edit form.
     */
    public function edit()
    {
        $settingsRaw = Setting::all();
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s->key] = $s->value;
        }
        return view('admin.settings.edit', compact('settings'));
    }

    /**
     * Update site settings.
     */
    public function update(Request $request)
    {
        $rules = [
            // Hero
            'hero_title'              => 'required|string|max:255',
            'hero_subtitle'           => 'required|string',
            // About
            'about_name'              => 'required|string|max:255',
            'about_role'              => 'required|string|max:255',
            'about_location'          => 'nullable|string|max:255',
            'about_experience'        => 'nullable|integer|min:0',
            'about_projects'          => 'nullable|integer|min:0',
            'about_clients'           => 'nullable|integer|min:0',
            'about_photo'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            // Philosophy / Values
            'philosophy_title'        => 'nullable|string|max:255',
            'philosophy_text'         => 'required|string',
            'philosophy_point1_title' => 'nullable|string|max:255',
            'philosophy_point1'       => 'required|string',
            'philosophy_point2_title' => 'nullable|string|max:255',
            'philosophy_point2'       => 'required|string',
            'philosophy_point3_title' => 'nullable|string|max:255',
            'philosophy_point3'       => 'required|string',
            'philosophy_quote'        => 'required|string',
            'philosophy_quote_author' => 'required|string',
            // Focus Banner
            'focus_title'             => 'required|string|max:255',
            'focus_description'       => 'required|string',
            // Socials & Contact
            'social_instagram'        => 'nullable|url',
            'social_linkedin'         => 'nullable|url',
            'social_github'           => 'nullable|url',
            'social_vimeo'            => 'nullable|url',
            'social_tiktok'           => 'nullable|url',
            'email'                   => 'required|email',
            // Profile image
            'profile_image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ];

        $validated = $request->validate($rules);

        // Handle Profile Image Upload
        if ($request->hasFile('profile_image')) {
            // Delete old local profile image if it's not a Cloudinary URL
            $oldSetting = Setting::where('key', 'profile_image')->first();
            if ($oldSetting && !str_starts_with($oldSetting->value, 'http')) {
                $oldPath = public_path('images/' . $oldSetting->value);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $url = $this->cloudinary->upload($request->file('profile_image'));
            Setting::updateOrCreate(['key' => 'profile_image'], ['value' => $url]);
        }

        // Handle About Photo Upload
        if ($request->hasFile('about_photo')) {
            // Delete old local about photo if not Cloudinary
            $oldSetting = Setting::where('key', 'about_photo')->first();
            if ($oldSetting && !str_starts_with($oldSetting->value, 'http')) {
                foreach (['jpg', 'jpeg', 'png', 'webp'] as $oldExt) {
                    $oldPath = public_path('images/about_photo.' . $oldExt);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }
            }

            $url = $this->cloudinary->upload($request->file('about_photo'));
            Setting::updateOrCreate(['key' => 'about_photo'], ['value' => $url]);
        }

        // Save other settings keys
        $skip = ['profile_image', 'about_photo'];
        foreach ($validated as $key => $value) {
            if (!in_array($key, $skip)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        // Auto-sync SQLite DB to Cloudinary CDN for permanent persistence
        $this->cloudinary->syncDatabase();

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Settings updated successfully.');
    }
}
