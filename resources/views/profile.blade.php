@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-rose-50 via-red-50 to-pink-50 py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section with Avatar -->
            <div class="relative mb-8">
                <div
                    class="bg-gradient-to-r from-red-600 via-rose-600 to-pink-600 rounded-3xl p-8 shadow-2xl overflow-hidden">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="relative flex flex-col md:flex-row items-center gap-6">
                        <!-- Avatar Section -->
                        <div class="relative group">
                            <div
                                class="w-32 h-32 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center shadow-xl border-4 border-white/30 group-hover:scale-105 transition-transform duration-300 overflow-hidden">
                                <!-- Dynamic Avatar with User Initials -->
                                <div class="w-full h-full bg-gradient-to-br from-red-400 to-rose-500 flex items-center justify-center">
                                    <span class="text-4xl font-bold text-white select-none">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                                    </span>
                                </div>
                            </div>
                            <div
                                class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-400 rounded-full border-4 border-white shadow-lg">
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="text-center md:text-left text-white">
                            <h1
                                class="text-4xl font-bold mb-2 bg-gradient-to-r from-white to-red-100 bg-clip-text text-transparent">
                                {{ $user->name }}
                            </h1>
                            <p class="text-red-100 text-lg mb-1">{{ $user->email }}</p>
                            <div class="flex items-center justify-center md:justify-start gap-2 text-red-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $user->country->name }}</span>
                            </div>
                        </div>

                        <!-- Edit Mode Toggle -->
                        <div class="ml-auto">
                            <button id="editToggle"
                                class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-3 rounded-full transition-all duration-300 shadow-lg hover:shadow-xl border border-white/30 group flex items-center gap-2">
                                <svg id="editIcon" class="w-5 h-5 transition-transform group-hover:rotate-12"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.828-2.828z">
                                    </path>
                                </svg>
                                <span id="editText">Edit Profile</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <form id="profileForm" method="POST" action="{{ route('profile.update') }}">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Personal Information -->
                    <div class="lg:col-span-2">
                        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-xl border border-white/20">
                            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-red-500 to-rose-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                Personal Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name Field -->
                                <div class="form-group">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                    <div class="relative">
                                        <input type="text" name="name" value="{{ $user->name }}"
                                            class="editable-field w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:outline-none transition-all duration-300 disabled:bg-transparent disabled:border-transparent disabled:text-gray-800 disabled:font-medium text-lg"
                                            disabled>
                                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Phone Field -->
                                <div class="form-group">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                                    <div class="relative">
                                        <input type="text" name="phoneno" value="{{ $user->phoneno }}"
                                            pattern="^(97|98)\d{8}$" maxlength="10" inputmode="numeric"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            title="Enter a valid 10-digit Nepali number starting with 97 or 98"
                                            class="editable-field w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:outline-none transition-all duration-300 disabled:bg-transparent disabled:border-transparent disabled:text-gray-800 disabled:font-medium text-lg"
                                            disabled>
                                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Address Field -->
                                <div class="form-group md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                                    <div class="relative">
                                        <textarea name="address" rows="3" minlength="3" required
                                            oninput="this.value = this.value.replace(/^\s+/, '')"
                                            title="Address must be at least 3 characters"
                                            class="editable-field w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:outline-none transition-all duration-300 disabled:bg-transparent disabled:border-transparent disabled:text-gray-800 disabled:font-medium text-lg resize-none"
                                            disabled>{{ $user->address }}</textarea>

                                        <div class="absolute right-3 top-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Province Field -->
                                <div class="form-group">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Province</label>
                                    <div class="relative">
                                        <select name="province_id"
                                            class="editable-field w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:outline-none transition-all duration-300 disabled:bg-transparent disabled:border-transparent disabled:text-gray-800 disabled:font-medium text-lg capitalize appearance-none"
                                            style="background-image: none;" disabled>
                                            <option value="{{ $user->province->id }}">{{ $user->province->name }}</option>
                                        </select>
                                    </div>
                                </div>


                                <!-- District Field -->
                                <div class="form-group">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">District</label>
                                    <div class="relative">
                                        <select name="district_id"
                                            class="editable-field w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:outline-none transition-all duration-300 disabled:bg-transparent disabled:border-transparent disabled:text-gray-800 disabled:font-medium text-lg capitalize appearance-none"
                                            style="background-image: none;" disabled>
                                            <option value="{{ $user->district->id }}">{{ $user->district->name }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Read-Only Information -->
                    <div class="space-y-6">
                        <!-- Account Details -->
                        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-xl border border-white/20">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-3">
                                <div
                                    class="w-6 h-6 bg-gradient-to-r from-red-500 to-rose-500 rounded-md flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                Account Details
                            </h3>

                            <div class="space-y-4">
                                <div
                                    class="flex items-center justify-between p-3 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl border border-red-100">
                                    <span class="text-sm font-medium text-gray-600">Gender</span>
                                    <span class="font-semibold text-gray-800">{{ $user->gender }}</span>
                                </div>

                                <div
                                    class="flex items-center justify-between p-3 bg-gradient-to-r from-pink-50 to-red-50 rounded-xl border border-pink-100">
                                    <span class="text-sm font-medium text-gray-600">Date of Birth</span>
                                    <span class="font-semibold text-gray-800">{{ $user->date_of_birth }}</span>
                                </div>

                                <div
                                    class="flex items-center justify-between p-3 bg-gradient-to-r from-rose-50 to-pink-50 rounded-xl border border-rose-100">
                                    <span class="text-sm font-medium text-gray-600">Country</span>
                                    <span class="font-semibold text-gray-800">{{ $user->country->name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div id="actionButtons"
                            class="hidden bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-xl border border-white/20">
                            <div class="space-y-3">
                                <button type="submit"
                                    class="w-full bg-gradient-to-r from-red-600 to-rose-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Save Changes
                                </button>

                                <button type="button" id="cancelEdit"
                                    class="w-full bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-medium shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .form-group {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .editable-field:not(:disabled) {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-color: #e2e8f0;
        }

        .editable-field:not(:disabled):focus {
            background: white;
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        .editable-field:disabled {
            cursor: default;
        }

        /* Floating label effect */
        .form-group label {
            transition: all 0.3s ease;
        }

        .form-group:has(.editable-field:not(:disabled)) label {
            color: #dc2626;
            transform: scale(0.95);
        }

        /* Gradient text animation */
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        h1 {
            background: linear-gradient(-45deg, #ffffff, #fecaca, #fca5a5, #f87171);
            background-size: 400% 400%;
            animation: gradient 3s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glassmorphism effect */
        .bg-white\/80 {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* Avatar initials styling */
        .avatar-initials {
            background: linear-gradient(135deg, #dc2626 0%, #e11d48 50%, #be185d 100%);
            background-size: 200% 200%;
            animation: avatarGradient 4s ease infinite;
        }

        @keyframes avatarGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editToggle = document.getElementById('editToggle');
            const editIcon = document.getElementById('editIcon');
            const editText = document.getElementById('editText');
            const actionButtons = document.getElementById('actionButtons');
            const cancelEdit = document.getElementById('cancelEdit');
            const editableFields = document.querySelectorAll('.editable-field');

            let isEditing = false;
            let originalValues = {};

            // Store original values
            editableFields.forEach(field => {
                originalValues[field.name] = field.value;
            });

            function toggleEditMode() {
                isEditing = !isEditing;

                editableFields.forEach(field => {
                    field.disabled = !isEditing;

                    if (isEditing) {
                        field.classList.add('animate-pulse');
                        setTimeout(() => field.classList.remove('animate-pulse'), 600);
                    }
                });

                if (isEditing) {
                    editText.textContent = 'Editing...';
                    editIcon.style.transform = 'rotate(180deg)';
                    editToggle.classList.add('bg-rose-500', 'text-white');
                    editToggle.classList.remove('bg-white/20');
                    actionButtons.classList.remove('hidden');

                    // Focus first editable field
                    setTimeout(() => {
                        const firstField = document.querySelector('.editable-field:not([disabled])');
                        if (firstField) firstField.focus();
                    }, 300);
                } else {
                    editText.textContent = 'Edit Profile';
                    editIcon.style.transform = 'rotate(0deg)';
                    editToggle.classList.remove('bg-rose-500', 'text-white');
                    editToggle.classList.add('bg-white/20');
                    actionButtons.classList.add('hidden');
                }
            }

            function cancelEditing() {
                // Restore original values
                editableFields.forEach(field => {
                    field.value = originalValues[field.name];
                });

                toggleEditMode();
            }

            editToggle.addEventListener('click', toggleEditMode);
            cancelEdit.addEventListener('click', cancelEditing);

            // Add smooth transitions to form fields
            editableFields.forEach(field => {
                field.addEventListener('focus', function () {
                    this.parentElement.style.transform = 'scale(1.02)';
                    this.parentElement.style.transition = 'transform 0.3s ease';
                });

                field.addEventListener('blur', function () {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Form submission with loading state
            const form = document.getElementById('profileForm');
            form.addEventListener('submit', function (e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalContent = submitBtn.innerHTML;

                submitBtn.innerHTML = `
                    <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                `;
                submitBtn.disabled = true;

                // Simulate processing time
                setTimeout(() => {
                    submitBtn.innerHTML = originalContent;
                    submitBtn.disabled = false;
                }, 2000);
            });

            // Add keyboard shortcuts
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && isEditing) {
                    cancelEditing();
                } else if (e.ctrlKey && e.key === 'e') {
                    e.preventDefault();
                    toggleEditMode();
                } else if (e.ctrlKey && e.key === 's' && isEditing) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            });

            // Add success animation after form submission
            window.addEventListener('pageshow', function (e) {
                if (e.persisted || performance.navigation.type === 2) {
                    // Page was loaded from cache (back/forward navigation)
                    const successMessage = document.createElement('div');
                    successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-500 z-50';
                    successMessage.innerHTML = `
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Profile updated successfully!
                        </div>
                    `;

                    document.body.appendChild(successMessage);

                    setTimeout(() => {
                        successMessage.classList.remove('translate-x-full');
                    }, 100);

                    setTimeout(() => {
                        successMessage.classList.add('translate-x-full');
                        setTimeout(() => successMessage.remove(), 500);
                    }, 3000);
                }
            });
        });
    </script>
@endsection