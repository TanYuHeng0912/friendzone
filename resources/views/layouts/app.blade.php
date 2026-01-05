<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FriendZone') }} - Find Your Perfect Match</title>

    <!-- Prevent Flash of Unstyled Content -->
    <style>
        html {
            background-color: #f7fafc;
        }
        body {
            background-color: #f7fafc !important;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        body.loaded {
            opacity: 1;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }
        /* Prevent red flash from delete buttons and other elements */
        #delete, .nav-item-danger, .btn-danger {
            transition: background-color 0.3s ease, color 0.3s ease !important;
        }
        
        /* Prevent badge flash */
        .nav-badge {
            transition: opacity 0.3s ease !important;
        }
        
        /* Ensure smooth page transitions - prevent any color flashes */
        body:not(.loaded) {
            overflow: hidden;
        }
        
        /* Smooth transitions for all elements */
        * {
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }
    </style>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Laravel Echo & Pusher (for WebSocket support) -->
    @php
        // Read from config (which reads from .env)
        // Note: env() doesn't work in Blade when config is cached, so we use config()
        $broadcastDriver = config('broadcasting.default');
        $pusherKey = config('broadcasting.connections.pusher.key');
        $pusherCluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');
        $pusherHost = config('broadcasting.connections.pusher.options.host', '127.0.0.1');
        $pusherPort = config('broadcasting.connections.pusher.options.port', 6001);
        $pusherScheme = config('broadcasting.connections.pusher.options.scheme', 'http');
        $shouldEnableWebSocket = ($broadcastDriver === 'pusher' && !empty($pusherKey));
        
        // Debug: Log to Laravel log file
        \Log::info('WebSocket Config Check', [
            'broadcastDriver' => $broadcastDriver,
            'pusherKey' => $pusherKey ? 'SET' : 'NOT SET',
            'shouldEnable' => $shouldEnableWebSocket
        ]);
    @endphp
    @if($shouldEnableWebSocket)
        <meta name="broadcast-driver" content="pusher">
        <meta name="pusher-key" content="{{ $pusherKey }}">
        <meta name="pusher-cluster" content="{{ $pusherCluster }}">
        <meta name="pusher-host" content="{{ $pusherHost }}">
        <meta name="pusher-port" content="{{ $pusherPort }}">
        <meta name="pusher-scheme" content="{{ $pusherScheme }}">
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script>
            window.Pusher = Pusher;
            // Also set as global variables for easy access
            window.PUSHER_CONFIG = {
                driver: 'pusher',
                key: '{{ $pusherKey }}',
                cluster: '{{ $pusherCluster }}',
                host: '{{ $pusherHost }}',
                port: {{ $pusherPort }},
                scheme: '{{ $pusherScheme }}'
            };
            console.log('WebSocket: PUSHER_CONFIG initialized', window.PUSHER_CONFIG);
        </script>
    @else
        <script>
            console.warn('WebSocket: Configuration check failed', {
                broadcastDriver: '{{ $broadcastDriver }}',
                pusherKey: '{{ $pusherKey ? "SET" : "NOT SET" }}',
                shouldEnable: {{ $shouldEnableWebSocket ? 'true' : 'false' }}
            });
        </script>
    @endif
</head>
<body onload="setTimeout(function(){document.body.classList.add('loaded')}, 50)">
<div id="app">
    <nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="{{ url('/home') }}">
                <div class="brand-container">
                    <!-- Using your new FriendZone logo -->
                    <img src="/storage/picture/friendzone-logo.png.png" alt="FriendZone Logo" class="brand-logo" id="brandLogo" onerror="this.src='/storage/picture/pngwing.com.png';">
                    <div class="brand-text">
                        <span class="brand-name">FriendZone</span>
                        <span class="brand-tagline">Recognition</span>
                    </div>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> {{ __('Login') }}
                            </a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link nav-link-primary" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus"></i> {{ __('Register') }}
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('chat.index') }}">
                                <i class="fas fa-comments"></i> {{ __('Chat') }}
                                @php
                                    $unreadChatsCount = App\Chat::where(function($query) {
                                        $query->where('user_one', auth()->id())
                                              ->orWhere('user_two', auth()->id());
                                    })->get()->sum(function($chat) {
                                        return $chat->getUnreadCount(auth()->id());
                                    });
                                @endphp
                                @if($unreadChatsCount > 0)
                                    <span class="nav-badge">{{ $unreadChatsCount }}</span>
                                @endif
                            </a>
                        </li>

                        @if(auth()->user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> {{ __('Admin') }}
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('matches') }}">
                                <i class="fas fa-heart"></i> {{ __('Matches') }}
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('community.index') }}">
                                <i class="fas fa-users"></i> {{ __('Communities') }}
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pictures.show') }}">
                                <i class="fas fa-images"></i> {{ __('Photos') }}
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.showEditProfile') }}">
                                <i class="fas fa-user-circle"></i> {{ __('Profile') }}
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('feedback.create') }}">
                                <i class="fas fa-comment-alt"></i> {{ __('Feedback') }}
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="return false;">
                                <i class="fas fa-user-circle"></i> {{ auth()->user()->info->name ?? 'User' }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile.showEditProfile') }}">
                                    <i class="fas fa-user-edit"></i> Edit Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('profile.updateSettings') }}">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="main-content">
        @yield('content')
    </main>
