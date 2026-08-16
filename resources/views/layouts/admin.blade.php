<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'APVISUALS Admin Panel')</title>

    <!-- Modern Google Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Remix Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Custom Admin Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    
    @yield('styles')
</head>
<body>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="admin-logo-badge"><i class="ri-terminal-window-line"></i></div>
                <div class="sidebar-title-group">
                    <span class="sidebar-logo-text">Studio Admin</span>
                    <span class="sidebar-subtitle">Creative Director</span>
                </div>
                <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close Sidebar"><i class="ri-close-line"></i></button>
            </div>

            <nav class="sidebar-menu">
                <a href="{{ route('admin.dashboard') }}" class="menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                    <i class="ri-dashboard-line"></i> <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.projects.index') }}" class="menu-item {{ Request::is('admin/projects*') ? 'active' : '' }}">
                    <i class="ri-folder-video-line"></i> <span>Projects</span>
                </a>
                <a href="{{ route('admin.skills.index') }}" class="menu-item {{ Request::is('admin/skills*') ? 'active' : '' }}">
                    <i class="ri-tools-line"></i> <span>Skills</span>
                </a>
                <a href="{{ route('admin.enquiries.index') }}" class="menu-item {{ Request::is('admin/enquiries*') ? 'active' : '' }}">
                    <i class="ri-mail-line"></i> <span>Enquiries</span>
                </a>
                <a href="{{ route('admin.settings.edit') }}" class="menu-item {{ Request::is('admin/settings*') ? 'active' : '' }}">
                    <i class="ri-settings-4-line"></i> <span>Settings</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-new-project">
                    <i class="ri-add-line"></i> <span>New Project</span>
                </a>
                
                <a href="{{ route('portfolio.index') }}" target="_blank" class="menu-item external-link">
                    <i class="ri-external-link-line"></i> <span>View Live Site</span>
                </a>

                <div class="divider"></div>

                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <button type="submit" class="menu-item btn-logout">
                        <i class="ri-logout-box-r-line"></i> <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <main class="main-workspace">
            <!-- Header bar -->
            <header class="workspace-header">
                <div class="header-left">
                    <button type="button" class="admin-mobile-toggle" id="adminSidebarToggle" aria-label="Toggle Sidebar">
                        <i class="ri-menu-line"></i>
                    </button>
                    <div class="search-bar">
                        <i class="ri-search-line search-icon"></i>
                        <input type="text" placeholder="Search resources..." aria-label="Search">
                    </div>
                </div>
                
                <div class="user-action-group">
                    <button class="icon-btn" aria-label="Notifications"><i class="ri-notification-3-line"></i><span class="badge-dot"></span></button>
                    <a href="{{ route('admin.settings.edit') }}" class="icon-btn" aria-label="Settings"><i class="ri-settings-line"></i></a>
                    
                    <div class="user-profile">
                        @php $profileImg = \App\Models\Setting::getValue('profile_image', 'profile.jpg'); @endphp
                        <img src="{{ asset('images/' . $profileImg) }}" alt="User profile photo" class="avatar-header">
                        <div class="profile-meta">
                            <span class="user-name">{{ Auth::user()->name }}</span>
                            <span class="user-role">Admin</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Workspace Content View -->
            <div class="workspace-content">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="ri-error-warning-line"></i> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Global Footer -->
            <footer class="workspace-footer">
                <span class="copy">&copy; {{ date('Y') }} APVISUALS Admin Panel. All rights reserved.</span>
                <div class="footer-links">
                    <a href="#">Documentation</a>
                    <a href="#">System Status</a>
                    <a href="#">Privacy Policy</a>
                </div>
            </footer>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('adminSidebarToggle');
            const closeBtn = document.getElementById('sidebarCloseBtn');
            const backdrop = document.getElementById('sidebarBackdrop');

            const openSidebar = () => {
                if (sidebar) sidebar.classList.add('open');
                if (backdrop) backdrop.classList.add('active');
            };

            const closeSidebar = () => {
                if (sidebar) sidebar.classList.remove('open');
                if (backdrop) backdrop.classList.remove('active');
            };

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);
        });
    </script>

    @yield('scripts')
</body>
</html>
