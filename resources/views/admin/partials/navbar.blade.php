<nav class="bg-red-800 p-4">
    <div class="container">
        <ul class="flex space-x-4">
            <li><a href="{{ route('admin.dashboard') }}" class="text-white">Dashboard</a></li>
            <li><a href="{{ route('admin.scanQr') }}" class="text-white">Scan QR</a></li>
            <li><a href="{{ route('events.create') }}" class="text-white">Create Events</a></li>
        </ul>
    </div>
</nav>