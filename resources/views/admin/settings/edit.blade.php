@extends('layouts.admin')

@section('title', 'Site Settings | APVISUALS Admin')

@section('content')
    <div class="content-header">
        <div class="header-title-group">
            <h1 class="page-title">General Site Settings</h1>
            <p class="page-subtitle">Configure the copy, philosophy, profile, and socials of your landing page.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.3);color:#34d399;padding:14px 20px;border-radius:12px;margin-bottom:24px;display:flex;align-items:center;gap:10px;">
            <i class="ri-checkbox-circle-line" style="font-size:18px;"></i> {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="admin-form-settings">
        @csrf

        <!-- Grid layout: Settings Sections -->
        <div class="settings-grid">
            <!-- Left Side: Copy Configurations -->
            <div class="settings-left">

                <!-- Hero & Profile Section -->
                <div class="content-card settings-section-card">
                    <h3 class="card-title settings-section-title"><i class="ri-home-4-line"></i> Hero & Profile Section</h3>

                    <div class="form-group">
                        <label for="hero_title" class="form-label">Hero Title</label>
                        <input type="text" id="hero_title" name="hero_title" class="form-control" value="{{ old('hero_title', $settings['hero_title'] ?? '') }}" required>
                        @error('hero_title') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="hero_subtitle" class="form-label">Hero Subtitle Text</label>
                        <textarea id="hero_subtitle" name="hero_subtitle" class="form-control" rows="3" required>{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}</textarea>
                        @error('hero_subtitle') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Current Profile Photo (Hero)</label>
                            <div class="current-profile-photo-preview">
                                <img src="{{ asset_url($settings['profile_image'] ?? 'profile.jpg') }}" alt="Profile photo" class="settings-profile-img">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="profile_image" class="form-label">Replace Profile Photo (Hero)</label>
                            <input type="file" id="profile_image" name="profile_image" class="form-control-file" accept="image/*">
                            <p class="form-input-help">Square portrait photo for the hero section (Max 3MB).</p>
                            @error('profile_image') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- About Me Section -->
                <div class="content-card settings-section-card">
                    <h3 class="card-title settings-section-title"><i class="ri-user-3-line"></i> About Me Section</h3>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="about_name" class="form-label">Full Name</label>
                            <input type="text" id="about_name" name="about_name" class="form-control" value="{{ old('about_name', $settings['about_name'] ?? '') }}" placeholder="Alex Rivera" required>
                            @error('about_name') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="about_location" class="form-label">Location</label>
                            <input type="text" id="about_location" name="about_location" class="form-control" value="{{ old('about_location', $settings['about_location'] ?? '') }}" placeholder="Indonesia">
                            @error('about_location') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="about_role" class="form-label">Role / Title</label>
                        <input type="text" id="about_role" name="about_role" class="form-control" value="{{ old('about_role', $settings['about_role'] ?? '') }}" placeholder="Cinematographer · Visual Director · Web Developer" required>
                        <p class="form-input-help">Separate roles with " · " (middle dot).</p>
                        @error('about_role') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <!-- About Photo -->
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Current About Photo</label>
                            <div class="current-profile-photo-preview">
                                @if(!empty($settings['about_photo']))
                                    <img src="{{ asset_url($settings['about_photo']) }}" alt="About photo" class="settings-profile-img">
                                @else
                                    <div class="settings-profile-img" style="display:flex;align-items:center;justify-content:center;background:rgba(22,34,102,0.2);border-radius:12px;color:#a0b4cc;font-size:32px;">
                                        <i class="ri-user-4-line"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="about_photo" class="form-label">Replace About Photo</label>
                            <input type="file" id="about_photo" name="about_photo" class="form-control-file" accept="image/*">
                            <p class="form-input-help">Photo shown on the About section card (Max 5MB).</p>
                            @error('about_photo') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="form-row-3">
                        <div class="form-group">
                            <label for="about_experience" class="form-label"><i class="ri-time-line"></i> Years Experience</label>
                            <input type="number" id="about_experience" name="about_experience" class="form-control" value="{{ old('about_experience', $settings['about_experience'] ?? '5') }}" min="0" max="99">
                            @error('about_experience') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="about_projects" class="form-label"><i class="ri-folder-4-line"></i> Projects Done</label>
                            <input type="number" id="about_projects" name="about_projects" class="form-control" value="{{ old('about_projects', $settings['about_projects'] ?? '50') }}" min="0">
                            @error('about_projects') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="about_clients" class="form-label"><i class="ri-group-line"></i> Happy Clients</label>
                            <input type="number" id="about_clients" name="about_clients" class="form-control" value="{{ old('about_clients', $settings['about_clients'] ?? '20') }}" min="0">
                            @error('about_clients') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Philosophy & Values Section -->
                <div class="content-card settings-section-card">
                    <h3 class="card-title settings-section-title"><i class="ri-book-open-line"></i> About Bio & Values</h3>

                    <div class="form-group">
                        <label for="philosophy_text" class="form-label">Bio / About Me Text</label>
                        <textarea id="philosophy_text" name="philosophy_text" class="form-control" rows="4" required>{{ old('philosophy_text', $settings['philosophy_text'] ?? '') }}</textarea>
                        @error('philosophy_text') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <!-- Value Card 1 -->
                    <div class="form-row-2" style="margin-top:8px;">
                        <div class="form-group">
                            <label for="philosophy_point1_title" class="form-label">Value #1 — Title</label>
                            <input type="text" id="philosophy_point1_title" name="philosophy_point1_title" class="form-control" value="{{ old('philosophy_point1_title', $settings['philosophy_point1_title'] ?? '') }}" placeholder="Visual Precision">
                            @error('philosophy_point1_title') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="philosophy_point1" class="form-label">Value #1 — Description</label>
                            <input type="text" id="philosophy_point1" name="philosophy_point1" class="form-control" value="{{ old('philosophy_point1', $settings['philosophy_point1'] ?? '') }}" required>
                            @error('philosophy_point1') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Value Card 2 -->
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="philosophy_point2_title" class="form-label">Value #2 — Title</label>
                            <input type="text" id="philosophy_point2_title" name="philosophy_point2_title" class="form-control" value="{{ old('philosophy_point2_title', $settings['philosophy_point2_title'] ?? '') }}" placeholder="Clean Code">
                            @error('philosophy_point2_title') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="philosophy_point2" class="form-label">Value #2 — Description</label>
                            <input type="text" id="philosophy_point2" name="philosophy_point2" class="form-control" value="{{ old('philosophy_point2', $settings['philosophy_point2'] ?? '') }}" required>
                            @error('philosophy_point2') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Value Card 3 -->
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="philosophy_point3_title" class="form-label">Value #3 — Title</label>
                            <input type="text" id="philosophy_point3_title" name="philosophy_point3_title" class="form-control" value="{{ old('philosophy_point3_title', $settings['philosophy_point3_title'] ?? '') }}" placeholder="Seamless Bridge">
                            @error('philosophy_point3_title') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="philosophy_point3" class="form-label">Value #3 — Description</label>
                            <input type="text" id="philosophy_point3" name="philosophy_point3" class="form-control" value="{{ old('philosophy_point3', $settings['philosophy_point3'] ?? '') }}" required>
                            @error('philosophy_point3') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Quote -->
                    <div class="form-row-2" style="margin-top:4px;">
                        <div class="form-group">
                            <label for="philosophy_quote" class="form-label">Closing Quote</label>
                            <input type="text" id="philosophy_quote" name="philosophy_quote" class="form-control" value="{{ old('philosophy_quote', $settings['philosophy_quote'] ?? '') }}" required>
                            @error('philosophy_quote') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="philosophy_quote_author" class="form-label">Quote Author</label>
                            <input type="text" id="philosophy_quote_author" name="philosophy_quote_author" class="form-control" value="{{ old('philosophy_quote_author', $settings['philosophy_quote_author'] ?? '') }}" required>
                            @error('philosophy_quote_author') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Focus Banner Section -->
                <div class="content-card settings-section-card">
                    <h3 class="card-title settings-section-title"><i class="ri-focus-3-line"></i> Focus Banner (Toolkit Section)</h3>

                    <div class="form-group">
                        <label for="focus_title" class="form-label">Focus Heading</label>
                        <input type="text" id="focus_title" name="focus_title" class="form-control" value="{{ old('focus_title', $settings['focus_title'] ?? '') }}" required>
                        @error('focus_title') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="focus_description" class="form-label">Focus Description</label>
                        <textarea id="focus_description" name="focus_description" class="form-control" rows="3" required>{{ old('focus_description', $settings['focus_description'] ?? '') }}</textarea>
                        @error('focus_description') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>

            <!-- Right Side: Social & Contact Configurations -->
            <div class="settings-right">
                <!-- Social Channels Section -->
                <div class="content-card settings-section-card">
                    <h3 class="card-title settings-section-title"><i class="ri-share-line"></i> Social Media Profiles</h3>

                    <div class="form-group">
                        <label for="social_instagram" class="form-label"><i class="ri-instagram-line"></i> Instagram URL</label>
                        <input type="url" id="social_instagram" name="social_instagram" class="form-control" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" placeholder="https://instagram.com/username">
                        @error('social_instagram') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="social_linkedin" class="form-label"><i class="ri-linkedin-line"></i> LinkedIn URL</label>
                        <input type="url" id="social_linkedin" name="social_linkedin" class="form-control" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}" placeholder="https://linkedin.com/in/username">
                        @error('social_linkedin') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="social_github" class="form-label"><i class="ri-github-line"></i> GitHub URL</label>
                        <input type="url" id="social_github" name="social_github" class="form-control" value="{{ old('social_github', $settings['social_github'] ?? '') }}" placeholder="https://github.com/username">
                        @error('social_github') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="social_tiktok" class="form-label"><i class="ri-tiktok-line"></i> TikTok URL</label>
                        <input type="url" id="social_tiktok" name="social_tiktok" class="form-control" value="{{ old('social_tiktok', $settings['social_tiktok'] ?? '') }}" placeholder="https://tiktok.com/@username">
                        @error('social_tiktok') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Contact Info Section -->
                <div class="content-card settings-section-card">
                    <h3 class="card-title settings-section-title"><i class="ri-mail-send-line"></i> Global Contact Details</h3>

                    <div class="form-group">
                        <label for="email" class="form-label">Contact Notification Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $settings['email'] ?? '') }}" required>
                        <p class="form-input-help">Email used for CTA contacts and communications on the landing page.</p>
                        @error('email') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Save Card -->
                <div class="settings-save-card">
                    <p class="save-disclaimer">Review all changes before committing. Saving will immediately update the public portfolio website.</p>
                    <button type="submit" class="btn btn-primary btn-block"><i class="ri-save-line"></i> Save All Settings</button>
                </div>
            </div>
        </div>
    </form>
@endsection
