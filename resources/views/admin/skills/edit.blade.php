@extends('layouts.admin')

@section('title', 'Edit Skill | APVISUALS Admin')

@section('content')
    <div class="content-header">
        <div class="header-title-group">
            <h1 class="page-title">Edit Skill: {{ $skill->name }}</h1>
            <p class="page-subtitle">Modify skill properties or details.</p>
        </div>
        <a href="{{ route('admin.skills.index') }}" class="btn btn-secondary">
            <i class="ri-arrow-left-line"></i> Back to List
        </a>
    </div>

    <div class="content-card">
        <form method="POST" action="{{ route('admin.skills.update', $skill->id) }}" class="admin-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-row-2">
                <div class="form-group">
                    <label for="name" class="form-label">Skill Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $skill->name) }}" required>
                    @error('name') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="category" class="form-label">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="creative" {{ old('category', $skill->category) === 'creative' ? 'selected' : '' }}>Creative</option>
                            <option value="engineering" {{ old('category', $skill->category) === 'engineering' ? 'selected' : '' }}>Engineering</option>
                        </select>
                        @error('category') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="order" class="form-label">Display Order</label>
                        <input type="number" id="order" name="order" class="form-control" value="{{ old('order', $skill->order) }}" required>
                        @error('order') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="type" class="form-label">Render Layout Type</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="card" {{ old('type', $skill->type) === 'card' ? 'selected' : '' }}>Detailed Card (Includes description)</option>
                    <option value="badge" {{ old('type', $skill->type) === 'badge' ? 'selected' : '' }}>Badge Tag (Omit description, renders as badge icon)</option>
                </select>
                @error('type') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description (Optional, only for Card layout)</label>
                <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $skill->description) }}</textarea>
                @error('description') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Logo / Icon Image <span style="color:var(--text-muted);font-weight:400;">(Optional — for Creative Cards)</span></label>
                @if($skill->logo_path)
                    <div style="margin-bottom:12px;display:flex;align-items:center;gap:12px;">
                        <img src="{{ asset('images/' . $skill->logo_path) }}" alt="Current Logo" style="width:52px;height:52px;object-fit:contain;border-radius:8px;background:rgba(255,255,255,0.05);padding:4px;border:1px solid rgba(255,255,255,0.1);">
                        <span style="font-size:12px;color:var(--text-muted);">Logo saat ini. Upload baru untuk mengganti.</span>
                    </div>
                @endif
                <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
                <p class="form-input-help">Upload logo software (PNG/WebP/SVG). Ukuran rekomendasi: 80x80px atau lebih besar.</p>
                @error('logo') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.skills.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
