@extends('layouts.layout')

@section('title', 'APVISUALS | Merging Pixel with Logic')

@section('content')
    <!-- Navbar -->
    <header class="navbar">
        <div class="nav-container">
            <a href="#" class="logo">APVISUALS</a>
            <nav class="nav-menu">
                <a href="#home" class="nav-link active">Home</a>
                <a href="#about" class="nav-link">About</a>
                <a href="#toolkit" class="nav-link">Toolkit</a>
                <a href="#showcase" class="nav-link">Media Showcase</a>
                <a href="#dev-projects" class="nav-link">Web & App</a>
                <a href="#contact" class="nav-link btn-nav-mobile">Hire Me</a>
            </nav>
            <a href="#contact" class="btn-nav">Hire Me</a>
            <button class="mobile-toggle" aria-label="Toggle Navigation"><i class="ri-menu-line"></i></button>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container hero-container">
            <div class="hero-content">
                <h1 class="hero-title">
                    {{ $settings['hero_title'] ?? 'Merging Pixel with Logic.' }}
                </h1>
                <p class="hero-subtitle">
                    {{ $settings['hero_subtitle'] ?? 'A multidisciplinary creator specializing in high-end photography, cinematic video editing, and front-end web development. I craft digital experiences that are as functional as they are beautiful.' }}
                </p>
                <div class="hero-actions">
                    <a href="#showcase" class="btn btn-primary">View Showcase</a>
                    <a href="#about" class="btn btn-secondary">About Me</a>
                </div>
            </div>
            
            <div class="hero-image-wrapper">
                <div class="hero-image-card">
                    <img src="{{ asset_url($settings['profile_image'] ?? 'profile.jpg') }}" alt="Creative Director" class="hero-img">
                    <div class="glass-overlay"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="about-layout">

                <!-- Left: Photo Card -->
                <div class="about-photo-col">
                    <div class="about-photo-card">
                        @if(!empty($settings['about_photo']))
                            <img src="{{ asset_url($settings['about_photo']) }}" alt="{{ $settings['about_name'] ?? 'About Photo' }}" class="about-photo">
                        @else
                            <div class="about-photo-placeholder">
                                <i class="ri-user-4-line"></i>
                            </div>
                        @endif
                        <div class="about-photo-badge">
                            <i class="ri-map-pin-2-line"></i>
                            {{ $settings['about_location'] ?? 'Indonesia' }}
                        </div>
                    </div>

                    <div class="about-stats">
                        <div class="about-stat">
                            <span class="about-stat-num">{{ $settings['about_experience'] ?? '5' }}+</span>
                            <span class="about-stat-label">Years Experience</span>
                        </div>
                        <div class="about-stat">
                            <span class="about-stat-num">{{ $settings['about_projects'] ?? '50' }}+</span>
                            <span class="about-stat-label">Projects Done</span>
                        </div>
                        <div class="about-stat">
                            <span class="about-stat-num">{{ $settings['about_clients'] ?? '20' }}+</span>
                            <span class="about-stat-label">Happy Clients</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Bio Content -->
                <div class="about-bio-col">
                    <h3 class="about-name">{{ $settings['about_name'] ?? 'Alex Rivera' }}</h3>
                    <p class="about-role">{{ $settings['about_role'] ?? 'Cinematographer · Visual Director · Web Developer' }}</p>

                    <p class="about-bio">
                        {{ $settings['philosophy_text'] ?? 'I believe that digital products should feel like second-nature to users. Whether it\'s a 30-second commercial or a complex SaaS platform, my approach remains the same: extreme attention-to-detail, technical rigor, and dynamic storytelling.' }}
                    </p>

                    <div class="about-values">
                        <div class="about-value-item">
                            <span class="about-value-icon"><i class="ri-camera-lens-line"></i></span>
                            <div>
                                <span class="about-value-title">{{ $settings['philosophy_point1_title'] ?? 'Visual Precision' }}</span>
                                <span class="about-value-desc">{{ $settings['philosophy_point1'] ?? 'Precision-engineered visuals for ultimate impact.' }}</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <span class="about-value-icon"><i class="ri-code-s-slash-line"></i></span>
                            <div>
                                <span class="about-value-title">{{ $settings['philosophy_point2_title'] ?? 'Clean Code' }}</span>
                                <span class="about-value-desc">{{ $settings['philosophy_point2'] ?? 'Cinematic visual sense for genuine emotion.' }}</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <span class="about-value-icon"><i class="ri-link-m"></i></span>
                            <div>
                                <span class="about-value-title">{{ $settings['philosophy_point3_title'] ?? 'Seamless Bridge' }}</span>
                                <span class="about-value-desc">{{ $settings['philosophy_point3'] ?? 'A seamless bridge between design and development.' }}</span>
                            </div>
                        </div>
                    </div>

                    <blockquote class="about-quote">
                        <span class="about-quote-mark">&ldquo;</span>
                        {{ $settings['philosophy_quote'] ?? "Quality isn't an act, it is a habit." }}
                        <cite class="about-quote-author">&mdash; {{ $settings['philosophy_quote_author'] ?? 'Aristotle' }}</cite>
                    </blockquote>
                </div>

            </div>
        </div>
    </section>

    <!-- Toolkit Section -->
    <section id="toolkit" class="toolkit-section">
        <div class="container">
            <h2 class="section-title">The Toolkit</h2>
            <p class="section-subtitle">My work lives at the intersection of technical engineering and visual storytelling. Each tool is chosen for its ability to deliver premium results.</p>
            
            <div class="toolkit-grid">
                <!-- Left: Creative & Focus Box -->
                <div class="toolkit-left">
                    <div class="toolkit-group">
                        <h3 class="toolkit-group-title"><i class="ri-palette-line"></i> Creative</h3>
                        <div class="creative-cards">
                            @foreach($creativeSkills as $skill)
                                <div class="creative-card">
                                    @if($skill->logo_path)
                                        <div class="card-logo-wrap">
                                            <img src="{{ asset_url($skill->logo_path) }}" alt="{{ $skill->name }} logo" class="card-logo-img">
                                        </div>
                                    @else
                                        <span class="card-number">0{{ $loop->iteration }}</span>
                                    @endif
                                    <h4 class="card-title">{{ $skill->name }}</h4>
                                    <p class="card-desc">{{ $skill->description }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right: Engineering & Focus Box -->
                <div class="toolkit-right">
                    <div class="toolkit-group">
                        <h3 class="toolkit-group-title"><i class="ri-code-s-slash-line"></i> Engineering</h3>
                        <div class="badges-grid">
                            @foreach($engineeringSkills as $skill)
                                <span class="engineering-badge"><i class="ri-checkbox-circle-fill"></i> {{ $skill->name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Focus Box -->
                    <div class="focus-box">
                        <h4 class="focus-title">{{ $settings['focus_title'] ?? 'FOCUS: Performance & UI' }}</h4>
                        <p class="focus-desc">{{ $settings['focus_description'] ?? 'I build highly-optimized, accessible web applications with a focus on immersive user experiences and seamless interactions.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Showcase Section -->
    <section id="showcase" class="showcase-section">
        <div class="container">
            <h2 class="section-title text-center">Creative Showcase</h2>
            <div class="section-divider"></div>

            <!-- 2-Tab Switcher: Foto / Video -->
            <div class="showcase-tab-switcher">
                <button class="showcase-tab active" data-tab="photo">
                    <i class="ri-camera-lens-line"></i> Foto
                </button>
                <button class="showcase-tab" data-tab="video">
                    <i class="ri-film-line"></i> Video
                </button>
            </div>

            <!-- Photo Tab -->
            <div class="showcase-tab-panel active" data-panel="photo">
                <div class="showcase-grid">
                    @php $photoCount = 0; @endphp
                    @foreach($projects as $project)
                        @php
                            $catLower = strtolower($project->category ?? '');
                            $titleLower = strtolower($project->title ?? '');
                            $isWebOrApp = str_contains($catLower, 'web') || str_contains($catLower, 'site') || str_contains($catLower, 'store') || str_contains($catLower, 'app') || str_contains($catLower, 'mobile') || str_contains($catLower, 'saas') || str_contains($catLower, 'system') || str_contains($titleLower, 'web') || str_contains($titleLower, 'store') || str_contains($titleLower, 'app');
                            $isVideo = $project->type === 'video' || \Illuminate\Support\Str::endsWith(strtolower($project->asset_path), ['.mp4', '.webm', '.ogg', '.mov', '.mkv', '.avi']);
                        @endphp
                        @if($photoCount < 6 && !$isVideo && !$isWebOrApp)
                            @php $photoCount++; @endphp
                            <div class="showcase-item"
                                data-id="{{ $project->id }}"
                                data-title="{{ $project->title }}"
                                data-desc="{{ $project->description }}"
                                data-category="{{ $project->category }}"
                                data-tags="{{ $project->tags }}"
                                data-type="{{ $project->type }}"
                                data-asset="{{ asset_url($project->asset_path) }}"
                                data-url="{{ $project->project_url }}">
                                <div class="showcase-media-wrapper">
                                    <img src="{{ asset_url($project->asset_path) }}" alt="{{ $project->title }}" class="showcase-img">
                                    <div class="showcase-overlay">
                                        <div class="showcase-content">
                                            <span class="showcase-category">{{ $project->category }}</span>
                                            <h3 class="showcase-title">{{ $project->title }}</h3>
                                            @if(!empty($project->description))
                                                <p class="showcase-desc">{{ $project->description }}</p>
                                            @endif
                                            <div class="showcase-tags">
                                                @foreach($project->tags_array as $tag)
                                                    <span class="mini-tag">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @if($photoCount === 0)
                        <p class="showcase-empty">Belum ada karya foto.</p>
                    @endif
                </div>
            </div>

            <!-- Video Tab -->
            <div class="showcase-tab-panel" data-panel="video">
                <div class="showcase-grid">
                    @php $videoCount = 0; @endphp
                    @foreach($projects as $project)
                        @if($videoCount < 6 && ($project->type === 'video' || \Illuminate\Support\Str::endsWith(strtolower($project->asset_path), ['.mp4', '.webm', '.ogg', '.mov', '.mkv', '.avi'])))
                            @php $videoCount++; @endphp
                            <div class="showcase-item"
                                data-id="{{ $project->id }}"
                                data-title="{{ $project->title }}"
                                data-desc="{{ $project->description }}"
                                data-category="{{ $project->category }}"
                                data-tags="{{ $project->tags }}"
                                data-type="video"
                                data-asset="{{ asset_url($project->asset_path) }}"
                                data-url="{{ $project->project_url }}">
                                <div class="showcase-media-wrapper">
                                    <video src="{{ asset_url($project->asset_path) }}" autoplay loop muted playsinline class="showcase-img showcase-video"></video>
                                    <div class="showcase-overlay">
                                        <div class="showcase-content">
                                            <span class="showcase-category">{{ $project->category }}</span>
                                            <h3 class="showcase-title">{{ $project->title }}</h3>
                                            @if(!empty($project->description))
                                                <p class="showcase-desc">{{ $project->description }}</p>
                                            @endif
                                            <div class="showcase-tags">
                                                @foreach($project->tags_array as $tag)
                                                    <span class="mini-tag">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @if($videoCount === 0)
                        <p class="showcase-empty">Belum ada karya video.</p>
                    @endif
                </div>
            </div>

    <!-- Web & App Projects Section -->
    <section id="dev-projects" class="showcase-section dev-projects-section">
        <div class="container">
            <h2 class="section-title text-center">Web & App Projects</h2>
            <div class="section-divider"></div>

            <!-- 2-Tab Switcher: Website / Aplikasi -->
            <div class="showcase-tab-switcher">
                <button class="showcase-tab active" data-tab="website">
                    <i class="ri-global-line"></i> Website
                </button>
                <button class="showcase-tab" data-tab="app">
                    <i class="ri-smartphone-line"></i> Aplikasi
                </button>
            </div>

            <!-- Website Tab (Max 3 Cards) -->
            <div class="showcase-tab-panel active" data-panel="website">
                <div class="showcase-grid">
                    @php $webCount = 0; @endphp
                    @foreach($projects as $project)
                        @php
                            $catLower = strtolower($project->category ?? '');
                            $titleLower = strtolower($project->title ?? '');
                            $isWeb = str_contains($catLower, 'web') || str_contains($catLower, 'site') || str_contains($catLower, 'store') || str_contains($catLower, 'landing') || str_contains($titleLower, 'web') || str_contains($titleLower, 'store') || str_contains($titleLower, 'catering');
                        @endphp
                        @if($webCount < 3 && $isWeb)
                            @php $webCount++; @endphp
                            <div class="showcase-item"
                                data-id="{{ $project->id }}"
                                data-title="{{ $project->title }}"
                                data-desc="{{ $project->description }}"
                                data-category="{{ $project->category }}"
                                data-tags="{{ $project->tags }}"
                                data-type="{{ $project->type }}"
                                data-asset="{{ asset_url($project->asset_path) }}"
                                data-url="{{ $project->project_url }}">
                                <div class="showcase-media-wrapper">
                                    @if($project->type === 'video' || \Illuminate\Support\Str::endsWith(strtolower($project->asset_path), ['.mp4', '.webm', '.ogg', '.mov']))
                                        <video src="{{ asset_url($project->asset_path) }}" autoplay loop muted playsinline class="showcase-img showcase-video"></video>
                                    @else
                                        <img src="{{ asset_url($project->asset_path) }}" alt="{{ $project->title }}" class="showcase-img">
                                    @endif
                                    <div class="showcase-overlay">
                                        <div class="showcase-content">
                                            <span class="showcase-category">{{ $project->category }}</span>
                                            <h3 class="showcase-title">{{ $project->title }}</h3>
                                            @if(!empty($project->description))
                                                <p class="showcase-desc">{{ $project->description }}</p>
                                            @endif
                                            <div class="showcase-tags">
                                                @foreach($project->tags_array as $tag)
                                                    <span class="mini-tag">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @if($webCount === 0)
                        <p class="showcase-empty">Belum ada proyek website.</p>
                    @endif
                </div>
            </div>

            <!-- Aplikasi Tab (Max 3 Cards) -->
            <div class="showcase-tab-panel" data-panel="app">
                <div class="showcase-grid">
                    @php $appCount = 0; @endphp
                    @foreach($projects as $project)
                        @php
                            $catLower = strtolower($project->category ?? '');
                            $titleLower = strtolower($project->title ?? '');
                            $isApp = str_contains($catLower, 'app') || str_contains($catLower, 'mobile') || str_contains($catLower, 'saas') || str_contains($catLower, 'system') || str_contains($titleLower, 'app');
                        @endphp
                        @if($appCount < 3 && $isApp)
                            @php $appCount++; @endphp
                            <div class="showcase-item"
                                data-id="{{ $project->id }}"
                                data-title="{{ $project->title }}"
                                data-desc="{{ $project->description }}"
                                data-category="{{ $project->category }}"
                                data-tags="{{ $project->tags }}"
                                data-type="{{ $project->type }}"
                                data-asset="{{ asset_url($project->asset_path) }}"
                                data-url="{{ $project->project_url }}">
                                <div class="showcase-media-wrapper">
                                    @if($project->type === 'video' || \Illuminate\Support\Str::endsWith(strtolower($project->asset_path), ['.mp4', '.webm', '.ogg', '.mov']))
                                        <video src="{{ asset_url($project->asset_path) }}" autoplay loop muted playsinline class="showcase-img showcase-video"></video>
                                    @else
                                        <img src="{{ asset_url($project->asset_path) }}" alt="{{ $project->title }}" class="showcase-img">
                                    @endif
                                    <div class="showcase-overlay">
                                        <div class="showcase-content">
                                            <span class="showcase-category">{{ $project->category }}</span>
                                            <h3 class="showcase-title">{{ $project->title }}</h3>
                                            @if(!empty($project->description))
                                                <p class="showcase-desc">{{ $project->description }}</p>
                                            @endif
                                            <div class="showcase-tags">
                                                @foreach($project->tags_array as $tag)
                                                    <span class="mini-tag">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @if($appCount === 0)
                        <p class="showcase-empty">Belum ada proyek aplikasi.</p>
                    @endif
                </div>
            </div>

        </div>
    </section>



    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <div class="cta-badge">
                    <span class="cta-badge-dot"></span> Available for New Projects
                </div>
                <h2 class="cta-title">Mari Wujudkan Karya<br class="desktop-only"> Luar Biasa Bersama.</h2>
                <p class="cta-subtitle">Siap membantu kebutuhan Visual Cinematography, Fotografi Profesional, maupun Pengembangan Web & Aplikasi performa tinggi untuk brand Anda.</p>
                <div class="cta-actions">
                    <a href="#contact" class="btn btn-primary"><i class="ri-chat-1-line"></i> Mulai Konsultasi</a>
                    <a href="mailto:{{ $settings['email'] ?? 'contact@apvisuals.com' }}" class="btn btn-secondary"><i class="ri-mail-line"></i> Kirim Email</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="contact-layout">
                <!-- Left: Info Panel -->
                <div class="contact-info-panel">
                    <h2 class="contact-info-title">Mari Berkolaborasi.</h2>
                    <p class="contact-info-subtitle">Punya proyek foto, video, atau website? Ceritakan visinya — saya siap mewujudkannya menjadi karya terbaik.</p>

                    <div class="contact-info-items">
                        <div class="contact-info-item">
                            <div class="contact-info-icon"><i class="ri-mail-send-line"></i></div>
                            <div class="contact-info-text">
                                <span class="contact-info-label">Email</span>
                                <span class="contact-info-value">{{ $settings['email'] ?? 'speedvisuals03@gmail.com' }}</span>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-info-icon"><i class="ri-map-pin-2-line"></i></div>
                            <div class="contact-info-text">
                                <span class="contact-info-label">Lokasi</span>
                                <span class="contact-info-value">Indonesia 🇮🇩</span>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-info-icon"><i class="ri-time-line"></i></div>
                            <div class="contact-info-text">
                                <span class="contact-info-label">Response Time</span>
                                <span class="contact-info-value">Dalam 24 jam</span>
                            </div>
                        </div>
                    </div>

                    <div class="contact-social-row">
                        <a href="{{ $settings['social_instagram'] ?? 'https://www.instagram.com/apvisualsgallery' }}" target="_blank" class="contact-social-link" aria-label="Instagram"><i class="ri-instagram-line"></i></a>
                        <a href="{{ $settings['social_tiktok'] ?? 'https://www.tiktok.com/@ap.visuals_' }}" target="_blank" class="contact-social-link" aria-label="TikTok"><i class="ri-tiktok-line"></i></a>
                        <a href="{{ $settings['social_linkedin'] ?? '#' }}" target="_blank" class="contact-social-link" aria-label="LinkedIn"><i class="ri-linkedin-line"></i></a>
                        <a href="{{ $settings['social_github'] ?? 'https://github.com/adityaputr23' }}" target="_blank" class="contact-social-link" aria-label="GitHub"><i class="ri-github-line"></i></a>
                    </div>
                </div>

                <!-- Right: Form Card -->
                <div class="contact-card-wrapper">
                    <form id="contactForm" action="{{ route('portfolio.enquire') }}" method="POST" class="contact-form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name" class="form-label"><i class="ri-user-3-line"></i> Nama</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Nama lengkap Anda" required>
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label"><i class="ri-mail-line"></i> Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="email@contoh.com" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject" class="form-label"><i class="ri-bookmark-3-line"></i> Perihal</label>
                            <input type="text" id="subject" name="subject" class="form-control" placeholder="Project Foto / Video / Website...">
                        </div>

                        <div class="form-group">
                            <label for="message" class="form-label"><i class="ri-chat-3-line"></i> Pesan</label>
                            <textarea id="message" name="message" class="form-control" rows="5" placeholder="Ceritakan detail proyek Anda..." required></textarea>
                        </div>

                        <div id="formAlert" class="form-alert hide"></div>

                        <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                            <span class="btn-text"><i class="ri-send-plane-2-line"></i> Kirim Pesan</span>
                            <span class="spinner hide"><i class="ri-loader-4-line spin-icon"></i> Mengirim...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-container">
            <div class="footer-left">
                <a href="#" class="footer-logo">APVISUALS</a>
                <p class="footer-tagline">Digital Craftsmanship. Built with precision and artistic intent.</p>
                <p class="footer-copy">&copy; {{ date('Y') }} APVISUALS. All rights reserved.</p>
            </div>
            
            <div class="footer-right">
                <div class="social-links">
                    <a href="{{ $settings['social_instagram'] ?? 'https://www.instagram.com/apvisualsgallery?igsh=MWd3cGFhZHB1Mm83ag==' }}" target="_blank" aria-label="Instagram"><i class="ri-instagram-line"></i></a>
                    <a href="{{ $settings['social_linkedin'] ?? 'https://www.linkedin.com/in/aditya-putra-2b9989325?utm_source=share_via&utm_content=profile&utm_medium=member_android' }}" target="_blank" aria-label="LinkedIn"><i class="ri-linkedin-line"></i></a>
                    <a href="{{ $settings['social_github'] ?? 'https://github.com/adityaputr23' }}" target="_blank" aria-label="GitHub"><i class="ri-github-line"></i></a>
                    <a href="{{ $settings['social_tiktok'] ?? 'https://www.tiktok.com/@ap.visuals_?_r=1&_t=ZS-98eyTz8bktI' }}" target="_blank" aria-label="TikTok"><i class="ri-tiktok-line"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Project Lightbox Modal -->
    <div id="projectModal" class="modal">
        <span class="modal-close"><i class="ri-close-line"></i></span>
        <div class="modal-content" id="modalContent">
            <div class="modal-grid">
                <div class="modal-media">
                    <img id="modalImg" src="" alt="" class="modal-img">
                    <video id="modalVideo" src="" autoplay loop controls playsinline class="modal-video hide"></video>
                </div>
                <div class="modal-info">
                    <div class="modal-info-left">
                        <span id="modalCategory" class="modal-category">Category</span>
                        <h3 id="modalTitle" class="modal-title">Project Title</h3>
                        <div id="modalTags" class="modal-tags">
                            <!-- Dynamic Tags -->
                        </div>
                        <p id="modalDesc" class="modal-desc">Project description details go here...</p>
                    </div>
                    <a id="modalUrl" href="#" target="_blank" class="btn btn-primary modal-action-btn">
                        <i class="ri-external-link-line"></i> View Project
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
