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
                    @if($project->type === 'video' || \Illuminate\Support\Str::endsWith(strtolower($project->asset_path), ['.mp4', '.webm', '.ogg', '.mov']))
                        <video src="{{ asset_url($project->asset_path) }}" autoplay loop muted playsinline style="width:120px;height:70px;object-fit:cover;border-radius:8px;background:#000;"></video>
                    @else
                        <img src="{{ asset_url($project->asset_path) }}" alt="{{ $project->title }}" style="width:120px;height:70px;object-fit:cover;border-radius:8px;">
                    @endif
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#e2e2e2;margin:0;">{{ $project->asset_path }}</p>
                        <span style="font-size:11px;color:rgba(255,255,255,0.4);">Current File in public/images/</span>
                    </div>
                </div>
            </div>

            {{-- Replace Asset Section - Tab Mode --}}
            <div class="form-group">
                <label class="form-label">Ganti Showcase Asset <span style="font-size:11px;color:rgba(255,255,255,0.35);font-weight:400;">(opsional)</span></label>

                {{-- Tab Switcher --}}
                <div class="asset-tab-bar">
                    <button type="button" class="asset-tab active" id="tabUpload" onclick="switchAssetTab('upload')">
                        <i class="ri-upload-cloud-line"></i> Upload File
                        <span class="tab-badge">Gambar / Video ≤10MB</span>
                    </button>
                    <button type="button" class="asset-tab" id="tabUrl" onclick="switchAssetTab('url')">
                        <i class="ri-links-line"></i> Paste URL Video
                        <span class="tab-badge" style="background:rgba(251,146,60,0.15);color:#fb923c;border-color:rgba(251,146,60,0.3);">YouTube / Vimeo / Direct</span>
                    </button>
                </div>

                {{-- Panel: Upload File --}}
                <div id="panelUpload">
                    <div class="file-drop-zone" id="fileDropZone">
                        <i class="ri-upload-cloud-2-line" style="font-size:32px;color:rgba(139,92,246,0.6);margin-bottom:8px;display:block;"></i>
                        <p style="font-size:13px;color:rgba(255,255,255,0.5);margin-bottom:8px;">Pilih file baru HANYA jika ingin mengganti file yang ada</p>
                        <label for="asset_file" class="btn btn-secondary" style="cursor:pointer;display:inline-flex;margin-bottom:0;">
                            <i class="ri-folder-open-line"></i> Pilih File Baru
                        </label>
                        <input type="file" id="asset_file" name="asset_file" style="display:none;" accept="image/*,video/*">
                        <p style="margin-top:12px;font-size:12px;color:rgba(255,255,255,0.3);">
                            Gambar: JPG, PNG, WEBP &nbsp;|&nbsp; Video: MP4, WEBM, MOV &nbsp;|&nbsp; Maks: <strong style="color:rgba(255,255,255,0.5);">10 MB</strong>
                        </p>
                    </div>

                    {{-- Size warning --}}
                    <div id="fileSizeWarning" style="display:none;margin-top:10px;padding:12px 16px;background:rgba(251,146,60,0.08);border:1px solid rgba(251,146,60,0.3);border-radius:10px;">
                        <p style="margin:0;font-size:13px;color:#fb923c;font-weight:600;">
                            <i class="ri-alert-line"></i> File terlalu besar untuk di-upload langsung.
                        </p>
                        <p style="margin:4px 0 0;font-size:12px;color:rgba(255,255,255,0.5);">Cloudinary (plan gratis) hanya menerima file ≤10 MB. Gunakan tab <strong style="color:#fb923c;">"Paste URL Video"</strong> — upload dulu videomu ke YouTube/Google Drive/Vimeo, lalu paste link-nya di sana.</p>
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

                {{-- Panel: URL Video --}}
                <div id="panelUrl" style="display:none;">
                    <div style="padding:20px;background:rgba(251,146,60,0.04);border:1px solid rgba(251,146,60,0.2);border-radius:14px;">
                        <p style="font-size:13px;color:rgba(255,255,255,0.5);margin:0 0 14px;">
                            <i class="ri-information-line" style="color:#fb923c;"></i>
                            Untuk video besar (>10 MB): upload dulu ke <strong style="color:#fff;">YouTube</strong>, <strong style="color:#fff;">Vimeo</strong>, atau <strong style="color:#fff;">Google Drive</strong> (buat publik), lalu paste URL-nya di sini.
                        </p>
                        <label for="asset_url_input" class="form-label" style="font-size:12px;">URL Video Baru</label>
                        <input type="text" id="asset_url_input" class="form-control" placeholder="https://youtu.be/... atau https://drive.google.com/..." autocomplete="off">
                        <div id="urlPreviewArea" style="display:none;margin-top:14px;">
                            <iframe id="urlIframe" src="" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen
                                style="width:100%;height:200px;border-radius:10px;background:#000;"></iframe>
                            <div id="urlDirectPreview" style="display:none;">
                                <video id="urlVideoEl" controls muted style="width:100%;max-height:220px;border-radius:10px;background:#000;"></video>
                            </div>
                        </div>
                    </div>
                </div>
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
<script src="{{ asset('js/cloudinary-upload.js') }}"></script>
<script>
const CLOUDINARY_MAX_BYTES = 10 * 1024 * 1024;

