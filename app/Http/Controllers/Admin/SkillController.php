<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SkillController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    /**
     * Display a listing of the skills.
     */
    public function index()
    {
        $skills = Skill::orderBy('category')
            ->orderBy('order', 'asc')
            ->get();
        return view('admin.skills.index', compact('skills'));
    }

    /**
     * Show the form for creating a new skill.
     */
    public function create()
    {
        return view('admin.skills.create');
    }

    /**
     * Store a newly created skill in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|in:creative,engineering',
            'type'        => 'required|in:card,badge',
            'description' => 'nullable|string',
            'order'       => 'required|integer',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $this->cloudinary->upload($request->file('logo'), 'skills');
        }

        unset($validated['logo']);
        Skill::create($validated);

        return redirect()->route('admin.skills.index')
            ->with('success', 'Skill created successfully.');
    }

    /**
     * Show the form for editing the specified skill.
     */
    public function edit(Skill $skill)
    {
        return view('admin.skills.edit', compact('skill'));
    }

    /**
     * Update the specified skill in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|in:creative,engineering',
            'type'        => 'required|in:card,badge',
            'description' => 'nullable|string',
            'order'       => 'required|integer',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old local logo only if it is a local file (not Cloudinary URL)
            if ($skill->logo_path && !str_starts_with($skill->logo_path, 'http')) {
                $localPath = public_path('images/' . $skill->logo_path);
                if (File::exists($localPath)) {
                    File::delete($localPath);
                }
            }

            $validated['logo_path'] = $this->cloudinary->upload($request->file('logo'), 'skills');
        }

        unset($validated['logo']);
        $skill->update($validated);

        return redirect()->route('admin.skills.index')
            ->with('success', 'Skill updated successfully.');
    }

    /**
     * Remove the specified skill from storage.
     */
    public function destroy(Skill $skill)
    {
        // Delete local logo only
        if ($skill->logo_path && !str_starts_with($skill->logo_path, 'http')) {
            $localPath = public_path('images/' . $skill->logo_path);
            if (File::exists($localPath)) {
                File::delete($localPath);
            }
        }

        $skill->delete();

        return redirect()->route('admin.skills.index')
            ->with('success', 'Skill deleted successfully.');
    }
}
