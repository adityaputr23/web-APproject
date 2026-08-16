<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SkillController extends Controller
{
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
            $file     = $request->file('logo');
            $filename = 'skill_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/skills'), $filename);
            $validated['logo_path'] = 'skills/' . $filename;
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
            // Hapus logo lama jika ada
            if ($skill->logo_path && File::exists(public_path('images/' . $skill->logo_path))) {
                File::delete(public_path('images/' . $skill->logo_path));
            }

            $file     = $request->file('logo');
            $filename = 'skill_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/skills'), $filename);
            $validated['logo_path'] = 'skills/' . $filename;
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
        // Hapus logo jika ada
        if ($skill->logo_path && File::exists(public_path('images/' . $skill->logo_path))) {
            File::delete(public_path('images/' . $skill->logo_path));
        }

        $skill->delete();

        return redirect()->route('admin.skills.index')
            ->with('success', 'Skill deleted successfully.');
    }
}
