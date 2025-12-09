<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Patient Portal - Mabini Health Center')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Notification Bell */
        .notification-bell {
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .notification-bell a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: background-color 0.2s;
        }
        
        .notification-bell a:hover {
            background-color: #f0fdf4;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 20px;
            text-align: center;
            line-height: 1.2;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Sidebar menu item with badge */
        .sidebar-menu li {
            position: relative;
        }

        .sidebar-menu li a {
            position: relative;
            display: flex;
            align-items: center;
        }

        .sidebar-menu .notification-badge {
            position: absolute;
            top: 8px;
            right: 12px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
            line-height: 1.2;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-bell {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-bell a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
        }

        .notification-bell a i {
            font-size: 1.25rem;
            line-height: 1;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-info i {
            font-size: 1.25rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .logout-btn i {
            font-size: 1rem;
            line-height: 1;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            position: relative;
            z-index: 1002;
            background: transparent;
            color: #059669;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0;
        }

        .mobile-menu-btn:hover {
            background: #f0fdf4;
        }

        .mobile-menu-btn:active {
            transform: scale(0.95);
        }

        .mobile-menu-btn i {
            font-size: 1.25rem;
        }

        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .sidebar-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 45px;
                min-height: 45px;
                -webkit-tap-highlight-color: transparent;
            }

            .sidebar {
                position: fixed;
                left: -260px;
                top: 0;
                bottom: 0;
                width: 260px;
                z-index: 1000;
                transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            .sidebar.active {
                left: 0;
            }

            .sidebar-overlay {
                display: block;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                position: relative;
                z-index: 1;
            }

            .top-header {
                padding-left: 0.75rem;
                position: relative;
                z-index: 100;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .header-left {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .header-left h1 {
                font-size: 1.25rem;
            }

            .user-info span {
                display: none;
            }

            .user-info {
                gap: 0.5rem;
            }

            .user-info i {
                font-size: 1.25rem;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 24px;
                height: 24px;
                line-height: 1;
            }

            .notification-bell a {
                width: 44px;
                height: 44px;
            }

            .notification-bell a i {
                font-size: 1.25rem;
                line-height: 1;
            }

            .logout-btn {
                padding: 0.5rem;
                font-size: 0;
                min-height: 40px;
                min-width: 40px;
                width: 40px;
                height: 40px;
                -webkit-tap-highlight-color: transparent;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 6px;
            }

            .logout-btn i {
                font-size: 1.1rem;
                line-height: 1;
                margin: 0;
            }

            .logout-btn span {
                display: none;
            }

            /* Improve touch targets for sidebar menu */
            .sidebar-menu li a {
                padding: 1rem;
                min-height: 48px;
                display: flex;
                align-items: center;
                -webkit-tap-highlight-color: transparent;
            }

            .sidebar-menu li a i {
                font-size: 1.25rem;
                width: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Fix notification bell touch target */
            .notification-bell a {
                min-width: 44px;
                min-height: 44px;
                width: 44px;
                height: 44px;
                -webkit-tap-highlight-color: transparent;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .notification-bell a i {
                font-size: 1.25rem;
                line-height: 1;
            }

            /* Fix button touch targets */
            .btn, .btn-primary, .btn-secondary {
                min-height: 44px;
                padding: 0.75rem 1.25rem;
                -webkit-tap-highlight-color: transparent;
                touch-action: manipulation;
            }
        }

        @media (max-width: 480px) {
            .top-header h1 {
                font-size: 0.95rem;
            }

            .top-header {
                padding: 0.75rem 0.5rem;
                gap: 0.5rem;
            }

            .mobile-menu-btn {
                width: 36px;
                height: 36px;
                min-width: 36px;
                min-height: 36px;
            }

            .logout-btn {
                width: 36px;
                height: 36px;
                min-width: 36px;
                min-height: 36px;
                padding: 0.4rem;
            }

            .notification-bell a {
                width: 36px;
                height: 36px;
            }

            .mobile-menu-btn i {
                font-size: 1.1rem;
            }

            .user-info i {
                font-size: 1.1rem;
            }

            /* Better spacing for small screens */
            .sidebar-menu li a {
                padding: 0.875rem;
            }

            .sidebar-menu li a i {
                font-size: 1.1rem;
                width: 24px;
            }

            .sidebar-menu li a span {
                font-size: 0.9rem;
            }
        }
                font-size: 1.1rem;
            }

            .user-info i {
                font-size: 1rem;
            }
        }

        @media (max-width: 360px) {
            .sidebar {
                width: 240px;
                left: -240px;
            }

            .logout-btn span {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-container">
        <!-- Patient Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>Patient Portal</h2>
            </div>
            
            <ul class="sidebar-menu">
                <li class="{{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('patient.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('patient.profile') ? 'active' : '' }}">
                    <a href="{{ route('patient.profile') }}">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('patient.medical-history') ? 'active' : '' }}">
                    <a href="{{ route('patient.medical-history') }}">
                        <i class="fas fa-file-medical-alt"></i>
                        <span>Medical History</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('patient.notifications.*') ? 'active' : '' }}">
                    <a href="{{ route('patient.notifications.index') }}">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                        @php
                            $unreadCount = Auth::guard('patient')->user()->unreadNotifications()->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="badge bg-danger rounded-pill ms-2">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ request()->routeIs('patient.queue.request') ? 'active' : '' }}">
                    <a href="{{ route('patient.queue.request') }}">
                        <i class="fas fa-list-check"></i>
                        <span>Request Queue</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('patient.support') ? 'active' : '' }}">
                    <a href="{{ route('patient.support') }}">
                        <i class="fas fa-headset"></i>
                        <span>Help & Support</span>
                        @php
                            $user = Auth::guard('patient')->user();
                            $unreadReplies = $user ? \App\Models\SupportMessage::where('user_type', 'patient')
                                ->where('user_id', $user->id)
                                ->where('status', 'replied')
                                ->whereNotNull('admin_reply')
                                ->where('updated_at', '>', $user->last_support_check ?? now()->subDays(30))
                                ->count() : 0;
                        @endphp
                        @if($unreadReplies > 0)
                            <span class="notification-badge">{{ $unreadReplies }}</span>
                        @endif
                    </a>
                </li>

            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle Menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>@yield('page-title', 'Patient Portal')</h1>
                </div>
                <div class="header-right">
                    <!-- Notification Bell -->
                    @auth('patient')
                    <div class="notification-bell">
                        <a href="{{ route('patient.notifications.index') }}" class="position-relative text-decoration-none">
                            <i class="fas fa-bell" style="font-size: 1.25rem; color: #059669;"></i>
                            @php
                                $unreadCount = Auth::guard('patient')->user()->unreadNotifications()->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="notification-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </a>
                    </div>
                    @endauth

                    <div class="user-info">
                        <span>{{ Auth::guard('patient')->user()->full_name }}</span>
                        <form method="POST" action="{{ route('patient.logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
                
                // Change icon
                const icon = mobileMenuBtn.querySelector('i');
                if (sidebar.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }

            function closeSidebar() {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }

            // Toggle on button click
            mobileMenuBtn.addEventListener('click', toggleSidebar);

            // Close on overlay click
            sidebarOverlay.addEventListener('click', closeSidebar);

            // Close on menu item click (on mobile)
            const menuLinks = sidebar.querySelectorAll('.sidebar-menu a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                    closeSidebar();
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeSidebar();
                }
            });
        });
    </script>
</body>
</html>
