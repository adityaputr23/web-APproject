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

    @if(session('success'))
        <div id="successAlert" style="margin-bottom:16px;padding:12px 18px;background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.3);border-radius:10px;color:#34d399;font-size:14px;font-weight:600;">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
    @else
        <div id="successAlert" style="display:none;margin-bottom:16px;padding:12px 18px;background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.3);border-radius:10px;color:#34d399;font-size:14px;font-weight:600;"></div>
    @endif

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
                <tbody id="projectsTableBody">
                    @forelse($projects as $project)
                        <tr id="project-row-{{ $project->id }}">
                            <td class="col-order">
                                <span class="badge-order">{{ $project->order }}</span>
                            </td>
                            <td class="col-preview">
                                @php
                                    $embedUrl = get_video_embed_url($project->asset_path);
                                    $ytThumb  = get_video_thumbnail($project->asset_path);
                                    $imgSrc   = $ytThumb ?: asset_url($project->asset_path);
                                @endphp
                                @if($project->type === 'video' && empty($embedUrl) && \Illuminate\Support\Str::endsWith(strtolower($project->asset_path), ['.mp4', '.webm', '.ogg', '.mov']))
                                    <video src="{{ asset_url($project->asset_path) }}" autoplay loop muted playsinline class="table-preview-img" style="object-fit:cover;"></video>
                                @else
                                    <img src="{{ $imgSrc }}" alt="{{ $project->title }}" class="table-preview-img" style="object-fit:cover;">
                                @endif
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
                                <span class="asset-code">{{ \Illuminate\Support\Str::limit($project->asset_path, 40) }}</span>
                            </td>
                            <td class="col-actions">
                                <div class="action-buttons-group">
                                    <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn-action edit" title="Edit"><i class="ri-edit-line"></i></a>

                                    {{-- JS fetch DELETE — avoids Vercel 404 from HTML form @method('DELETE') --}}
                                    <button type="button"
                                        class="btn-action delete"
                                        title="Delete"
                                        id="del-btn-{{ $project->id }}"
                                        onclick="confirmDeleteProject({{ $project->id }}, '{{ addslashes($project->title) }}', '{{ csrf_token() }}')"
                                    ><i class="ri-delete-bin-line"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
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

@section('scripts')
<script>
function confirmDeleteProject(id, title, csrfToken) {
    if (!confirm('Hapus project "' + title + '"?\nTindakan ini tidak bisa dibatalkan.')) return;

    var btn = document.getElementById('del-btn-' + id);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line" style="animation:apvSpin 1s linear infinite;display:inline-block;"></i>';
    }

    fetch('/admin/projects/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: '_token=' + encodeURIComponent(csrfToken) + '&_method=DELETE',
        redirect: 'follow'
    })
    .then(function(res) {
        if (res.status >= 200 && res.status < 400) {
            // Auto-refresh halaman agar data selalu fresh dari server
            window.location.reload();
        } else {
            alert('Gagal menghapus project. Status: ' + res.status);
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-delete-bin-line"></i>'; }
        }
    })
    .catch(function(err) {
        alert('Koneksi terputus: ' + err.message);
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-delete-bin-line"></i>'; }
    });
}
</script>
<style>
@keyframes apvSpin { to { transform: rotate(360deg); } }
</style>
@endsection
