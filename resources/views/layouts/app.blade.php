<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>@yield('title', 'Mabini Health Center')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA Meta Tags (Minimal) -->
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="Queue Management System for Mabini Health Center">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192x192.png') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Submenu Styles */
        .nav-list .has-submenu > a {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .submenu-arrow {
            transition: transform 0.3s ease;
            font-size: 0.8em;
        }
        
        .has-submenu.open .submenu-arrow {
            transform: rotate(180deg);
        }
        
        .submenu {
            display: none;
            list-style: none;
            padding: 0;
            margin: 8px 0 0 0;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            overflow: hidden;
        }
        
        .has-submenu.open .submenu {
            display: block;
        }
        
        .submenu li {
            margin: 0;
        }
        
        .submenu a {
            display: block;
            padding: 8px 16px 8px 40px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9em;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .submenu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left-color: #3b82f6;
        }
        
        .submenu a.active {
            background-color: rgba(59, 130, 246, 0.3);
            color: #fff;
            border-left-color: #3b82f6;
        }
        
        /* Notification Bell Styles */
        .notification-bell:hover {
            color: #059669 !important;
            transform: scale(1.1);
        }
        
        .notification-bell:active {
            transform: scale(0.95);
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
    </style>
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    
    <div class="app-container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('images/health-center-logo.png') }}" 
                     alt="Mabini Health Center" 
                     class="sidebar-logo"
                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; margin-bottom: 0.75rem; display: block; margin-left: auto; margin-right: auto; background: white; padding: 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2 style="font-size: 1rem; margin: 0.5rem 0 0; font-weight: 600;">Mabini Health Center</h2>
                <p style="font-size: 0.7rem; color: rgba(255, 255, 255, 0.85); margin: 0.25rem 0 0; font-weight: 400;">Queue Management System</p>
            </div>
            
            <ul class="sidebar-menu">
                <!-- Staff/Admin Menu -->
                @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                <li class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('staff.dashboard') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('front-desk.*') ? 'active' : '' }}">
                    <a href="{{ route('front-desk.index') }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Front Desk Queue</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('service-management.*') ? 'active' : '' }}">
                    <a href="{{ route('service-management.index') }}" title="Set service availability, daily limits, and operating hours">
                        <i class="fas fa-cogs"></i>
                        <span>Service Management</span>
                    </a>
                </li>
                @endif
                
                <!-- Phase 2: Service-Specific Dashboards -->
                <li class="{{ request()->routeIs('services.*') ? 'active' : '' }} has-submenu">
                    <a href="#" onclick="toggleSubmenu(event)">
                        <i class="fas fa-hospital"></i>
                        <span>Service Queues</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        @php
                            $user = auth()->user();
                            $services = \App\Models\Service::where('is_active', true)->get();
                            
                            // Filter services based on user access
                            if ($user->isServiceStaff()) {
                                $services = $services->where('id', $user->service_id);
                            }
                        @endphp
                        @foreach($services as $service)
                            @if($user->canAccessService($service->id))
                            <li>
                                <a href="{{ route('services.dashboard', $service->id) }}" 
                                   class="{{ request()->route('serviceId') == $service->id ? 'active' : '' }}">
                                    @php
                                        $icon = 'hospital';
                                        if ($service->name === 'General Practitioner') $icon = 'user-md';
                                        elseif ($service->name === 'Dental Service') $icon = 'tooth';
                                        elseif ($service->name === 'Laboratory Service') $icon = 'vial';
                                        elseif (str_contains($service->name, 'Maternal')) $icon = 'baby';
                                    @endphp
                                    <i class="fas fa-{{ $icon }}"></i>
                                    <span>{{ $service->name }}</span>
                                </a>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
                
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'front_desk')
                <!-- Public Queue Display for Patients -->
                <li class="{{ request()->routeIs('queue.display') ? 'active' : '' }}">
                    <a href="{{ route('queue.display') }}" target="_blank" title="Public display showing current queue status">
                        <i class="fas fa-tv"></i>
                        <span>Public Queue Display</span>
                    </a>
                </li>

                <!-- Centralized Announcements -->
                <li class="{{ request()->routeIs('queue.announcements') ? 'active' : '' }}">
                    <a href="{{ route('queue.announcements') }}" target="_blank" title="Centralized voice announcements for all services">
                        <i class="fas fa-volume-up"></i>
                        <span>Voice Announcements</span>
                    </a>
                </li>
                @endif
                
                @if(Auth::user()->role === 'staff' || Auth::user()->role === 'admin' || Auth::user()->role === 'front_desk')
                <!-- Staff Tools: Queue Analytics, Patient Search, Transfer Management -->
                <li class="{{ request()->routeIs('staff.*') || request()->routeIs('staff.queue.requests') ? 'active' : '' }}">
                    <a href="{{ route('staff.queue.management') }}" title="Queue Analytics, Patient Search, Transfer Management">
                        <i class="fas fa-chart-bar"></i>
                        <span>Queue Analytics</span>
                    </a>
                </li>
                @endif
                
                @if(Auth::user()->role === 'admin')
                <li class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <a href="{{ route('admin.users') }}" title="Manage staff accounts, system settings, and user permissions">
                        <i class="fas fa-users-cog"></i>
                        <span>Admin Panel</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.activity.logs') ? 'active' : '' }}">
                    <a href="{{ route('admin.activity.logs') }}">
                        <i class="fas fa-history"></i>
                        <span>Activity Logs</span>
                    </a>
                </li>
                @endif
                {{-- Admin Support Management --}}
                @if(Auth::user()->isAdmin())
                <li class="{{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.support.index') }}">
                        <i class="fas fa-headset"></i>
                        <span>Support Messages</span>
                        @php
                            $pendingSupport = \App\Models\SupportMessage::where('status', 'pending')->count();
                        @endphp
                        @if($pendingSupport > 0)
                            <span class="badge badge-warning" style="background: #ffc107; color: #000; padding: 2px 8px; border-radius: 12px; margin-left: 8px; font-size: 11px; font-weight: bold;">
                                {{ $pendingSupport }}
                            </span>
                        @endif
                    </a>
                </li>
                @endif
                
                {{-- Regular Support for Non-Admin Users --}}
                @if(!Auth::user()->isAdmin())
                <li class="{{ request()->routeIs('staff.support') ? 'active' : '' }}">
                    <a href="{{ route('staff.support') }}">
                        <i class="fas fa-headset"></i>
                        <span>Help & Support</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle navigation menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="header-right">
                    <!-- Notification Bell -->
                    <div class="notification-wrapper" style="position: relative; display: flex; align-items: center;">
                        <button id="notificationBell" class="notification-bell" style="position: relative; background: none; border: none; color: #6b7280; cursor: pointer; font-size: 1.5rem; padding: 0.5rem; transition: color 0.2s ease; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-bell"></i>
                            <span id="notificationBadge" class="notification-badge" style="display: none; position: absolute; top: 2px; right: 2px; background: #ef4444; color: white; border-radius: 50%; min-width: 20px; height: 20px; font-size: 0.7rem; font-weight: bold; display: flex; align-items: center; justify-content: center; padding: 0 5px; border: 2px solid white;">0</span>
                        </button>
                        
                        <!-- Notification Dropdown -->
                        <div id="notificationDropdown" class="notification-dropdown" style="display: none; position: absolute; top: calc(100% + 8px); right: 0; background: white; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.15); width: 400px; max-height: 500px; overflow: hidden; z-index: 9999;">
                            <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="margin: 0; font-size: 1rem; color: #111827; font-weight: 600;">Notifications</h3>
                                <button id="markAllRead" style="background: none; border: none; color: #3b82f6; cursor: pointer; font-size: 0.875rem; font-weight: 500;">Mark all as read</button>
                            </div>
                            <div id="notificationList" style="max-height: 400px; overflow-y: auto;">
                                <div style="padding: 2rem; text-align: center; color: #9ca3af;">
                                    <i class="fas fa-bell-slash" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                    <p style="margin: 0;">No notifications</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="user-info">
                        <i class="fas fa-user-md"></i>
                        <span>{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Toast Notification Container -->
            <div id="toastContainer" style="position: fixed; bottom: 2rem; right: 2rem; z-index: 9999; display: flex; flex-direction: column-reverse; gap: 0.75rem; max-width: 400px;"></div>

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
        // Submenu Toggle Function
        function toggleSubmenu(event) {
            event.preventDefault();
            const parentLi = event.target.closest('.has-submenu');
            if (parentLi) {
                parentLi.classList.toggle('open');
            }
        }

        // Mobile Navigation Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            if (mobileMenuToggle && sidebar && mobileOverlay) {
                // Toggle sidebar
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    mobileOverlay.classList.toggle('active');
                    document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
                });
                
                // Close sidebar when clicking overlay
                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    mobileOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                });
                
                // Close sidebar when clicking a link (mobile only)
                if (window.innerWidth <= 768) {
                    const sidebarLinks = sidebar.querySelectorAll('a');
                    sidebarLinks.forEach(link => {
                        link.addEventListener('click', function() {
                            sidebar.classList.remove('active');
                            mobileOverlay.classList.remove('active');
                            document.body.style.overflow = '';
                        });
                    });
                }
                
                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        sidebar.classList.remove('active');
                        mobileOverlay.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                });
            }
            
            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });

            // Notification System
            const notificationBell = document.getElementById('notificationBell');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationBadge = document.getElementById('notificationBadge');
            const notificationList = document.getElementById('notificationList');
            const markAllRead = document.getElementById('markAllRead');

            // Toggle notification dropdown
            notificationBell.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.style.display = notificationDropdown.style.display === 'none' ? 'block' : 'none';
                
                if (notificationDropdown.style.display === 'block') {
                    loadNotifications();
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.style.display = 'none';
                }
            });

            // Mark all as read
            markAllRead.addEventListener('click', function() {
                fetch('{{ route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationCount();
                        loadNotifications();
                    }
                });
            });

            // Load notifications
            function loadNotifications() {
                fetch('{{ route("notifications.index") }}')
                    .then(response => response.json())
                    .then(notifications => {
                        if (notifications.length === 0) {
                            notificationList.innerHTML = `
                                <div style="padding: 2rem; text-align: center; color: #9ca3af;">
                                    <i class="fas fa-bell-slash" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                    <p style="margin: 0;">No notifications</p>
                                </div>
                            `;
                        } else {
                            notificationList.innerHTML = notifications.map(notif => {
                                const typeColors = {
                                    'service_status': '#3b82f6',
                                    'capacity_warning': '#f59e0b',
                                    'priority_alert': '#ef4444',
                                    'long_wait': '#f59e0b'
                                };
                                
                                const typeIcons = {
                                    'service_status': 'fa-hospital',
                                    'capacity_warning': 'fa-exclamation-triangle',
                                    'priority_alert': 'fa-bell',
                                    'long_wait': 'fa-clock'
                                };

                                const timeAgo = getTimeAgo(new Date(notif.created_at));
                                
                                return `
                                    <div class="notification-item" data-id="${notif.id}" style="padding: 1rem; border-bottom: 1px solid #e5e7eb; cursor: pointer; transition: background 0.2s; ${!notif.is_read ? 'background: #eff6ff;' : ''}">
                                        <div style="display: flex; gap: 0.75rem;">
                                            <div style="flex-shrink: 0;">
                                                <div style="width: 40px; height: 40px; border-radius: 50%; background: ${typeColors[notif.type]}15; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas ${typeIcons[notif.type]}" style="color: ${typeColors[notif.type]}; font-size: 1.1rem;"></i>
                                                </div>
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.25rem;">
                                                    <h4 style="margin: 0; font-size: 0.875rem; font-weight: 600; color: #111827;">${notif.title}</h4>
                                                    ${!notif.is_read ? '<div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; margin-top: 0.25rem;"></div>' : ''}
                                                </div>
                                                <p style="margin: 0; font-size: 0.8rem; color: #6b7280; line-height: 1.4;">${notif.message}</p>
                                                <span style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem; display: block;">${timeAgo}</span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('');

                            // Add click handlers to mark as read
                            document.querySelectorAll('.notification-item').forEach(item => {
                                item.addEventListener('click', function() {
                                    const notifId = this.getAttribute('data-id');
                                    markAsRead(notifId);
                                });
                            });
                        }
                    });
            }

            // Mark single notification as read
            function markAsRead(notifId) {
                fetch(`/notifications/${notifId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationCount();
                        loadNotifications();
                    }
                });
            }

            // Update notification count
            function updateNotificationCount() {
                fetch('{{ route("notifications.count") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.count > 0) {
                            notificationBadge.textContent = data.count > 99 ? '99+' : data.count;
                            notificationBadge.style.display = 'flex';
                        } else {
                            notificationBadge.style.display = 'none';
                        }
                    });
            }

            // Time ago helper
            function getTimeAgo(date) {
                const seconds = Math.floor((new Date() - date) / 1000);
                
                if (seconds < 60) return 'Just now';
                if (seconds < 3600) return `${Math.floor(seconds / 60)} minutes ago`;
                if (seconds < 86400) return `${Math.floor(seconds / 3600)} hours ago`;
                if (seconds < 604800) return `${Math.floor(seconds / 86400)} days ago`;
                return date.toLocaleDateString();
            }

            // Update count on page load
            updateNotificationCount();

            // Poll for new notifications every 30 seconds
            setInterval(updateNotificationCount, 30000);

            // Toast Notification System
            window.showToast = function(title, message, type = 'info') {
                const toastContainer = document.getElementById('toastContainer');
                const toast = document.createElement('div');
                
                const colors = {
                    'success': { bg: '#10b981', icon: 'fa-check-circle' },
                    'error': { bg: '#ef4444', icon: 'fa-exclamation-circle' },
                    'warning': { bg: '#f59e0b', icon: 'fa-exclamation-triangle' },
                    'info': { bg: '#3b82f6', icon: 'fa-info-circle' }
                };
                
                const style = colors[type] || colors.info;
                
                toast.innerHTML = `
                    <div style="background: white; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); padding: 1rem; min-width: 300px; max-width: 400px; animation: slideIn 0.3s ease;">
                        <div style="display: flex; gap: 0.75rem; align-items: start;">
                            <div style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; background: ${style.bg}15; display: flex; align-items: center; justify-content: center;">
                                <i class="fas ${style.icon}" style="color: ${style.bg}; font-size: 1.1rem;"></i>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 0.25rem; font-size: 0.95rem; font-weight: 600; color: #111827;">${title}</h4>
                                <p style="margin: 0; font-size: 0.85rem; color: #6b7280; line-height: 1.4;">${message}</p>
                            </div>
                            <button onclick="this.closest('div').parentElement.remove()" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 0; font-size: 1.2rem; line-height: 1; margin-left: 0.5rem;">×</button>
                        </div>
                    </div>
                `;
                
                toastContainer.appendChild(toast);
                
                // Auto-remove after 5 seconds
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(20px)';
                    toast.style.transition = 'all 0.3s';
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            };
        });
    </script>
    
    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>

    <!-- PWA Service Worker Registration (Safe Version) -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function(registration) {
                        console.log('✅ Service Worker registered successfully');
                    })
                    .catch(function(error) {
                        console.log('❌ Service Worker registration failed:', error);
                    });
            });
        }
    </script>

    <!-- Bootstrap 5 Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>
