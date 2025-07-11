@php
    $types = ['success', 'error', 'warning', 'info'];
    $colors = [
        'success' => 'green',
        'error' => 'red',
        'warning' => 'amber',
        'info' => 'blue',
    ];
    $icons = [
        'success' => '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>',
        'error' => '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>',
        'warning' => '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>',
        'info' => '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>'
    ];
@endphp

<div id="alert-container" class="fixed top-12 right-4 z-50 max-w-sm w-full space-y-2" style="margin-top: 60px;">
    @foreach ($types as $type)
        @if (session($type))
            @php
                $color = $colors[$type] ?? 'green'; // Ensure a valid color
                $icon = $icons[$type] ?? $icons['success'];
            @endphp
            <div id="alert-{{ $type }}-{{ $loop->index }}"
                class="flex items-center p-3 text-{{ $color }}-800 border border-{{ $color }}-300 bg-{{ $color }}-100 rounded-lg shadow-lg backdrop-blur-sm bg-opacity-95 dark:text-{{ $color }}-200 dark:bg-slate-200 dark:border-{{ $color }}-800 animate-slide-in"
                role="alert" style="color: {{ $color }}; background-color: {{ $color }}-100;">
                <svg class="shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    viewBox="0 0 20 20">
                    {!! $icon !!}
                </svg>
                <div class="ms-3 text-sm font-semibold" style="color: {{ $color }}-800;">
                    {{ session($type) }}
                </div>
                <button type="button"
                    class="ms-auto -mx-1.5 -my-1.5 bg-{{ $color }}-50 text-{{ $color }}-600 rounded-lg focus:ring-2 focus:ring-{{ $color }}-400 p-1.5 hover:bg-{{ $color }}-200 inline-flex items-center justify-center h-8 w-8 dark:bg-slate-300 dark:text-{{ $color }}-500 dark:hover:bg-slate-400 transition-colors dismiss-btn"
                    data-alert-id="alert-{{ $type }}-{{ $loop->index }}" aria-label="Close">
                    <span class="sr-only">Dismiss</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
        @endif
    @endforeach

    @if (session('message'))
        @php
            $status = session('status', 1);
            $alertType = $status == 1 ? 'success' : ($status == 2 ? 'warning' : 'error');
            $color = $colors[$alertType] ?? 'green';
            $icon = $icons[$alertType] ?? $icons['success'];
        @endphp
        <div id="alert-custom-message"
            class="flex items-center p-3 text-{{ $color }}-800 border border-{{ $color }}-300 bg-{{ $color }}-100 rounded-lg shadow-lg backdrop-blur-sm bg-opacity-95 dark:text-{{ $color }}-200 dark:bg-slate-200 dark:border-{{ $color }}-800 animate-slide-in"
            role="alert" style="color: {{ $color }}; background-color: {{ $color }}-100;">
            <svg class="shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
                {!! $icon !!}
            </svg>
            <div class="ms-3 text-sm font-semibold" style="color: {{ $color }}-800;">
                {{ session('message') }}
                @if (session('error'))
                    <br><small class="text-{{ $color }}-700 font-medium">{{ session('error') }}</small>
                @endif
            </div>
            <button type="button"
                class="ms-auto -mx-1.5 -my-1.5 bg-{{ $color }}-50 text-{{ $color }}-600 rounded-lg focus:ring-2 focus:ring-{{ $color }}-400 p-1.5 hover:bg-{{ $color }}-200 inline-flex items-center justify-center h-8 w-8 dark:bg-slate-300 dark:text-{{ $color }}-500 dark:hover:bg-slate-400 transition-colors dismiss-btn"
                data-alert-id="alert-custom-message" aria-label="Close">
                <span class="sr-only">Dismiss</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
            </button>
        </div>
    @endif

    @if (isset($message) && !session('message'))
        @php
            $viewStatus = $status ?? 1;
            $alertType = $viewStatus == 1 ? 'success' : ($viewStatus == 2 ? 'warning' : 'error');
            $color = $colors[$alertType] ?? 'green';
            $icon = $icons[$alertType] ?? $icons['success'];
        @endphp
        <div id="alert-view-message"
            class="flex items-center p-3 text-{{ $color }}-800 border border-{{ $color }}-300 bg-{{ $color }}-100 rounded-lg shadow-lg backdrop-blur-sm bg-opacity-95 dark:text-{{ $color }}-200 dark:bg-slate-200 dark:border-{{ $color }}-800 animate-slide-in"
            role="alert" style="color: {{ $color }}; background-color: {{ $color }}-100;">
            <svg class="shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
                {!! $icon !!}
            </svg>
            <div class="ms-3 text-sm font-semibold" style="color: {{ $color }}-800;">
                {{ $message }}
                @if (isset($error))
                    <br><small class="text-{{ $color }}-700 font-medium">{{ $error }}</small>
                @endif
            </div>
            <button type="button"
                class="ms-auto -mx-1.5 -my-1.5 bg-{{ $color }}-50 text-{{ $color }}-600 rounded-lg focus:ring-2 focus:ring-{{ $color }}-400 p-1.5 hover:bg-{{ $color }}-200 inline-flex items-center justify-center h-8 w-8 dark:bg-slate-300 dark:text-{{ $color }}-500 dark:hover:bg-slate-400 transition-colors dismiss-btn"
                data-alert-id="alert-view-message" aria-label="Close">
                <span class="sr-only">Dismiss</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
            </button>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('#alert-container > div');
        alerts.forEach(function (alert) {
            // Start auto-dismiss timer
            const timer = setTimeout(function () {
                if (alert.parentElement) {
                    alert.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(100%)';
                    setTimeout(function () {
                        alert.remove();
                    }, 300);
                }
            }, 5000); // Auto-dismiss after 5 seconds
        });

        // Add click event for dismiss buttons
        document.querySelectorAll('.dismiss-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const alertId = this.getAttribute('data-alert-id');
                const alert = document.getElementById(alertId);
                if (alert) {
                    alert.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(100%)';
                    setTimeout(function () {
                        alert.remove();
                    }, 300); // Match the transition duration
                }
            });
        });
    });
</script>

<style>
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }

    #alert-container {
        pointer-events: none;
        z-index: 50;
        /* Ensure above navbar and background */
    }

    #alert-container>div {
        pointer-events: auto;
    }

    @media (max-width: 640px) {

        #alert-container {
            top: 10px;
            margin-top: 20px;
            right: 10px;
            max-width: 90%;
        }
    }
</style>