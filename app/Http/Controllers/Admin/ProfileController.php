<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    /**
     * Display profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update admin profile info (Name, Email, Profile Picture).
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        if ($request->hasFile('profile_image')) {
            $url = $this->cloudinary->upload($request->file('profile_image'));
            Setting::updateOrCreate(['key' => 'profile_image'], ['value' => $url]);
        }

        // Auto-sync SQLite database to Cloudinary CDN for permanent persistence
        $this->cloudinary->syncDatabase();

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Profil admin berhasil diperbarui.');
    }

    /**
     * Update admin password.
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'new_password'     => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password lama tidak cocok.',
            'new_password.min'                   => 'Password baru minimal 8 karakter.',
            'new_password.confirmed'             => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        // Auto-sync SQLite database to Cloudinary CDN for permanent persistence
        $this->cloudinary->syncDatabase();

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Password admin berhasil diubah.');
    }
}