function switchAssetTab(tab) {
    const panelUpload = document.getElementById('panelUpload');
    const panelUrl    = document.getElementById('panelUrl');
    const tabUpload   = document.getElementById('tabUpload');
    const tabUrl      = document.getElementById('tabUrl');
    if (tab === 'upload') {
        panelUpload.style.display = '';
        panelUrl.style.display    = 'none';
        tabUpload.classList.add('active');
        tabUrl.classList.remove('active');
    } else {
        panelUpload.style.display = 'none';
        panelUrl.style.display    = '';
        tabUpload.classList.remove('active');
        tabUrl.classList.add('active');
    }
}

function toEmbedUrl(url) {
    if (!url) return null;
    const ytMatch = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/);
    if (ytMatch) return `https://www.youtube.com/embed/${ytMatch[1]}`;
    const viMatch = url.match(/vimeo\.com\/(\d+)/);
    if (viMatch) return `https://player.vimeo.com/video/${viMatch[1]}`;
    return null;
}

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

        const sizeWarning = document.getElementById('fileSizeWarning');
        if (file.size > CLOUDINARY_MAX_BYTES) {
            sizeWarning.style.display = 'block';
            previewArea.style.display = 'none';
            return;
        }
        sizeWarning.style.display = 'none';
        previewArea.style.display = 'block';
    });

    // URL input preview
    const urlInput       = document.getElementById('asset_url_input');
    const urlPreviewArea = document.getElementById('urlPreviewArea');
    const urlIframe      = document.getElementById('urlIframe');
    const urlDirectPrev  = document.getElementById('urlDirectPreview');
    const urlVideoEl     = document.getElementById('urlVideoEl');

    let urlDebounce;
    urlInput.addEventListener('input', () => {
        clearTimeout(urlDebounce);
        urlDebounce = setTimeout(() => {
            const val = urlInput.value.trim();
            if (!val) { urlPreviewArea.style.display = 'none'; return; }
            const embed = toEmbedUrl(val);
            if (embed) {
                urlIframe.src = embed;
                urlIframe.style.display = 'block';
                urlDirectPrev.style.display = 'none';
                urlPreviewArea.style.display = 'block';
                typeSelect.value = 'video';
            } else if (val.match(/\.(mp4|webm|mov|ogg)$/i)) {
                urlVideoEl.src = val;
                urlDirectPrev.style.display = 'block';
                urlIframe.style.display = 'none';
                urlPreviewArea.style.display = 'block';
                typeSelect.value = 'video';
            } else {
                urlPreviewArea.style.display = 'none';
            }
        }, 600);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const isUrlMode = document.getElementById('panelUrl').style.display !== 'none';
        const file      = fileInput.files[0];
        const urlValue  = (urlInput ? urlInput.value.trim() : '');

        // --- URL mode ---
        if (isUrlMode) {
            if (!urlValue) {
                alert('Masukkan URL video terlebih dahulu.');
                return;
            }
            sendProjectForm(urlValue);
            return;
        }

        // --- File upload mode ---
        if (file) {
            if (file.size > CLOUDINARY_MAX_BYTES) {
                alert(`File terlalu besar (${(file.size/1024/1024).toFixed(1)} MB).\nCloudinary (plan gratis) hanya menerima file ≤10 MB.\n\nGunakan tab "Paste URL Video" — upload dulu ke YouTube/Vimeo/Google Drive, lalu paste URL-nya.`);
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ri-loader-4-line" style="display:inline-block;animation:adminSpin 1s linear infinite;"></i> Menyiapkan Upload...';
            progressWrapper.style.display = 'block';
            progressStatus.textContent = 'Mendapatkan izin upload...';
            progressBar.style.width = '3%';
            progressPercent.textContent = '0%';

            fetch('{{ route("admin.cloudinary.signature") }}')
                .then(r => r.json())
                .then(sig => {
                    progressStatus.textContent = 'Mengupload ke Cloudinary (chunked)...';
                    progressBar.style.width = '5%';

                    window.cloudinaryChunkedUpload(sig, file, {
                        onProgress: function(percent, loadedMB, totalMB) {
                            progressBar.style.width = percent + '%';
                            progressPercent.textContent = percent + '%';
                            progressStatus.textContent = `Mengupload file... ${loadedMB} MB / ${totalMB} MB`;
                        },
                        onSuccess: function(secureUrl) {
                            progressStatus.textContent = 'Upload selesai! Menyimpan perubahan...';
                            progressPercent.textContent = '100%';
                            sendProjectForm(secureUrl);
                        },
                        onError: function(errMsg) {
                            alert('Gagal mengupload ke Cloudinary:\n' + errMsg);
                            resetSubmitBtn();
                        }
                    });
                })
                .catch(err => {
                    alert('Gagal mendapatkan signature upload: ' + err.message);
                    resetSubmitBtn();
                });
        } else {
            sendProjectForm(null);
        }

        function sendProjectForm(cloudinaryUrl) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ri-loader-4-line" style="display:inline-block;animation:adminSpin 1s linear infinite;"></i> Menyimpan Perubahan...';

            const projectData = new FormData(form);
            projectData.delete('asset_file');

            if (cloudinaryUrl) {
                projectData.set('asset_path', cloudinaryUrl);
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        window.location.href = res.redirect || "{{ route('admin.projects.index') }}";
                    } catch(err) {
                        window.location.href = "{{ route('admin.projects.index') }}";
                    }
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
                    resetSubmitBtn();
                }
            };

            xhr.onerror = function() {
                alert('Koneksi terputus saat menyimpan perubahan.');
                resetSubmitBtn();
            };

            xhr.send(projectData);
        }

        function resetSubmitBtn() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ri-check-line"></i> Save Changes';
            progressWrapper.style.display = 'none';
            progressBar.style.width = '0%';
            progressPercent.textContent = '0%';
        }
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
.asset-tab-bar {
    display: flex;
    gap: 8px;
    margin-bottom: 14px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 12px;
    padding: 5px;
}
.asset-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    border: none;
    border-radius: 9px;
    background: transparent;
    color: rgba(255,255,255,0.45);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}
.asset-tab:hover { background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); }
.asset-tab.active { background: rgba(139,92,246,0.15); color: #c084fc; border: 1px solid rgba(139,92,246,0.3); }
.tab-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    background: rgba(139,92,246,0.1);
    border: 1px solid rgba(139,92,246,0.25);
    color: #a78bfa;
}
@keyframes adminSpin { to { transform: rotate(360deg); } }
</style>
@endsection
