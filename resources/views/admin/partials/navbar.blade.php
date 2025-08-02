<style>
    .top-nav {
        position: fixed;
        width: 100%;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background: linear-gradient(90deg, #860303, #ff3300);
        color: white;
        font-size: 0.9rem;
        font-weight: 500;
        position: sticky;
        top: 50px;
        /* Below the navbar */
        z-index: 998;
    }

    .breadcrumb a {
        color: white;
        text-decoration: none;
        margin: 0 0.5rem;
        transition: color 0.3s ease;
    }

    .breadcrumb a:hover {
        color: #ffd700;
    }

    .breadcrumb .separator {
        margin: 0 0.5rem;
    }
</style>

<nav class="top-nav">
    <a href="{{ route('admin.dashboard') }}" class="logo flex items-center justify-center" style="gap: 10px;">
        <svg class="w-10 h-10 text-white text-3xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
            </path>
        </svg>
        <span class="text-white text-3xl font-semibold">BooKets</span>
    </a>
    <!-- Breadcrumb -->
    <div style="display:flex; align-items: center; justify-content: center; gap:15px">

        <a href="{{ route('admin.dashboard') }}" class="logo"> Admin Panel</a>

        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'text-red-600 font-bold' : 'hover:text-red-500' }}">
                Dashboard</a>
            @if (request()->is('admin/scan-qr'))
                <span class="separator">/</span>
                <a href="{{ route('admin.scanQr') }}"
                    class="{{ request()->routeIs('admin.scanQr') ? 'text-red-600 font-bold' : 'hover:text-red-500' }}">Scan
                    QR-Code</a>
            @endif
            @if (request()->is('events*'))
                <span class="separator">/</span>
                <a href="{{ route('events.index') }}"
                    class="{{ request()->routeIs('events.index') ? 'text-red-600 font-bold' : 'hover:text-red-500' }}">All
                    Events</a>
                @if (request()->routeIs('events.create'))
                    <span class="separator">/</span>
                    <a href="{{ route('events.create') }}"
                        class="{{ request()->routeIs('events.create') ? 'text-red-600 font-bold' : 'hover:text-red-500' }} mt-10">
                        Create Event
                    </a>
                @endif
            @endif
            @if (request()->is('admin/users*'))
                <span class="separator">/</span>
                <a href="{{ route('users.index') }}"
                    class="{{ request()->routeIs('users.index') ? 'text-red-600 font-bold' : 'hover:text-red-500' }}">
                    All Users
                </a>
            @endif
        </div>
    </div>

    <!-- Hamburger button for mobile -->
    <button class="hamburger" aria-label="Toggle menu">☰</button>



    <!-- Desktop Navigation -->
    <ul class="nav-links desktop-nav">
        <li class="user-dropdown">
            @if(Auth::check())
                <a href="#" onclick="toggleDropdown(event)"><i class="fa fa-user-check"></i></a>
                <div class="dropdown-menu">
                    <a href="{{ url('profile') }}" class="dropdown-button block py-1"><i
                            class="fa fa-user mr-2"></i>{{ Auth::user()->name }}</a>
                    <form action="{{ route('logout') }}" id="logout-form" style="display: none;">@csrf</form>
                    <a href="#" class="dropdown-button block py-1"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            @else
                <a href="#" onclick="toggleDropdown(event)"><i class="fa fa-user"></i></a>
                <div class="dropdown-menu">
                    <a href="#" class="dropdown-button" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fa fa-sign-in-alt"></i> Login
                    </a>
                    <div class="dropdown-or">OR</div>
                    <a href="#" class="dropdown-button open-signup-btn">
                        <i class="fa fa-user-plus"></i> Signup
                    </a>
            @endif
        </li>
    </ul>

    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="search-container">
            <input type="text" placeholder="Search..." class="search-bar" name="search" />
        </div>
        <ul class="nav-links">
            @if(Auth::check())
                <li><a href="{{ url('profile') }}"><i class="fa fa-user"></i> Welcome, {{ Auth::user()->name }}</a></li>
                <li>
                    <form action="{{ route('logout') }}" id="mobile-logout-form" style="display: none;">@csrf</form>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">
                        <i class="fa fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            @else
                <li><a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fa fa-sign-in-alt"></i>
                        Login</a></li>
                <li><a href="#" class="open-signup-btn" data-bs-toggle="modal" data-bs-target="#signupModal"><i
                            class="fa fa-user-plus"></i> Signup</a></li>
            @endif
        </ul>
    </div>
</nav>