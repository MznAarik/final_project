<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sign Up</title>
  <style>
    @tailwind utilities;

    /* Modal Background */
    #signupModalUnique {
      display: none;
      /* Hidden by default */
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(6px);
      z-index: 1040;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      box-sizing: border-box;
    }

    /* Modal shown when .show is present */
    #signupModalUnique.show {
      display: flex;
    }

    /* Modal Content Box */
    #signupModalUniqueContent {
      background: white;
      padding: 1.5rem 2rem;
      border-radius: 16px;
      /* width: 90%; */
      max-width: 900px;
      min-height: 650px;
      max-height: none;
      overflow-y: auto;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      animation: slideFadeIn 0.3s ease;
      display: flex;
      flex-direction: column;
    }

    /* Modal Title */
    #signupTitleUnique {
      font-size: 3.5rem;
      text-align: center;
      color: #ff3300;
      font-weight: 700;
      font-family: 'Rakkas';
    }

    /* Close Button */
    #signupCloseButtonUnique {
      position: absolute;
      top: 1rem;
      right: 1rem;
      border: none;
      background: transparent;
      line-height: 1;
      font-size: 1.5rem;
      cursor: pointer;
      color: #888;
      padding: 0.2rem 0.5rem;
      border-radius: 5px;
      outline: none;
    }

    #signupCloseButtonUnique:hover {
      transform: scale(1.15);
      color: #333;
      background-color: #dedbe1;
    }

    /* Form styles */
    #signupFormUnique {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      align-items: flex-start;
    }

    /* Control styling */
    input,
    select,
    textarea {
      padding: 0.4rem;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 1rem;
      box-sizing: border-box;
      width: 100%;
      background-color: white !important;
    }

    input:valid,
    select:valid,
    textarea:valid {
      background-color: white !important;
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    .alreadyLoginUnique {
      grid-column: 2 / 3;
      margin-top: 1rem;
      font-size: 0.95rem;
      text-align: center;
      color: #333;
    }

    button#signupSubmitButtonUnique {
      padding: 0.6rem;
      background-color: hsl(351, 100.00%, 45.10%);
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      transition: background-color 0.3s;
      cursor: pointer;
      width: 100%;
      margin-top: 1rem;
    }

    button#signupSubmitButtonUnique:hover {
      background-color: #cc001f;
    }

    .alreadyLoginUnique a {
      color: #ff3300;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .alreadyLoginUnique a:hover {
      text-decoration: none;
      color: #cc001f;
      text-decoration: underline;
    }

    /* Global Alert Styles */
    #globalAlert {
      display: none;
      position: fixed;
      top: 10px;
      left: 42%;
      transform: translateX(-50%) translateY(-20px);
      padding: 1rem 1.5rem;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
      z-index: 9999;
      font-weight: 600;
      transition: opacity 0.5s ease, transform 0.5s ease;
      opacity: 0;
      max-width: 90vw;
      min-width: 280px;
      box-sizing: border-box;
      text-align: center;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: 1rem;
    }

    #globalAlert.show {
      display: flex;
      opacity: 1;
      transform: translateX(-50%) translateY(0);
    }

    #globalAlert .alert-icon {
      flex-shrink: 0;
      width: 24px;
      height: 24px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
    }

    .alert-success {
      background-color: #d4edda !important;
      color: #155724 !important;
      border: 2px solid #28a745 !important;
      font-weight: bold;
      font-size: 1.1rem;
      text-align: center;
      top: 50px;
    }

    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1.5px solid #f5a5a3;
    }

    .alert-info {
      background-color: #e7f4fb;
      color: #1a4a5b;
      border: 1.5px solid #a6d0eb;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeOut {
      from {
        opacity: 1;
        transform: translateY(0);
      }

      to {
        opacity: 0;
      }
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
      opacity: 1;
      appearance: auto;
      margin: 0;
    }

    @keyframes slideFadeIn {
      from {
        transform: translateY(-20px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .horizontal-date-field {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      margin-top: 1rem;
      margin-left: 5px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .horizontal-date-field label {
      min-width: 110px;
      font-weight: normal;
      color: #000;
    }

    .horizontal-date-field input {
      flex: 1;
    }

    @media (max-width: 768px) {
      #signupModalUniqueContent form.signup-form {
        display: flex;
        flex-direction: column !important;
      }

      #signupModalUniqueContent {
        max-height: 90vh;
        overflow-y: auto;
        padding-bottom: 2rem;
      }

      #signupModalUniqueContent form.signup-form .leftColumnUnique,
      #signupModalUniqueContent form.signup-form .rightColumnUnique {
        width: 100%;
        max-width: 100%;
      }
    }

    #signupFormUnique label {
      color: white;
    }

    @media (max-width: 600px) {
      #eye-icon {
        position: relative;
        bottom: 30px;
        right: 90px;
      }

      #eye-icon-confirm {
        position: relative;
        bottom: 30px;
        right: 90px;
      }
    }
  </style>
