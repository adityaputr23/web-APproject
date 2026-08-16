<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index()
    {
        $projects = Project::orderBy('order', 'asc')->get();
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'nullable|string',
            'type' => 'required|in:image,video',
            'asset_file' => 'required_without:asset_path|file|mimes:jpeg,png,jpg,gif,svg,webp,mp4,webm,ogg,mov,avi,mkv,flv,wmv,m4v|max:524288',
            'asset_path' => 'nullable|string',
            'project_url' => 'nullable|string',
            'order' => 'required|integer',
        ]);

        if ($request->hasFile('asset_file')) {
            $file = $request->file('asset_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $validated['asset_path'] = $filename;
        }

        unset($validated['asset_file']);

        $project = Project::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project created successfully.',
                'redirect' => route('admin.projects.index')
            ]);
        }

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'nullable|string',
            'type' => 'required|in:image,video',
            'asset_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,mp4,webm,ogg,mov,avi,mkv,flv,wmv,m4v|max:524288',
            'asset_path' => 'nullable|string',
            'project_url' => 'nullable|string',
            'order' => 'required|integer',
        ]);

        if ($request->hasFile('asset_file')) {
            // Delete old file if it exists and is not a seeded project asset
            $oldPath = public_path('images/' . $project->asset_path);
            if (File::exists($oldPath) && !in_array($project->asset_path, ['project1.jpg', 'project2.jpg', 'project3.jpg', 'project4.jpg'])) {
                File::delete($oldPath);
            }

            $file = $request->file('asset_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $validated['asset_path'] = $filename;
        }

        unset($validated['asset_file']);

        $project->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully.',
                'redirect' => route('admin.projects.index')
            ]);
        }

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        // Delete file if it exists and is not a default seeded project asset
        $filePath = public_path('images/' . $project->asset_path);
        if (File::exists($filePath) && !in_array($project->asset_path, ['project1.jpg', 'project2.jpg', 'project3.jpg', 'project4.jpg'])) {
            File::delete($filePath);
        }

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
