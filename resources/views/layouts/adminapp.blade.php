<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - {{ config('app.name', 'FriendZone') }}</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .admin-sidebar-header {
            padding: 25px 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .admin-sidebar-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-sidebar-header .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .admin-sidebar-nav {
            padding: 20px 0;
        }

        .admin-nav-item {
            display: block;
            padding: 15px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-nav-item:hover,
        .admin-nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #667eea;
        }

        .admin-nav-item i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
        }

        .admin-header {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }

        .admin-user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-user-info {
            text-align: right;
        }

        .admin-user-info .name {
            font-weight: 600;
            color: #333;
        }

        .admin-user-info .role {
            font-size: 0.85rem;
            color: #666;
        }

        .admin-content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }

        /* Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stat-card.primary { border-left-color: #667eea; }
        .stat-card.success { border-left-color: #48bb78; }
        .stat-card.warning { border-left-color: #ed8936; }
        .stat-card.info { border-left-color: #4299e1; }
        .stat-card.danger { border-left-color: #f56565; }
        .stat-card.purple { border-left-color: #9f7aea; }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-card-title {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-card.primary .stat-card-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card.success .stat-card-icon { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); }
        .stat-card.warning .stat-card-icon { background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); }
        .stat-card.info .stat-card-icon { background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%); }
        .stat-card.danger .stat-card-icon { background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); }
        .stat-card.purple .stat-card-icon { background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%); }

        .stat-card-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a202c;
            margin: 10px 0;
        }

        .stat-card-footer {
            font-size: 0.85rem;
            color: #666;
            margin-top: 10px;
        }

        /* Buttons */
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            color: white;
        }

        /* Tables */
        .admin-table {
            width: 100%;
        }

        .admin-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .admin-table thead th {
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .admin-table tbody tr {
            transition: background 0.2s ease;
        }

        .admin-table tbody tr:hover {
            background: #f7fafc;
        }

        /* Alerts */
        .alert-modern {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .admin-sidebar.active {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-sidebar-header">
            <h3>
                <span class="logo-icon"><i class="fas fa-shield-alt"></i></span>
                Admin Panel
            </h3>
        </div>
        <nav class="admin-sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.users') }}" class="admin-nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="{{ route('admin.communities') }}" class="admin-nav-item {{ request()->routeIs('admin.communities*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>Communities</span>
            </a>
            <a href="{{ route('admin.feedback') }}" class="admin-nav-item {{ request()->routeIs('admin.feedback*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i>
                <span>Feedback</span>
            </a>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
            <a href="{{ route('home') }}" class="admin-nav-item">
                <i class="fas fa-home"></i>
                <span>Back to Site</span>
            </a>
            <a href="{{ route('logout') }}" class="admin-nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <div>
                <h1>@yield('page-title', 'Admin Dashboard')</h1>
                <p class="text-muted mb-0">@yield('page-subtitle', 'Manage your application')</p>
            </div>
            <div class="admin-user-menu">
                <div class="admin-user-info">
                    <div class="name">{{ auth()->user()->email }}</div>
                    <div class="role">Administrator</div>
                </div>
                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </div>

        <div class="admin-content">
            @if(session('success'))
                <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script>
    // Bootstrap 5 compatibility
    if (typeof bootstrap === 'undefined' && typeof $ !== 'undefined') {
        // Fallback for Bootstrap 4
        $('.alert').alert();
    }
</script>
</body>
</html>
