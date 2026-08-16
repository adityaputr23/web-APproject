@extends('layouts.admin')

@section('title', 'New Skill | APVISUALS Admin')

@section('content')
    <div class="content-header">
        <div class="header-title-group">
            <h1 class="page-title">Add New Skill</h1>
            <p class="page-subtitle">Configure a new capability to show on the portfolio page.</p>
        </div>
        <a href="{{ route('admin.skills.index') }}" class="btn btn-secondary">
            <i class="ri-arrow-left-line"></i> Back to List
        </a>
    </div>

    <div class="content-card">
        <form method="POST" action="{{ route('admin.skills.store') }}" class="admin-form" enctype="multipart/form-data">
            @csrf

            <div class="form-row-2">
                <div class="form-group">
                    <label for="name" class="form-label">Skill Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. DaVinci Resolve" required>
                    @error('name') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="category" class="form-label">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="creative" {{ old('category') === 'creative' ? 'selected' : '' }}>Creative</option>
                            <option value="engineering" {{ old('category') === 'engineering' ? 'selected' : '' }}>Engineering</option>
                        </select>
                        @error('category') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="order" class="form-label">Display Order</label>
                        <input type="number" id="order" name="order" class="form-control" value="{{ old('order', 0) }}" required>
                        @error('order') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="type" class="form-label">Render Layout Type</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="card" {{ old('type') === 'card' ? 'selected' : '' }}>Detailed Card (Includes description)</option>
                    <option value="badge" {{ old('type') === 'badge' ? 'selected' : '' }}>Badge Tag (Omit description, renders as badge icon)</option>
                </select>
                <p class="form-input-help">Creative skills are typically rendered as Cards. Engineering skills are typically rendered as Badges.</p>
                @error('type') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description (Optional, only for Card layout)</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Brief summary of your expert credentials, specific tooling, and workflow applications..."></textarea>
                @error('description') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="logo" class="form-label">Logo / Icon Image <span style="color:var(--text-muted);font-weight:400;">(Optional — for Creative Cards)</span></label>
                <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
                <p class="form-input-help">Upload logo software (PNG/WebP/SVG). Ukuran rekomendasi: 80x80px atau lebih besar.</p>
                @error('logo') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Skill</button>
                <a href="{{ route('admin.skills.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
