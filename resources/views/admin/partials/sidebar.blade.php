<style>
    .sidebar-icon {
        margin: 0 10px 0 40px;
    }
</style>
<div class="fixed top-[100px] left-0 w-60 mr-30  font-bold overflow-y-auto leading-12 text-justify">
    <ul class="space-y-4">
        <li>
            <a href="{{ route('admin.dashboard') }}"
                class="block px-4 py-2 rounded-md{{ request()->is('admin/dashboard') ? ' bg-red-600 font-semibold' : ' hover:bg-red-700' }} transition"><i
                    class="sidebar-icon fa-solid fa-house"></i>Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.scanQr') }}"
                class="block px-3 py-2 rounded-md {{ request()->is('admin/scan-qr') ? 'bg-red-600 font-semibold' : 'hover:bg-red-700' }}">
                <i class="sidebar-icon fa-solid fa-qrcode"></i> Scan QR-Code</a>
        </li>
        <li>
            <a href="{{ route('events.index') }}"
                class="block px-3 py-2 rounded-md {{ request()->is('events*') ? 'bg-red-600 font-semibold' : 'hover:bg-red-700' }}">
                <i class="sidebar-icon fa-solid fa-calendar-days"></i> All Events
            </a>
        </li>
        <li>
            <a href="{{ url('all-tickets') }}"
                class="block px-3 py-2 rounded-md {{ request()->is('admin/tickets*') ? 'bg-red-600 font-semibold' : 'hover:bg-red-700' }}">
                <i class="sidebar-icon fa-solid fa-ticket"></i> All Tickets
            </a>
        </li>
        <li>
            <a href="{{ route('users.index') }}"
                class="block px-3 py-2 rounded-md {{ request()->is('admin/users*') ? 'bg-red-600 font-semibold' : 'hover:bg-red-700' }}">
                <i class="sidebar-icon fa-solid fa-user"></i> All Users
            </a>
        </li>
    </ul>
</div>