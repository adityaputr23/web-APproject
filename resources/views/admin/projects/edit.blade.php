@extends('layouts.admin')

@section('title', 'Edit Project | APVISUALS Admin')

@section('content')
    <div class="content-header">
        <div class="header-title-group">
            <h1 class="page-title">Edit Project: {{ $project->title }}</h1>
            <p class="page-subtitle">Update details, tags, or replace the showcase image/video.</p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
            <i class="ri-arrow-left-line"></i> Back to List
        </a>
    </div>

    <div class="content-card">
        <form method="POST" action="{{ route('admin.projects.update', $project->id) }}" enctype="multipart/form-data" class="admin-form" id="projectEditForm">
            @csrf
            @method('PUT')

            <div class="form-row-2">
                <div class="form-group">
                    <label for="title" class="form-label">Project Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $project->title) }}" required>
                    @error('title') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" id="category" name="category" class="form-control" value="{{ old('category', $project->category) }}" required>
                        @error('category') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="order" class="form-label">Display Order</label>
                        <input type="number" id="order" name="order" class="form-control" value="{{ old('order', $project->order) }}" required>
                        @error('order') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label for="tags" class="form-label">Tags (comma-separated)</label>
                    <input type="text" id="tags" name="tags" class="form-control" value="{{ old('tags', $project->tags) }}" placeholder="e.g. Video, Lighting, DaVinci">
                    @error('tags') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="type" class="form-label">Media Type</label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="image" {{ old('type', $project->type) === 'image' ? 'selected' : '' }}>🖼️ Image Showcase</option>
                            <option value="video" {{ old('type', $project->type) === 'video' ? 'selected' : '' }}>🎬 Video Showcase</option>
                        </select>
                        @error('type') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="project_url" class="form-label">Project External URL</label>
                        <input type="text" id="project_url" name="project_url" class="form-control" value="{{ old('project_url', $project->project_url) }}">
                        @error('project_url') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5" required>{{ old('description', $project->description) }}</textarea>
                @error('description') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Current Media File</label>
                <div class="current-asset-preview" style="padding:14px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;display:flex;align-items:center;gap:16px;">
                    @if($project->type === 'video' || Str::endsWith(strtolower($project->asset_path), ['.mp4', '.webm', '.ogg', '.mov']))
                        <video src="{{ asset('images/' . $project->asset_path) }}" autoplay loop muted playsinline style="width:120px;height:70px;object-fit:cover;border-radius:8px;background:#000;"></video>
                    @else
                        <img src="{{ asset('images/' . $project->asset_path) }}" alt="{{ $project->title }}" style="width:120px;height:70px;object-fit:cover;border-radius:8px;">
                    @endif
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#e2e2e2;margin:0;">{{ $project->asset_path }}</p>
                        <span style="font-size:11px;color:rgba(255,255,255,0.4);">Current File in public/images/</span>
                    </div>
                </div>
            </div>

            {{-- Replace File Upload --}}
            <div class="form-group">
                <label class="form-label">Ganti Showcase Asset (Opsional — tanpa kompresi)</label>
                
                <div class="file-drop-zone" id="fileDropZone">
                    <i class="ri-upload-cloud-2-line" style="font-size:32px;color:rgba(139,92,246,0.6);margin-bottom:8px;display:block;"></i>
                    <p style="font-size:13px;color:rgba(255,255,255,0.5);margin-bottom:8px;">Pilih file baru HANYA jika ingin mengganti file yang ada</p>
                    <label for="asset_file" class="btn btn-secondary" style="cursor:pointer;display:inline-flex;margin-bottom:0;">
                        <i class="ri-folder-open-line"></i> Pilih File Baru
                    </label>
                    <input type="file" id="asset_file" name="asset_file" style="display:none;" accept="image/*,video/*">
                </div>

                {{-- File info after pick --}}
                <div id="filePreviewArea" style="display:none;margin-top:14px;">
                    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:rgba(139,92,246,0.06);border:1px solid rgba(139,92,246,0.2);border-radius:12px;">
                        <i id="fileTypeIcon" class="ri-file-line" style="font-size:24px;color:#c084fc;flex-shrink:0;"></i>
                        <div style="flex:1;min-width:0;">
                            <p id="fileName" style="font-size:13px;font-weight:600;color:#e2e2e2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0;"></p>
                            <p id="fileSize" style="font-size:11px;color:rgba(255,255,255,0.4);margin:2px 0 0;"></p>
                        </div>
                        <span id="fileTypeBadge" style="font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.3);color:#c084fc;text-transform:uppercase;flex-shrink:0;"></span>
                    </div>
                </div>

                {{-- Real-Time Progress Bar --}}
                <div id="uploadProgressWrapper" style="display:none;margin-top:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                        <span id="uploadProgressStatus" style="font-size:12px;font-weight:600;color:#c084fc;">Mengupload file...</span>
                        <span id="uploadProgressPercent" style="font-size:13px;font-weight:800;color:#ffffff;">0%</span>
                    </div>
                    <div style="width:100%;height:10px;background:rgba(255,255,255,0.06);border-radius:10px;overflow:hidden;border:1px solid rgba(255,255,255,0.1);">
                        <div id="uploadProgressBar" style="width:0%;height:100%;background:linear-gradient(90deg, #7c3aed, #3b82f6);border-radius:10px;transition:width 0.15s ease;"></div>
                    </div>
                </div>

                @error('asset_file') <span class="error-message" style="display:block;margin-top:8px;">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="ri-check-line"></i> Save Changes
                </button>
                <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileInput     = document.getElementById('asset_file');
    const typeSelect    = document.getElementById('type');
    const previewArea   = document.getElementById('filePreviewArea');
    const fileNameEl    = document.getElementById('fileName');
    const fileSizeEl    = document.getElementById('fileSize');
    const badgeEl       = document.getElementById('fileTypeBadge');
    const iconEl        = document.getElementById('fileTypeIcon');

    const form            = document.getElementById('projectEditForm');
    const submitBtn       = document.getElementById('submitBtn');
    const progressWrapper = document.getElementById('uploadProgressWrapper');
    const progressBar     = document.getElementById('uploadProgressBar');
    const progressPercent = document.getElementById('uploadProgressPercent');
    const progressStatus  = document.getElementById('uploadProgressStatus');

    const VIDEO_EXTS = ['mp4','webm','mov','ogg','avi','mkv','flv','wmv','m4v'];

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) return;
        const ext     = file.name.split('.').pop().toLowerCase();
        const isVideo = VIDEO_EXTS.includes(ext);
        const sizeMB  = (file.size / 1024 / 1024).toFixed(2);

        typeSelect.value       = isVideo ? 'video' : 'image';
        fileNameEl.textContent = file.name;
        fileSizeEl.textContent = sizeMB + ' MB';
        badgeEl.textContent    = ext.toUpperCase();
        iconEl.className       = isVideo ? 'ri-video-line' : 'ri-image-line';
        iconEl.style.color     = isVideo ? '#fb923c' : '#34d399';
        previewArea.style.display = 'block';
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();

        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        if (xhr.upload) {
            xhr.upload.onprogress = function(event) {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    const loadedMB = (event.loaded / 1024 / 1024).toFixed(1);
                    const totalMB = (event.total / 1024 / 1024).toFixed(1);

                    progressWrapper.style.display = 'block';
                    progressBar.style.width = percent + '%';
                    progressPercent.textContent = percent + '%';
                    progressStatus.textContent = `Mengupload file tanpa kompresi... ${loadedMB} MB / ${totalMB} MB`;
                }
            };
        }

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                progressStatus.textContent = 'Pembaruan selesai!';
                progressBar.style.width = '100%';
                window.location.href = "{{ route('admin.projects.index') }}";
            } else {
                let errMsg = 'Terjadi kesalahan saat menyimpan perubahan.';
                try {
                    const errRes = JSON.parse(xhr.responseText);
                    if (errRes.errors) {
                        errMsg = Object.values(errRes.errors).flat().join('\n');
                    } else if (errRes.message) {
                        errMsg = errRes.message;
                    }
                } catch(e) {}
                alert(errMsg);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ri-check-line"></i> Save Changes';
            }
        };

        xhr.onerror = function() {
            alert('Koneksi terputus saat mengirim data.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ri-check-line"></i> Save Changes';
        };

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ri-loader-4-line" style="display:inline-block;animation:adminSpin 1s linear infinite;"></i> Menyimpan Perubahan...';

        xhr.send(formData);
    });
});
</script>
<style>
.file-drop-zone {
    border: 2px dashed rgba(139,92,246,0.25);
    border-radius: 16px;
    padding: 30px 20px;
    text-align: center;
    transition: border-color 0.3s, background 0.3s;
    background: rgba(139,92,246,0.02);
}
.file-drop-zone:hover {
    border-color: rgba(139,92,246,0.4);
    background: rgba(139,92,246,0.05);
}
@keyframes adminSpin { to { transform: rotate(360deg); } }
</style>
@endsection
