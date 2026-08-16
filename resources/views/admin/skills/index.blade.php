@extends('layouts.admin')

@section('title', 'Skills CRUD | APVISUALS Admin')

@section('content')
    <div class="content-header">
        <div class="header-title-group">
            <h1 class="page-title">Manage Skills & Toolkit</h1>
            <p class="page-subtitle">Configure the items displayed in your creative and engineering toolkits.</p>
        </div>
        <a href="{{ route('admin.skills.create') }}" class="btn btn-primary">
            <i class="ri-add-line"></i> Add New Skill
        </a>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">Toolkit Matrix</h3>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Skill Name</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Description (for Cards)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skills as $skill)
                        <tr>
                            <td class="col-order">
                                <span class="badge-order">{{ $skill->order }}</span>
                            </td>
                            <td>
                                <span class="table-highlight">{{ $skill->name }}</span>
                            </td>
                            <td>
                                <span class="category-badge {{ $skill->category === 'creative' ? 'badge-creative' : 'badge-engineering' }}">
                                    {{ ucfirst($skill->category) }}
                                </span>
                            </td>
                            <td>
                                <span class="type-badge {{ $skill->type === 'card' ? 'badge-card' : 'badge-tag' }}">
                                    {{ $skill->type === 'card' ? 'Detailed Card' : 'Badge Tag' }}
                                </span>
                            </td>
                            <td>
                                <span class="table-sub">{{ $skill->description ?? '— (Badge tags omit description)' }}</span>
                            </td>
                            <td class="col-actions">
                                <div class="action-buttons-group">
                                    <a href="{{ route('admin.skills.edit', $skill->id) }}" class="btn-action edit" title="Edit"><i class="ri-edit-line"></i></a>
                                    
                                    <form method="POST" action="{{ route('admin.skills.destroy', $skill->id) }}" onsubmit="return confirm('Are you sure you want to delete this skill?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action delete" title="Delete"><i class="ri-delete-bin-line"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-table-row">
                                <i class="ri-tools-line empty-icon"></i>
                                <p>No skills found. Create your first skill item using the button above.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
