<nav class="top-nav">
  <a href="{{ route('home') }}" class="logo flex items-center justify-center space-x-2">
    <svg class="w-10 h-10 text-white text-3xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
      </path>
    </svg>
    <span class="text-white text-3xl font-semibold">BooKets</span>
  </a>

  <!-- Hamburger button for mobile -->
  <button class="hamburger" aria-label="Toggle menu">&#9776;</button>

  <!-- Desktop search -->
  <div class="search-container desktop-search">
    <input type="text" placeholder="Search events..." class="search-bar" />
  </div>

  <!-- Desktop Navigation -->
  <ul class="nav-links desktop-nav">
    <li><a href="{{ route('buy_tickets') }}"><i class="fa fa-ticket-alt"></i> Buy Tickets</a></li>
    <li><a href="{{ route('upcoming') }}"><i class="fa fa-calendar-alt"></i> Upcoming</a></li>
    <li><a href="{{ route('popular') }}"><i class="fa fa-fire"></i> Popular</a></li>
    <li> <a href="{{ route('cart.index') }}"><i class="fa fa-shopping-cart"></i>My
        Cart</a> </li>
    <li class="user-dropdown">
      @if(Auth::check())
      <a href="#" onclick="toggleDropdown(event)"><i class="fa fa-user-check"></i></a>
      <div class="dropdown-menu">

      <a href="{{ url('profile') }}" class="dropdown-button block py-1"><i
        class="fa fa-user mr-2"></i>{{ Auth::user()->name }}</a>

      <a href="{{ route('user.tickets.index') }}" class="dropdown-button block py-1"><i
        class="fa fa-clipboard-list mr-2"></i>My
        Tickets</a>

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
      <input type="text" placeholder="Search events..." class="search-bar" />
    </div>

    <ul class="nav-links">
      <li><a href="{{ route('buy_tickets') }}"><i class="fa fa-ticket-alt"></i> Buy Tickets</a></li>
      <li><a href="{{ route('upcoming') }}"><i class="fa fa-calendar-alt"></i> Upcoming</a></li>
      <li><a href="{{ route('popular') }}"><i class="fa fa-fire"></i> Popular</a></li>
      <li><a href="{{ route('cart.index') }}"><i class="fa fa-shopping-cart"></i> My Cart</a></li>
      <li><a href="{{ route('user.tickets.index') }}"><i class="fa fa-clipboard-list"></i> My Tickets</a></li>

      @if(Auth::check())
      <li><a href="{{ url('profile') }}"><i class="fa fa-user"></i> Welcome, {{ Auth::user()->name }}</a></li>
      <li>
      <form action="{{ route('logout') }}" id="mobile-logout-form" style="display: none;">@csrf</form>
      <a href="#" onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">
        <i class="fa fa-sign-out-alt"></i> Logout
      </a>
      </li>
    @else
      <li><a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fa fa-sign-in-alt"></i> Login</a>
      </li>
      <li><a href="#" class="open-signup-btn" data-bs-toggle="modal" data-bs-target="#signupModal">
        <i class="fa fa-user-plus"></i> Signup
      </a>

      </li>
    @endif
    </ul>
  </div>
</nav>