</head>

<body>
  <!-- Modal -->
  <div id="signupModalUnique" class="signup-modal" aria-hidden="false" role="dialog" aria-modal="true"
    aria-labelledby="signupTitleUnique">
    <div id="signupModalUniqueContent" class="signup-modal-content" style="position:relative;">
      <button id="signupCloseButtonUnique" class="signup-close" aria-label="close">×</button>
      <h2 id="signupTitleUnique">Sign Up</h2>

      <div id="signupAlertContainerUnique" role="alert" aria-live="polite" aria-atomic="true"></div>

      <form method="POST" action="{{ route('register.submit') }}" class="signup-form" id="signupFormUnique">
        @csrf

        <div class="leftColumnUnique">
          <label for="nameUnique">Name</label>
          <input type="text" name="name" id="nameUnique" placeholder="Enter your name" value="{{ old('name') }}" />

          <label for="genderUnique">Gender</label>
          <select name="gender" id="genderUnique" required>
            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
          </select>

          <label for="phonenoUnique">Phone Number</label>
          <input type="text" name="phoneno" id="phonenoUnique" placeholder="Phone Number" pattern="\d{10}"
            maxlength="10" minlength="10" title="Phone number must be exactly 10 digits" value="{{ old('phoneno') }}"
            oninput="this.value = this.value.replace(/[^0-9]/g, '')" required />

          <label for="addressUnique">Address</label>
          <input type="text" name="address" id="addressUnique" placeholder="Address" required
            value="{{ old('address') }}" />
          <span id="addressError" style="color:red; display:none;overflow:hidden;"></span>

          @php
            $maxDate = \Carbon\Carbon::now()->subYears(16)->format('Y-m-d');
          @endphp

          <div class="horizontal-date-field">
            <label for="dateOfBirthUnique" style="color: black;">Date of Birth</label>
            <input type="date" name="date_of_birth" id="dateOfBirthUnique" max="{{ $maxDate }}"
              value="{{ old('date_of_birth') }}" required oninput="validateAge()" />
            <span id="dobError" style="color:red; font-size: 0.7em;"></span>
          </div>

          <label for="provinceIdUnique">Province Name</label>
          <select name="province_name" id="provinceIdUnique" required>
            <option value="" disabled selected>Select Province</option>
            <option value="Koshi Pradesh" {{ old('province_name') == 'Koshi Pradesh' ? 'selected' : '' }}>Koshi Pradesh
            </option>
            <option value="Madhesh Pradesh" {{ old('province_name') == 'Madhesh Pradesh' ? 'selected' : '' }}>Madhesh
              Pradesh</option>
            <option value="Bagmati Pradesh" {{ old('province_name') == 'Bagmati Pradesh' ? 'selected' : '' }}>Bagmati
              Pradesh</option>
            <option value="Gandaki Pradesh" {{ old('province_name') == 'Gandaki Pradesh' ? 'selected' : '' }}>Gandaki
              Pradesh</option>
            <option value="Lumbini Pradesh" {{ old('province_name') == 'Lumbini Pradesh' ? 'selected' : '' }}>Lumbini
              Pradesh</option>
            <option value="Karnali Pradesh" {{ old('province_name') == 'Karnali Pradesh' ? 'selected' : '' }}>Karnali
              Pradesh</option>
            <option value="Sudurpashchim Pradesh" {{ old('province_name') == 'Sudurpashchim Pradesh' ? 'selected' : '' }}>
              Sudurpashchim Pradesh</option>
          </select>

          <label for="districtIdUnique">District Name</label>
          <select name="district_name" id="districtIdUnique" required>
            <option value="" disabled selected>Select District</option>
          </select>

          <label for="countryIdUnique">Country Name</label>
          <input type="text" name="country_name" id="countryIdUnique" placeholder="Country Name" value="Nepal" readonly
            title="Please write correct country." />
        </div>

        <div class="rightColumnUnique">
          <label for="emailUnique">Email</label>
          <input type="email" name="email" id="emailUnique" placeholder="Enter your email" value="{{ old('email') }}"
            required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please enter a valid email address."
            style="text-transform: none" />

          <label for="passwordUnique">Password</label>
          <input style="text-transform: none;" type="password" name="password" id="passwordUnique"
            placeholder="Enter password">
          <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 cursor-pointer"
            onclick="togglePasswordVisibility()" style="position: relative; bottom: 30px; left: 370px;">
            <i id="eye-icon" class="fas fa-eye-slash"></i>
          </span>

          <label for="passwordConfirmationUnique">Confirm Password</label>
          <input style="text-transform: none" type="password" name="password_confirmation"
            id="passwordConfirmationUnique" placeholder="Confirm Password" required />
          <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 cursor-pointer"
            onclick="confirmTogglePasswordVisibility()" style="position: relative; bottom: 30px; left: 370px;">
            <i id="eye-icon-confirm" class="fas fa-eye-slash"></i>
          </span>
          {{-- captcha --}}

          <div class="captcha-box">
            <div class="flex gap-2 mb-2">
              <img src="{{ url('/captcha') }}?{{ time() }}" id="captchaImageSignup" alt="CAPTCHA"
                class="h-15 border-2 border-gray-300 rounded-xl shadow-sm bg-white">

              <button type="button" onclick="refreshCaptcha1()"
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

          <button type="submit" id="signupSubmitButtonUnique">Sign Up</button>

          <div class="alreadyLoginUnique">
            <p>Already have an account? <a href="#" id="openLoginModal">Login</a></p>
          </div>
        </div>

        <input type="hidden" name="role" value="user" />
      </form>
    </div>
  </div>
  <div id="globalAlert" style="display: none;"></div>

  <script>
    function refreshCaptcha1() {
      document.getElementById('captchaImageSignup').src =
        "{{ url('/captcha') }}?" + new Date().getTime();
    }

    // refreshCaptcha();

    // Province-District data for Nepal
    const provinceDistrictData = {
      "Koshi Pradesh": [
        "Bhojpur", "Dhankuta", "Ilam", "Jhapa", "Khotang", "Morang",
        "Okhaldhunga", "Panchthar", "Sankhuwasabha", "Solukhumbu",
        "Sunsari", "Taplejung", "Terhathum", "Udayapur"
      ],
      "Madhesh Pradesh": [
        "Bara", "Dhanusha", "Mahottari", "Parsa", "Rautahat",
        "Saptari", "Sarlahi", "Siraha"
      ],
      "Bagmati Pradesh": [
        "Bhaktapur", "Chitwan", "Dhading", "Dolakha", "Kathmandu",
        "Kavrepalanchok", "Lalitpur", "Makwanpur", "Nuwakot",
        "Ramechhap", "Rasuwa", "Sindhuli", "Sindhupalchok"
      ],
      "Gandaki Pradesh": [
        "Baglung", "Gorkha", "Kaski", "Lamjung", "Manang",
        "Mustang", "Myagdi", "Nawalpur", "Parbat", "Syangja", "Tanahun"
      ],
      "Lumbini Pradesh": [
        "Arghakhanchi", "Banke", "Bardiya", "Dang", "Gulmi",
        "Kapilvastu", "Palpa", "Parasi", "Pyuthan", "Rolpa",
        "Rukum East", "Rupandehi"
      ],
      "Karnali Pradesh": [
        "Dailekh", "Dolpa", "Humla", "Jajarkot", "Jumla",
        "Kalikot", "Mugu", "Rukum West", "Salyan", "Surkhet"
      ],
      "Sudurpashchim Pradesh": [
        "Achham", "Baitadi", "Bajhang", "Bajura", "Dadeldhura",
        "Darchula", "Doti", "Kailali", "Kanchanpur"
      ]
    };

    const form = document.getElementById('signupFormUnique');
    const modal = document.getElementById('signupModalUnique');
    const alertBox = document.getElementById('globalAlert');
    const provinceSelect = document.getElementById('provinceIdUnique');
    const districtSelect = document.getElementById('districtIdUnique');

    // Initialize form validation scripts
    const address = document.getElementById('addressUnique');
    const error = document.getElementById('addressError');

    address.addEventListener('input', () => {
      if (address.value.length <= 3) {
        error.style.display = 'block';
        address.setCustomValidity("Please write the complete address.");
      } else {
        error.style.display = 'none';
        address.setCustomValidity('');
      }
    });

    function validateAge() {
      const dobInput = document.getElementById('dateOfBirthUnique');
      const errorSpan = document.getElementById('dobError');
      const selectedDate = new Date(dobInput.value);
      const today = new Date();
      const age = today.getFullYear() - selectedDate.getFullYear();
      const monthDiff = today.getMonth() - selectedDate.getMonth();
      const dayDiff = today.getDate() - selectedDate.getDate();

      let isUnder16 = age < 16 || (age === 16 && (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)));

      if (isUnder16) {
        errorSpan.textContent = "You must be at least 16 years old.";
        dobInput.setCustomValidity("You must be at least 16 years old.");
      } else {
        errorSpan.textContent = "";
        dobInput.setCustomValidity("");
      }
    }

    // Province-District functionality
    provinceSelect.addEventListener('change', function () {
      const selectedProvince = this.value;
      const districts = provinceDistrictData[selectedProvince] || [];

      // Clear existing district options
      districtSelect.innerHTML = '<option value="" disabled selected>Select District</option>';

      // Add districts for selected province
      districts.forEach(district => {
        const option = document.createElement('option');
        option.value = district;
        option.textContent = district;
        districtSelect.appendChild(option);
      });

      // Enable district dropdown
      districtSelect.disabled = false;
    });

    // Password validation and visibility toggle
    const password = document.getElementById('passwordUnique');
    const confirmPassword = document.getElementById('passwordConfirmationUnique');

    confirmPassword.addEventListener('input', function () {
      if (confirmPassword.value !== password.value) {
        confirmPassword.setCustomValidity("Passwords do not match.");
      } else {
        confirmPassword.setCustomValidity("");
      }
    });

    function togglePasswordVisibility(e) {
      const passwordInput = document.getElementById('passwordUnique');
      const eyeIcon = document.getElementById('eye-icon');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      }
    }

    function confirmTogglePasswordVisibility(e) {
      const confirmPasswordInput = document.getElementById('passwordConfirmationUnique');
      const eyeIcon = document.getElementById('eye-icon-confirm');
      if (confirmPasswordInput.type === 'password') {
        confirmPasswordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      } else {
        confirmPasswordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      }
    }

    // Form submission
    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      const submitBtn = document.getElementById('signupSubmitButtonUnique');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Signing up...';

      const formData = new FormData(form);

      try {
        const res = await fetch(form.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: formData
        });

        const data = await res.json();

        if (data.success) {
          modal.classList.remove('show');
          showAlert('Please check your email for verification');
        } else if (data.errors) {
          let msg = '';
          Object.values(data.errors).forEach(err => { msg += `<div>${err}</div>`; });
          showAlert(msg, 'danger');

          // If backend sends a new captcha URL, update it
          if (data.new_captcha_url) {
            document.getElementById('captchaImageSignup').src = data.new_captcha_url;
          }
        } else if (data.message) {
          showAlert(data.message, 'danger');

          // Replace captcha if backend provides it
          if (data.new_captcha_url) {
            document.getElementById('captchaImageSignup').src = data.new_captcha_url;
          }
        } else {
          showAlert('Unexpected error occurred.', 'danger');
        }

      } catch (err) {
        showAlert('Server error. Try again.', 'danger');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Sign Up';
      }
    });

    function showAlert(message, type = 'success') {
      alertBox.innerHTML = message;
      alertBox.className = '';
      alertBox.classList.add('alert-' + type);
      alertBox.classList.add('show');

      if (type === 'success') {
        alertBox.style.background = '#d4edda';
        alertBox.style.color = '#155724';
      } else if (type === 'danger') {
        alertBox.style.background = '#f8d7da';
        alertBox.style.color = '#721c24';
      } else {
        alertBox.style.background = '#d1ecf1';
        alertBox.style.color = '#0c5460';
      }

      alertBox.style.display = 'block';
      alertBox.style.opacity = '0';
      alertBox.style.transform = 'translateY(-20px)';

      setTimeout(() => {
        alertBox.style.opacity = '1';
        alertBox.style.transform = 'translateY(0)';
      }, 10);

      setTimeout(() => {
        alertBox.style.opacity = '0';
        alertBox.style.transform = 'translateY(-20px)';
        setTimeout(() => {
          alertBox.style.display = 'none';
          alertBox.classList.remove('show');
        }, 500);
      }, 4000);
    }

    function clearSignupForm() {
      const form = document.getElementById('signupFormUnique');
      form.reset();
      document.getElementById('addressError').style.display = 'none';
      document.getElementById('dobError').textContent = '';
      // Reset district dropdown
      districtSelect.innerHTML = '<option value="" disabled selected>Select District</option>';
      districtSelect.disabled = true;
    }

    document.getElementById('signupCloseButtonUnique').addEventListener('click', () => {
      const modal = document.getElementById('signupModalUnique');
      modal.classList.remove('show');
      clearSignupForm();
    });

    // Initialize district dropdown as disabled
    districtSelect.disabled = true;
  </script>

  <!-- Font Awesome for eye icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>

</html>