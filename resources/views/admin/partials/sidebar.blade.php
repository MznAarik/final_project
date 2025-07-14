<div class="sidebar my-24 text-center text-white font-bold text-2l w-60 min-h-screen py-auto leading-12 relative "
    style="margin-top: 100px; position:fixed;">
    <ul class="space-y-4">
        <li>
            <a href="{{ route('admin.dashboard') }}"
                class="block px-4 py-2 rounded-md{{ request()->is('admin/dashboard') ? ' bg-red-600 font-semibold' : ' hover:bg-red-500' }} transition"><i
                    class="fa-solid fa-house"></i>Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.scanQr') }}"
                class="block px-3 py-2 rounded-md {{ request()->is('admin/scan-qr') ? 'bg-red-600 font-semibold' : 'hover:bg-red-500' }}">
                <i class="fa-solid fa-qrcode"></i> Scan QR-Code</a>
        </li>
        <ul class="space-y-2">
            <li>
                <a href="{{ route('events.index') }}"
                    class="block px-3 py-2 rounded-md {{ request()->is('events*') ? 'bg-red-600 font-semibold' : 'hover:bg-red-500' }}">
                    <i class="fa-solid fa-calendar-days"></i> All Events
                </a>
            </li>
            <!-- Add more static links here if needed -->
        </ul>
    </ul>
</div>