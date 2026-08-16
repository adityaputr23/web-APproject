@extends('layouts.admin')

@section('title', 'Projects CRUD | APVISUALS Admin')

@section('content')
    <div class="content-header">
        <div class="header-title-group">
            <h1 class="page-title">Manage Showcase Projects</h1>
            <p class="page-subtitle">Add, edit, or remove projects in the portfolio grid.</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
            <i class="ri-add-line"></i> Add New Project
        </a>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">All Projects</h3>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Preview</th>
                        <th>Project Name</th>
                        <th>Category</th>
                        <th>Tags</th>
                        <th>Asset Path</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td class="col-order">
                                <span class="badge-order">{{ $project->order }}</span>
                            </td>
                            <td class="col-preview">
                                <img src="{{ asset_url($project->asset_path) }}" alt="{{ $project->title }}" class="table-preview-img">
                            </td>
                            <td>
                                <span class="table-highlight">{{ $project->title }}</span>
                                <span class="table-sub">{{ \Illuminate\Support\Str::limit($project->description, 50) }}</span>
                            </td>
                            <td>
                                <span class="category-badge">{{ $project->category }}</span>
                            </td>
                            <td>
                                <div class="table-tags">
                                    @foreach($project->tags_array as $tag)
                                        <span class="mini-tag">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="asset-code">{{ $project->asset_path }}</span>
                            </td>
                            <td class="col-actions">
                                <div class="action-buttons-group">
                                    <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn-action edit" title="Edit"><i class="ri-edit-line"></i></a>
                                    
                                    <form method="POST" action="{{ route('admin.projects.destroy', $project->id) }}" onsubmit="return confirm('Are you sure you want to delete this project?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action delete" title="Delete"><i class="ri-delete-bin-line"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-table-row">
                                <i class="ri-folder-open-line empty-icon"></i>
                                <p>No projects found. Add your first project using the button above.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
