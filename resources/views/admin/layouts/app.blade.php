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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/preview.js') }}"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <header class="bg-white shadow px-6 py-4 sticky top-0 z-50 w-full">
        @include('admin.partials.navbar')
    </header>

    <div class="grid grid-cols-[15rem_1fr] min-h-[calc(100vh-4rem)]">

        <aside class="bg-red-800 text-white px-4 py-6 h-full overflow-y-auto">
            @include('admin.partials.sidebar')
        </aside>

        <main class="p-6 overflow-y-auto" style="margin-top: 100px;">
            @include('admin.components.alert')
            @yield('content')
        </main>
    </div>

</body>

</html>