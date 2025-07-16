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

<div id="alert-container"
    style="position: fixed; top: 3rem; right: 1rem; z-index: 50; max-width: 24rem; width: 100%; gap: 0.5rem; margin-top: 60px; pointer-events: none;">
    @foreach ($types as $type)
        @if (session($type))
            @php
                $color = $colors[$type] ?? 'green';
                $icon = $icons[$type] ?? $icons['success'];
            @endphp
            <div id="alert-{{ $type }}-{{ $loop->index}}"
                style="display: flex; align-items: center; padding: 0.75rem; color: {{ $color }}; border: 1px solid {{ $color }}; background-color: {{ $color }}-100; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); backdrop-filter: blur(4px); background-opacity: 0.95; animation: slide-in 0.3s ease-out; pointer-events: auto; opacity: 1; transform: translateX(0);">
                <svg style="flex-shrink: 0; width: 1.25rem; height: 1.25rem; fill: currentColor;" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    {!! $icon !!}
                </svg>
                <div style="margin-left: 0.75rem; font-size: 0.875rem; font-weight: 600; color: {{ $color }}-800;">
                    {{ session($type) }}
                </div>
                <button type="button"
                    style="margin-left: auto; margin-right: -0.375rem; margin-top: -0.375rem; margin-bottom: -0.375rem; background-color: {{ $color }}-50; color: {{ $color }}-600; border-radius: 0.5rem; padding: 0.375rem; display: inline-flex; align-items: center; justify-content: center; height: 2rem; width: 2rem; border: none; cursor: pointer; transition: background-color 0.2s ease; outline: none;"
                    class="dismiss-btn" data-alert-id="alert-{{ $type }}-{{ $loop->index }}" aria-label="Close">
                    <span class="sr-only">Dismiss</span>
                    <svg style="width: 0.75rem; height: 0.75rem; stroke: {{ $color }}-600;" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
            <style>
                #alert-{{ $type }}-{{ $loop->index }}:hover {
                    background-color:
                        {{ $color }}
                        -50;
                    border-color:
                        {{ $color }}
                    ;
                }

                #alert-{{ $type }}-{{ $loop->index }} .dismiss-btn:hover {
                    background-color: rgba(0, 0, 0, 0.05);
                }

                @media (prefers-color-scheme: dark) {
                    #alert-{{ $type }}-{{ $loop->index }} {
                        color:
                            {{ $color }}
                            -200;
                        background-color: #c4cbd6;
                        border-color:
                            {{ $color }}
                            -800;
                    }

                    #alert-{{ $type }}-{{ $loop->index }} .dismiss-btn {
                        background-color: #8997ac;
                        color:
                            {{ $color }}
                            -500;
                    }

                    #alert-{{ $type }}-{{ $loop->index }} .dismiss-btn:hover {
                        background-color: #94a3b8;
                    }
                }
            </style>
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
            style="display: flex; align-items: center; padding: 0.75rem; color: {{ $color }}; border: 1px solid {{ $color }}; background-color: {{ $color }}-100; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); backdrop-filter: blur(4px); background-opacity: 0.95; animation: slide-in 0.3s ease-out; pointer-events: auto; opacity: 1; transform: translateX(0);">
            <svg style="flex-shrink: 0; width: 1.25rem; height: 1.25rem; fill: currentColor;" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                {!! $icon !!}
            </svg>
            <div style="margin-left: 0.75rem; font-size: 0.875rem; font-weight: 600; color: {{ $color }}-800;">
                {{ session('message') }}
                @if (session('error'))
                    <br><small style="color: {{ $color }}-700; font-weight: 500;">{{ session('error') }}</small>
                @endif
            </div>
            <button type="button"
                style="margin-left: auto; margin-right: -0.375rem; margin-top: -0.375rem; margin-bottom: -0.375rem; background-color: {{ $color }}-50; color: {{ $color }}-600; border-radius: 0.5rem; padding: 0.375rem; display: inline-flex; align-items: center; justify-content: center; height: 2rem; width: 2rem; border: none; cursor: pointer; transition: background-color 0.2s ease; outline: none;"
                class="dismiss-btn" data-alert-id="alert-custom-message" aria-label="Close">
                <span class="sr-only">Dismiss</span>
                <svg style="width: 0.75rem; height: 0.75rem; stroke: {{ $color }}-600;" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
            </button>
        </div>
        <style>
            #alert-custom-message:hover {
                background-color:
                    {{ $color }}
                    -50;
                border-color:
                    {{ $color }}
                ;
            }

            #alert-custom-message .dismiss-btn:hover {
                background-color: rgba(0, 0, 0, 0.05);
            }

            @media (prefers-color-scheme: dark) {
                #alert-custom-message {
                    color:
                        {{ $color }}
                        -200;
                    background-color: #c4cbd6;
                    border-color:
                        {{ $color }}
                        -800;
                }

                #alert-custom-message .dismiss-btn {
                    background-color: #8997ac;
                    color:
                        {{ $color }}
                        -500;
                }

                #alert-custom-message .dismiss-btn:hover {
                    background-color: #94a3b8;
                }
            }
        </style>
    @endif

    @if (isset($message) && !session('message'))
        @php
            $viewStatus = $status ?? 1;
            $alertType = $viewStatus == 1 ? 'success' : ($viewStatus == 2 ? 'warning' : 'error');
            $color = $colors[$alertType] ?? 'green';
            $icon = $icons[$alertType] ?? $icons['success'];
        @endphp
        <div id="alert-view-message"
            style="display: flex; align-items: center; padding: 0.75rem; color: {{ $color }}; border: 1px solid {{ $color }}; background-color: {{ $color }}-100; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); backdrop-filter: blur(4px); background-opacity: 0.95; animation: slide-in 0.3s ease-out; pointer-events: auto; opacity: 1; transform: translateX(0);">
            <svg style="flex-shrink: 0; width: 1.25rem; height: 1.25rem; fill: currentColor;" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                {!! $icon !!}
            </svg>
            <div style="margin-left: 0.75rem; font-size: 0.875rem; font-weight: 600; color: {{ $color }}-800;">
                {{ $message }}
                @if (isset($error))
                    <br><small style="color: {{ $color }}-700; font-weight: 500;">{{ $error }}</small>
                @endif
            </div>
            <button type="button"
                style="margin-left: auto; margin-right: -0.375rem; margin-top: -0.375rem; margin-bottom: -0.375rem; background-color: {{ $color }}-50; color: {{ $color }}-600; border-radius: 0.5rem; padding: 0.375rem; display: inline-flex; align-items: center; justify-content: center; height: 2rem; width: 2rem; border: none; cursor: pointer; transition: background-color 0.2s ease; outline: none;"
                class="dismiss-btn" data-alert-id="alert-view-message" aria-label="Close">
                <span class="sr-only">Dismiss</span>
                <svg style="width: 0.75rem; height: 0.75rem; stroke: {{ $color }}-600;" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
            </button>
        </div>
        <style>
            #alert-view-message:hover {
                background-color:
                    {{ $color }}
                    -50;
                border-color:
                    {{ $color }}
                ;
            }

            #alert-view-message .dismiss-btn:hover {
                background-color: rgba(0, 0, 0, 0.05);
            }

            @media (prefers-color-scheme: dark) {
                #alert-view-message {
                    color:
                        {{ $color }}
                    ;
                    background-color: whitesmoke;
                    border-color:
                        {{ $color }}
                    ;
                }

                #alert-view-message .dismiss-btn {
                    background-color: #c4cbd6;
                    color:
                        {{ $color }}
                        -500;
                }

                #alert-view-message .dismiss-btn:hover {
                    background-color: #c4cbd6;
                }
            }
        </style>
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