<!-- Bootstrap Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered justify-content-center">
        <div class="login-modal-content position-relative text-dark">

            <span class="login-modal-close" data-bs-dismiss="modal" aria-label="Close">&times;</span>

            <h2 class="mb-2 text-center">Login</h2>
            <!-- Alert Container for Dynamic Messages -->
            <div id="login-alert-container" class="mb-1"></div>

            <form id="loginForm" method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-1">
                    <label for="email" class="form-label">Email</label>
                    <input class="lowercase form-control" type="email" name="email" id="email"
                        placeholder="example@gmail.com" value="{{ old('email') }}" required>
                </div>

                <div class="form-group position-relative">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" class="form-control" name="password" placeholder="******">
                    </div>
                </div>
                <x-re-captcha />
                <button id="loginSubmitButton" type="submit" class="btn btn-primary w-100 mt-1">Login</button>
            </form>

            <p class="signup-link-container mt-1">
                Don't have an account? <a href="#" id="openSignupFromLogin">Sign Up</a>
            </p>

        </div>
    </div>
</div>