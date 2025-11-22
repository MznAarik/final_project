<div class="captcha-box">
    <div class="flex gap-2 mb-2">
        <img src="{{ url('/captcha') }}?{{ time() }}" id="captchaImageLogin" alt="CAPTCHA"
            class="h-15 border-2 border-gray-300 rounded-xl shadow-sm bg-white">

        <button type="button" onclick="refreshCaptcha()"
            class="group flex items-center gap-2 text-orange-600 hover:text-orange-700 font-medium text-sm transition-all duration-200">

            <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>

            <span class="tracking-wider">Refresh</span>
        </button>
    </div>
    <input type="text" name="captcha" placeholder="Enter code" required
        class="px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none focus:ring-4 focus:ring-orange-100 transition">
</div>

<script>
    function refreshCaptcha(newUrl = null) {
        const img = document.getElementById('captchaImageLogin');
        img.src = (newUrl || "{{ url('/captcha') }}") + "?" + Date.now();
    }

    // Login form submission
    document.addEventListener("DOMContentLoaded", () => {
        const loginForm = document.getElementById("loginForm");
        if (!loginForm) return;

        const loginBtn = document.getElementById("loginSubmitButton");
        const alertContainer = document.getElementById("login-alert-container");

        loginForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            if (loginBtn) {
                loginBtn.disabled = true;
                loginBtn.textContent = "Logging in...";
            }

            if (alertContainer) alertContainer.innerHTML = "";

            const formData = new FormData(loginForm);

            try {
                const res = await fetch(loginForm.action, {
                    method: "POST",
                    body: formData,
                    credentials: "same-origin", // important for session cookie
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                    }
                });

                const data = await res.json();

                if (data.success) {
                    if (alertContainer)
                        alertContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;

                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);

                } else {
                    // Show error message
                    if (alertContainer)
                        alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;

                    // Refresh captcha if provided
                    if (data.new_captcha_url) {
                        refreshCaptcha(data.new_captcha_url);
                    } else {
                        refreshCaptcha();
                    }

                    if (loginBtn) {
                        loginBtn.disabled = false;
                        loginBtn.textContent = "Login";
                    }
                }

            } catch (error) {
                if (alertContainer)
                    alertContainer.innerHTML = `<div class="alert alert-danger">Something went wrong. Please try again.</div>`;
                if (loginBtn) {
                    loginBtn.disabled = false;
                    loginBtn.textContent = "Login";
                }
                console.error("Login error:", error);
            }
        });
    });
</script>