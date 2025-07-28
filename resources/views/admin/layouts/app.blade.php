<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/event_section.css') }}">
    <link rel="stylesheet" href="{{ asset('css/preview.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/preview.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Sidebar styles */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background-color: #991b1b;
            color: white;
            padding: 24px 16px;
            overflow-y: auto;
            z-index: 30;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        /* Main content styles */
        .admin-main {
            margin-left: 240px;
            padding-top: 80px;
            min-height: 100vh;
        }

        .admin-content {
            padding: 24px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 200px;
            }

            .admin-navbar {
                left: 200px;
            }

            .admin-main {
                margin-left: 200px;
            }

            .admin-content {
                padding: 16px;
            }
        }

        @media (max-width: 640px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .admin-navbar {
                left: 0;
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        @include('admin.partials.sidebar')
    </aside>

    <!-- Top Navbar -->
    <header class="admin-navbar">
        @include('admin.partials.navbar')
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-content">
            @include('admin.components.alert')
            @yield('content')
        </div>
    </main>

</body>

</html>