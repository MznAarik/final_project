<div class="flex items-center justify-center mb-5">
    <img id="captcha-img" alt="captcha" style="pointer-events:none;">
    <button type="button" onclick="refreshCaptcha()" class="text-3xl hover:shadow-md hover:bg-gray-600">🔃</button>
</div>

<input type="text" name="captcha" placeholder="Enter CAPTCHA" required>

<script>
    function loadCaptcha() {
        document.getElementById('captcha-img').src =
            "{{ route('captcha.generate') }}" + '?t=' + Date.now();
    }

    function refreshCaptcha() {
        loadCaptcha();
    }
    window.onload = loadCaptcha;
</script>
