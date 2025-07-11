<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    @include('admin.partials.navbar')
    @include('admin.partials.sidebar')

    <main class="p-4">
        @include('admin.components.alert')
        @yield('content')
    </main>

</body>

</html>