</div>

<script>

// Initialize Bootstrap dropdown
document.addEventListener('DOMContentLoaded', function() {
    // Ensure Bootstrap dropdown works
    const dropdownToggle = document.getElementById('userDropdown');
    if (dropdownToggle) {
        // Use jQuery if available, otherwise use vanilla JS
        if (typeof $ !== 'undefined' && $.fn.dropdown) {
            $(dropdownToggle).dropdown();
        } else {
            // Fallback: manual dropdown toggle
            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const dropdown = this.nextElementSibling;
                const isOpen = dropdown.classList.contains('show');
                
                // Close all other dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
                
                // Toggle this dropdown
                if (!isOpen) {
                    dropdown.classList.add('show');
                } else {
                    dropdown.classList.remove('show');
                }
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });
        }
    }
});
</script>
</body>
</html>

<style>
    /* Global Typography */
    * {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    body {
        font-family: 'Inter', sans-serif;
        font-weight: 400;
        line-height: 1.6;
        color: #2d3748;
        background-color: #f7fafc;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #1a202c;
    }

    /* Color Variables */
    :root {
        --primary-color: #667eea;
        --primary-dark: #5568d3;
        --primary-light: #764ba2;
        --secondary-color: #f093fb;
        --accent-color: #4facfe;
        --success-color: #48bb78;
        --danger-color: #f56565;
        --warning-color: #ed8936;
        --info-color: #4299e1;
        
        --bg-color: #f7fafc;
        --text-color: #2d3748;
        --text-light: #718096;
        --card-bg: #ffffff;
        --border-color: #e2e8f0;
        --shadow: rgba(0, 0, 0, 0.1);
        --shadow-lg: rgba(0, 0, 0, 0.15);
        
        --navbar-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --navbar-text: #ffffff;
    }


    /* Navbar Styles */
    .navbar-custom {
        background: var(--navbar-bg) !important;
        padding: 0.75rem 0;
        border-bottom: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar-custom .navbar-brand {
        padding: 0.5rem 0;
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: transform 0.2s ease;
    }

    .navbar-custom .navbar-brand:hover {
        transform: translateY(-2px);
    }

    .brand-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-logo {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        background: white;
    }


    .brand-logo:hover {
        transform: scale(1.05);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .brand-text {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .brand-name {
        font-family: 'Poppins', sans-serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--navbar-text);
        letter-spacing: -0.5px;
    }

    .brand-tagline {
        font-size: 0.75rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.85);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .navbar-custom .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500;
        font-size: 0.95rem;
        padding: 0.6rem 1rem !important;
        margin: 0 0.2rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
    }

    .navbar-custom .nav-link i {
        font-size: 1rem;
    }

    .navbar-custom .nav-link:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    .navbar-custom .nav-link-primary {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .navbar-custom .nav-link-primary:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .nav-badge {
        background: #f56565;
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-left: 5px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }

    .navbar-custom .navbar-toggler {
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 6px;
        padding: 0.4rem 0.6rem;
    }

    .navbar-custom .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }


    /* Dropdown Menu */
    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border-radius: 12px;
        padding: 0.5rem 0;
        margin-top: 0.5rem;
        display: none;
    }
    
    .dropdown-menu.show {
        display: block;
    }
    
    .nav-item.dropdown {
        position: relative;
    }
    
    .dropdown-toggle {
        cursor: pointer;
    }

    .dropdown-item {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dropdown-item i {
        width: 20px;
        text-align: center;
    }

    .dropdown-item:hover {
        background: #f7fafc;
        color: var(--primary-color);
    }

    .dropdown-divider {
        margin: 0.5rem 0;
        border-top: 1px solid #e2e8f0;
    }

    /* Main Content */
    .main-content {
        min-height: calc(100vh - 76px);
        padding: 0;
    }


    /* Responsive */
    @media (max-width: 991px) {
        .brand-name {
            font-size: 1.2rem;
        }

        .brand-tagline {
            font-size: 0.65rem;
        }

        .navbar-custom .nav-link {
            padding: 0.75rem 1rem !important;
        }
    }

    @media (max-width: 768px) {
        .brand-container {
            gap: 8px;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
        }

        .brand-name {
            font-size: 1.1rem;
        }

        .brand-tagline {
            display: none;
        }
    }
</style>
