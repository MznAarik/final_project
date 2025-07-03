<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'EvenTickets')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/event_section.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buy_ticket_cards.css') }}">

    <!-- Use only one version of Font Awesome (latest version is preferred) -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    {{--
    <script src="https://cdn.tailwindcss.com"></script> --}}


    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Your local JS file (make sure it exists in public/js/app.js) -->
    <script src="{{ asset('js/app.js') }}" defer></script>

</head>

<body>
    <div class="app-container">
        @include('partials.header')

        {{-- Include login modal only on login page --}}

        @include('auth.login')


        {{-- Include signup modal only on signup page --}}
        @include('auth.signup')
        @include('components.preview') {{-- modal markup here --}}




        <div id="main-content">
            <main>
                @yield('content')
            </main>
        </div>

        @include('partials.footer')
    </div>

</body>


</html>