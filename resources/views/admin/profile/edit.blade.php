@extends('layouts.admin')

@section('title', 'Admin Profile & Security | APVISUALS Admin')

@section('content')
    <div class="content-header">
        <div class="header-title-group">
            <h1 class="page-title">Admin Profile & Security</h1>
            <p class="page-subtitle">Kelola informasi akun admin, foto profil, dan kata sandi keamanan Anda.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.3);color:#34d399;padding:14px 20px;border-radius:12px;margin-bottom:24px;display:flex;align-items:center;gap:10px;">
            <i class="ri-checkbox-circle-line" style="font-size:18px;"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error" style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#f87171;padding:14px 20px;border-radius:12px;margin-bottom:24px;">
            <ul style="margin:0;padding-left:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="settings-grid">
        <!-- Left Side: Profile Details -->
        <div class="settings-left">
            <div class="content-card settings-section-card">
                <h3 class="card-title settings-section-title"><i class="ri-user-settings-line"></i> Informasi Akun Admin</h3>

                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-row-2" style="align-items:center;margin-bottom:20px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Foto Profil Saat Ini</label>
                            @php $profileImg = \App\Models\Setting::getValue('profile_image', 'profile.jpg'); @endphp
                            <div class="current-profile-photo-preview">
                                <img src="{{ asset_url($profileImg) }}" alt="Foto Profile Admin" class="settings-profile-img" style="width:84px;height:84px;border-radius:50%;object-fit:cover;border:2px solid rgba(139,92,246,0.4);">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label for="profile_image" class="form-label">Upload Foto Profil Baru</label>
                            <input type="file" id="profile_image" name="profile_image" class="form-control-file" accept="image/*">
                            <p class="form-input-help">Format: JPG, PNG, WEBP. Maksimal 5 MB.</p>
                            @error('profile_image') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label"><i class="ri-user-line"></i> Nama Lengkap Admin</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        @error('name') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label"><i class="ri-mail-line"></i> Alamat Email Admin</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        <p class="form-input-help">Email ini digunakan untuk login ke Admin Panel.</p>
                        @error('email') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div style="margin-top:24px;text-align:right;">
                        <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Simpan Perubahan Profil</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Side: Change Password -->
        <div class="settings-right">
            <div class="content-card settings-section-card">
                <h3 class="card-title settings-section-title"><i class="ri-lock-password-line"></i> Keamanan & Ganti Password</h3>

                <form method="POST" action="{{ route('admin.profile.password') }}">
                    @csrf

                    <div class="form-group">
                        <label for="current_password" class="form-label"><i class="ri-key-2-line"></i> Password Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" placeholder="••••••••" required>
                        @error('current_password') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="form-label"><i class="ri-lock-unlock-line"></i> Password Baru</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Minimal 8 karakter" required>
                        @error('new_password') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation" class="form-label"><i class="ri-checkbox-circle-line"></i> Konfirmasi Password Baru</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" placeholder="Ulangi password baru" required>
                    </div>

                    <div style="margin-top:24px;">
                        <button type="submit" class="btn btn-primary btn-block"><i class="ri-shield-keyhole-line"></i> Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
