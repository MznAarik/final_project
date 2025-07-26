<div class="fixed top-[100px] left-0 w-60 mr-30  font-bold overflow-y-auto leading-12 text-center">
    <ul class="space-y-4">
        <li>
            <a href="{{ route('admin.dashboard') }}"
                class="block px-4 py-2 rounded-md{{ request()->is('admin/dashboard') ? ' bg-red-600 font-semibold' : ' hover:bg-red-700' }} transition"><i
                    class="fa-solid fa-house"></i>Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.scanQr') }}"
                class="block px-3 py-2 rounded-md {{ request()->is('admin/scan-qr') ? 'bg-red-600 font-semibold' : 'hover:bg-red-700' }}">
                <i class="fa-solid fa-qrcode"></i> Scan QR-Code</a>
        </li>
        <ul class="space-y-2">
            <li>
                <a href="{{ route('events.index') }}"
                    class="block px-3 py-2 rounded-md {{ request()->is('events*') ? 'bg-red-600 font-semibold' : 'hover:bg-red-700' }}">
                    <i class="fa-solid fa-calendar-days"></i> All Events
                </a>
            </li>
            <!-- Add more static links here if needed -->
        </ul>
    </ul>
</div>