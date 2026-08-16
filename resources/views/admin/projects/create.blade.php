@extends('layouts.admin')

@section('title', 'New Project | APVISUALS Admin')

@section('content')
    <div class="content-header">
        <div class="header-title-group">
            <h1 class="page-title">Add New Showcase Project</h1>
            <p class="page-subtitle">Insert a new piece of work into the portfolio showcase grid.</p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
            <i class="ri-arrow-left-line"></i> Back to List
        </a>
    </div>

    <div class="content-card">
        <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data" class="admin-form" id="projectForm">
            @csrf

            <div class="form-row-2">
                <div class="form-group">
                    <label for="title" class="form-label">Project Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. Video Detailing" required>
                    @error('title') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" id="category" name="category" class="form-control" value="{{ old('category') }}" placeholder="e.g. Cinematography" required>
                        @error('category') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="order" class="form-label">Display Order</label>
                        <input type="number" id="order" name="order" class="form-control" value="{{ old('order', 0) }}" required>
                        @error('order') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label for="tags" class="form-label">Tags (comma-separated)</label>
                    <input type="text" id="tags" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="e.g. Video, Lighting, DaVinci">
                    @error('tags') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="type" class="form-label">
                            Media Type
                            <span style="color:#c084fc;font-size:10px;font-weight:500;margin-left:6px;">(otomatis terdeteksi)</span>
                        </label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="image" {{ old('type') === 'image' ? 'selected' : '' }}>🖼️ Image Showcase</option>
                            <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>🎬 Video Showcase</option>
                        </select>
                        @error('type') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="project_url" class="form-label">Project External URL</label>
                        <input type="text" id="project_url" name="project_url" class="form-control" value="{{ old('project_url') }}" placeholder="e.g. Live link or Video URL">
                        @error('project_url') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Ceritakan detail proyek ini..." required>{{ old('description') }}</textarea>
                @error('description') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            {{-- Upload Field --}}
            <div class="form-group">
                <label class="form-label">Upload Showcase Asset (Gambar atau Video Asli)</label>

                <div class="file-drop-zone" id="fileDropZone">
                    <i class="ri-upload-cloud-2-line" style="font-size:36px;color:rgba(139,92,246,0.6);margin-bottom:12px;display:block;"></i>
                    <p style="font-size:14px;color:rgba(255,255,255,0.5);margin-bottom:12px;">Drag &amp; drop file di sini, atau</p>
                    <label for="asset_file" class="btn btn-secondary" style="cursor:pointer;display:inline-flex;margin-bottom:0;">
                        <i class="ri-folder-open-line"></i> Pilih File
                    </label>
                    <input type="file" id="asset_file" name="asset_file" style="display:none;" accept="image/*,video/*">
                    <p style="margin-top:14px;font-size:12px;color:rgba(255,255,255,0.3);">
                        Tanpa kompresi &nbsp;|&nbsp; Gambar: JPG, PNG, WEBP &nbsp;|&nbsp; Video: MP4, WEBM, MOV, AVI, MKV &nbsp;|&nbsp; Maks: <strong style="color:rgba(255,255,255,0.5);">512MB</strong>
                    </p>
                </div>

                {{-- File info after pick --}}
                <div id="filePreviewArea" style="display:none;margin-top:14px;">
                    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:rgba(139,92,246,0.06);border:1px solid rgba(139,92,246,0.2);border-radius:12px;">
                        <i id="fileTypeIcon" class="ri-file-line" style="font-size:26px;color:#c084fc;flex-shrink:0;"></i>
                        <div style="flex:1;min-width:0;">
                            <p id="fileName" style="font-size:13px;font-weight:600;color:#e2e2e2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0;"></p>
                            <p id="fileSize" style="font-size:11px;color:rgba(255,255,255,0.4);margin:2px 0 0;"></p>
                        </div>
                        <span id="fileTypeBadge" style="font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.3);color:#c084fc;text-transform:uppercase;flex-shrink:0;"></span>
                    </div>
                    <video id="videoPreview" controls muted style="display:none;width:100%;max-height:260px;border-radius:12px;margin-top:12px;background:#000;"></video>
                    <img id="imgPreview" src="" alt="" style="display:none;width:100%;max-height:220px;object-fit:contain;border-radius:12px;margin-top:12px;background:rgba(0,0,0,0.3);">
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
                    <i class="ri-check-line"></i> Create Project
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
    const dropZone      = document.getElementById('fileDropZone');
    const previewArea   = document.getElementById('filePreviewArea');
    const fileNameEl    = document.getElementById('fileName');
    const fileSizeEl    = document.getElementById('fileSize');
    const badgeEl       = document.getElementById('fileTypeBadge');
    const iconEl        = document.getElementById('fileTypeIcon');
    const videoPreview  = document.getElementById('videoPreview');
    const imgPreview    = document.getElementById('imgPreview');

    const form            = document.getElementById('projectForm');
    const submitBtn       = document.getElementById('submitBtn');
    const progressWrapper = document.getElementById('uploadProgressWrapper');
    const progressBar     = document.getElementById('uploadProgressBar');
    const progressPercent = document.getElementById('uploadProgressPercent');
    const progressStatus  = document.getElementById('uploadProgressStatus');

    const VIDEO_EXTS = ['mp4','webm','mov','ogg','avi','mkv','flv','wmv','m4v'];

    function handleFile(file) {
        if (!file) return;
        const ext      = file.name.split('.').pop().toLowerCase();
        const isVideo  = VIDEO_EXTS.includes(ext);
        const sizeMB   = (file.size / 1024 / 1024).toFixed(2);

        // Auto-switch Media Type dropdown
        typeSelect.value = isVideo ? 'video' : 'image';

        // Show file info
        fileNameEl.textContent = file.name;
        fileSizeEl.textContent = sizeMB + ' MB';
        badgeEl.textContent    = ext.toUpperCase();
        iconEl.className       = isVideo ? 'ri-video-line' : 'ri-image-line';
        iconEl.style.color     = isVideo ? '#fb923c' : '#34d399';
        previewArea.style.display = 'block';

        // Preview
        const objectUrl = URL.createObjectURL(file);
        if (isVideo) {
            videoPreview.src = objectUrl;
            videoPreview.style.display = 'block';
            imgPreview.style.display   = 'none';
        } else {
            imgPreview.src  = objectUrl;
            imgPreview.style.display  = 'block';
            videoPreview.style.display = 'none';
            videoPreview.src = '';
        }
    }

    fileInput.addEventListener('change', () => {
        if (fileInput.files[0]) handleFile(fileInput.files[0]);
    });

    // Drag & drop
    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.style.borderColor  = 'rgba(139,92,246,0.7)';
        dropZone.style.background   = 'rgba(139,92,246,0.08)';
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.style.borderColor = '';
        dropZone.style.background  = '';
    });
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.style.borderColor = '';
        dropZone.style.background  = '';
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            handleFile(file);
        }
    });

    // High-speed AJAX XHR Upload with Real-Time Progress Bar
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();

        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        // Track upload progress in real-time
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
                progressStatus.textContent = 'Upload selesai! Menyimpan project...';
                progressBar.style.width = '100%';
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.redirect) {
                        window.location.href = res.redirect;
                    } else {
                        window.location.href = "{{ route('admin.projects.index') }}";
                    }
                } catch(err) {
                    window.location.href = "{{ route('admin.projects.index') }}";
                }
            } else {
                let errMsg = 'Terjadi kesalahan saat mengupload file.';
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
                submitBtn.innerHTML = '<i class="ri-check-line"></i> Create Project';
            }
        };

        xhr.onerror = function() {
            alert('Koneksi terputus saat mengupload file.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ri-check-line"></i> Create Project';
        };

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ri-loader-4-line" style="display:inline-block;animation:adminSpin 1s linear infinite;"></i> Mengirim File...';

        xhr.send(formData);
    });
});
</script>
<style>
.file-drop-zone {
    border: 2px dashed rgba(139,92,246,0.25);
    border-radius: 16px;
    padding: 40px 24px;
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
