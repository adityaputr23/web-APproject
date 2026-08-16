@extends('layouts.admin')

@section('title', 'Dashboard | APVISUALS Admin')

@section('content')
    <div class="dashboard-container">
        <!-- Overview Banner -->
        <div class="overview-banner">
            <div class="overview-text-group">
                <span class="overview-breadcrumb">Overview</span>
                <h1 class="overview-heading">Welcome back, Alex. Your creative ecosystem is operating at peak efficiency.</h1>
            </div>
            <div class="system-health-badge">
                <span class="pulse-dot"></span>
                <span>System Health: 99.9% Online</span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Projects</span>
                    <span class="stat-icon-wrapper"><i class="ri-folder-open-line"></i></span>
                </div>
                <div class="stat-value-group">
                    <span class="stat-value">{{ $totalProjects }}</span>
                    <span class="stat-trend trend-up"><i class="ri-arrow-up-line"></i> +12%</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Site Views</span>
                    <span class="stat-icon-wrapper"><i class="ri-eye-line"></i></span>
                </div>
                <div class="stat-value-group">
                    <span class="stat-value">{{ $totalViews }}</span>
                    <span class="stat-trend trend-up"><i class="ri-arrow-up-line"></i> +24%</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Active Skills</span>
                    <span class="stat-icon-wrapper"><i class="ri-tools-line"></i></span>
                </div>
                <div class="stat-value-group">
                    <span class="stat-value">{{ $activeSkills }}</span>
                    <span class="stat-trend trend-steady">Steady</span>
                </div>
            </div>

            <div class="stat-card {{ $newEnquiries > 0 ? 'highlight-alert' : '' }}">
                <div class="stat-header">
                    <span class="stat-title">New Enquiries</span>
                    <span class="stat-icon-wrapper"><i class="ri-mail-unread-line"></i></span>
                </div>
                <div class="stat-value-group">
                    <span class="stat-value">{{ sprintf('%02d', $newEnquiries) }}</span>
                    <span class="stat-trend {{ $newEnquiries > 0 ? 'trend-alert' : 'trend-steady' }}">
                        {{ $newEnquiries > 0 ? 'Action Needed' : 'Inbox Clear' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Middle Content Split (Analytics & Activities) -->
        <div class="dashboard-split-grid">
            <!-- Left Side: Weekly Traffic & Recent Activity -->
            <div class="split-left">
                <!-- Weekly Traffic SVG Chart -->
                <div class="content-card chart-card">
                    <h3 class="card-title">Weekly Traffic Analytics</h3>
                    <div class="chart-wrapper">
                        @php
                            $maxVal = max(array_values($trafficData));
                            $maxVal = $maxVal > 0 ? $maxVal : 10;
                            // Generate SVG height metrics
                            $points = [];
                            $index = 0;
                            foreach($trafficData as $day => $val) {
                                $x = 40 + ($index * 75);
                                $y = 170 - (($val / $maxVal) * 120);
                                $points[] = "$x,$y";
                                $index++;
                            }
                            $pointsStr = implode(' ', $points);
                        @endphp
                        
                        <svg viewBox="0 0 540 220" class="traffic-svg">
                            <!-- Grid Lines -->
                            <line x1="30" y1="170" x2="520" y2="170" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                            <line x1="30" y1="110" x2="520" y2="110" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                            <line x1="30" y1="50" x2="520" y2="50" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
                            
                            <!-- Graph Area under Line -->
                            @if(count($points) > 0)
                                <polygon points="40,170 {{ $pointsStr }} 490,170" fill="url(#chartGrad)" opacity="0.15" />
                                <polyline points="{{ $pointsStr }}" fill="none" stroke="var(--primary)" stroke-width="3" stroke-linecap="round" />
                            @endif

                            <!-- Gradient Definition -->
                            <defs>
                                <linearGradient id="chartGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="var(--primary)" />
                                    <stop offset="100%" stop-color="transparent" />
                                </linearGradient>
                            </defs>

                            <!-- Nodes & Values -->
                            @php $idx = 0; @endphp
                            @foreach($trafficData as $day => $val)
                                @php
                                    $x = 40 + ($idx * 75);
                                    $y = 170 - (($val / $maxVal) * 120);
                                @endphp
                                <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="var(--primary)" stroke="#06060a" stroke-width="2" />
                                <text x="{{ $x }}" y="{{ $y - 12 }}" class="chart-val" text-anchor="middle">{{ $val }}</text>
                                <text x="{{ $x }}" y="195" class="chart-label" text-anchor="middle">{{ $day }}</text>
                                @php $idx++; @endphp
                            @endforeach
                        </svg>
                    </div>
                </div>

                <!-- Recent Activity Panel -->
                <div class="content-card activity-card">
                    <div class="card-header-actions">
                        <h3 class="card-title">Recent Activity</h3>
                        <a href="{{ route('admin.enquiries.index') }}" class="view-all-link">View All Inbox</a>
                    </div>
                    
                    <div class="activity-timeline">
                        @forelse($activities as $act)
                            <div class="timeline-item">
                                <div class="timeline-icon {{ $act['type'] === 'project' ? 'icon-project' : 'icon-enquiry' }}">
                                    @if($act['type'] === 'project')
                                        <i class="ri-folder-add-line"></i>
                                    @else
                                        <i class="ri-chat-smile-3-line"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <h4 class="timeline-title">{{ $act['title'] }}</h4>
                                    <p class="timeline-desc">{{ $act['description'] }}</p>
                                    <span class="timeline-time">{{ $act['time']->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="ri-history-line"></i>
                                <p>No recent activity detected.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Side: Quick Actions & Database Backup -->
            <div class="split-right">
                <!-- Quick Actions Panel -->
                <div class="content-card quick-actions-card">
                    <h3 class="card-title">Quick Actions</h3>
                    <div class="actions-grid">
                        <a href="{{ route('admin.projects.create') }}" class="action-btn-item">
                            <i class="ri-file-add-line icon-violet"></i>
                            <div class="action-btn-text">
                                <span class="action-btn-name">Add Work</span>
                                <span class="action-btn-desc">Upload new media</span>
                            </div>
                        </a>

                        <a href="{{ route('admin.enquiries.index') }}" class="action-btn-item">
                            <i class="ri-message-3-line icon-blue"></i>
                            <div class="action-btn-text">
                                <span class="action-btn-name">Messages</span>
                                <span class="action-btn-desc">Check inquiries</span>
                            </div>
                        </a>

                        <a href="{{ route('admin.settings.edit') }}" class="action-btn-item">
                            <i class="ri-shield-user-line icon-green"></i>
                            <div class="action-btn-text">
                                <span class="action-btn-name">SEO Report</span>
                                <span class="action-btn-desc">Modify tags & info</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Cloud Backup status card -->
                <div class="content-card backup-card">
                    <div class="backup-header">
                        <i class="ri-cloud-line backup-icon"></i>
                        <div class="backup-meta">
                            <h3 class="card-title">Cloud Backup</h3>
                            <span class="backup-status-text text-green">System backup successful</span>
                        </div>
                    </div>
                    <div class="backup-info">
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: 78%"></div>
                        </div>
                        <div class="backup-stats">
                            <span>12.4GB of assets synced</span>
                            <span>78% Full</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
