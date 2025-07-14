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
      display: flex;
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

    /* Modal hidden by default */
    #signupModalUnique:not(.show) {
      display: none;
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
      /* Adjusted gap for better spacing */
      align-items: flex-start;
      /* Align items to the start to reduce vertical space */
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
      /* force white background */
    }

    /* Remove or override these if you don't want green highlight for valid fields */
    input:valid,
    select:valid,
    textarea:valid {
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
      /* Align it to the right column */
      margin-top: 1rem;
      /* Add some space above */
      font-size: 0.95rem;
      text-align: center;
      /* Align text to the right */
      color: #333;
      /* Change color to a desired shade */
    }

    .alreadyLoginUnique {}

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
      /* Ensures space above the button */
    }

    button#signupSubmitButtonUnique:hover {
      background-color: #cc001f;
    }

    .alreadyLoginUnique {
      color: #000;
      /* Black text */
      font-size: 0.95rem;
      text-align: center;
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
      /* Hidden by default */
      position: fixed;
      top: 10px;
      /* Position from the top */
      left: 45%;
      /* Center horizontally */
      transform: translateX(-50%) translateY(-20px);
      /* Center it and start slightly above */
      padding: 1rem 1.5rem;
      /* Padding for the alert */
      border-radius: 8px;
      /* Rounded corners */
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      /* Shadow for depth */
      z-index: 9999;
      /* Ensure it appears above other elements */
      font-weight: bold;
      /* Bold text for visibility */
      transition: opacity 0.5s ease, transform 0.5s ease;
      /* Smooth fade and slide effect */
      opacity: 0;
      /* Start as invisible */
    }

    /* Show alert */
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

      /* Flex to align icon and text horizontally */
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

    /* Example alert types */

    .alert-success {
      background-color: #e6f4ea;
      color: #2f6f3f;
      border: 1.5px solid #a3d3a1;
    }

    .alert-danger {
      background-color: #fdecea;
      color: #8b1a1a;
      border: 1.5px solid #f5a5a3;
    }

    .alert-info {
      background-color: #e7f4fb;
      color: #1a4a5b;
      border: 1.5px solid #a6d0eb;
    }

    .alert-success {
      background-color: #d4edda !important;
      /* your green background */
      color: #155724 !important;
      /* your green text */
      border: 2px solid #28a745 !important;
      font-weight: bold;
      font-size: 1.1rem;
      text-align: center;
      top: 50px;

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
        /* or any height you want */
        overflow-y: auto;
        padding-bottom: 2rem;
        /* optional, spacing at bottom */
      }

      #signupModalUniqueContent form.signup-form .leftColumnUnique,
      #signupModalUniqueContent form.signup-form .rightColumnUnique {
        width: 100%;
        max-width: 100%;
      }
    }

    #signupFormUnique label {
      color: white;
      /* Make the labels white */
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
  @php
  $shouldShowSignup = session('message') || $errors->any();
@endphp
  <div id="signupModalUnique" class="signup-modal {{ $shouldShowSignup ? 'show' : '' }}" aria-hidden="true"
    role="dialog" aria-modal="true" aria-labelledby="signupTitleUnique">

    <div id="signupModalUniqueContent" class="signup-modal-content" style="position:relative;">
      <button id="signupCloseButtonUnique" class="signup-close" aria-label="close">&times;</button>
      <h2 id="signupTitleUnique">Sign Up</h2>

      @if (session('message'))
      <div class="alert alert-{{ session('status') ?: 'info' }}">
      {{ session('message') }}
      </div>
    @endif

      @if ($errors->any())
      <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
      </ul>
      </div>
    @endif

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
          <input type="text" name="address" id="addressUnique" minlength="10" placeholder="Address" required
            value="{{ old('address') }}" />
          <span id="addressError" style="color:red; display:none;overflow:hidden;"></span>

          <script>
            const address = document.getElementById('addressUnique');
            const error = document.getElementById('addressError');

            address.addEventListener('input', () => {
              if (address.value.length <= 5) {
                error.style.display = 'block';
                address.setCustomValidity("Please write the complete address.");
              } else {
                error.style.display = 'none';
                address.setCustomValidity('');
              }
            });
          </script>

          @php
      $maxDate = \Carbon\Carbon::now()->subYears(16)->format('Y-m-d');
    @endphp

          <div class="horizontal-date-field">
            <label for="dateOfBirthUnique" style="color: black;">Date of Birth</label>
            <input type="date" name="date_of_birth" id="dateOfBirthUnique" max="{{ $maxDate }}"
              value="{{ old('date_of_birth') }}" required oninput="validateAge()" />
            <span id="dobError" style="color:red; font-size: 0.7em;"></span>
          </div>

          <script>
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
          </script>
          <label for="districtIdUnique">District Name</label>
          <input type="text" name="district_name" id="districtIdUnique" placeholder="District Name"
            value="{{ old('district_name') }}" required title=" Please write correct district." />

          <label for="provinceIdUnique">Province Name</label>
          <input type="text" name="province_name" id="provinceIdUnique" placeholder="Province Name"
            value="{{ old('province_name') }}" required title="Please write correct province." />

          <label for="countryIdUnique">Country Name</label>
          <input type="text" name="country_name" id="countryIdUnique" placeholder="Country Name" value="Nepal" readonly
            title="Please write correct country." />
        </div>

        <div class="rightColumnUnique ">
          <label for="emailUnique">Email</label>
          <input type="email" name="email" id="emailUnique" placeholder="Enter your email" value="{{ old('email') }}"
            required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please enter a valid email address."
            style="text-transform: none" />

          <label for="passwordUnique">Password</label>
          <input style="text-transform: none;" type="password" name="password" id="passwordUnique"
            placeholder="Enter password">
          <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 cursor-pointer"
            onclick="togglePasswordVisibility()" style="position: relative; bottom: 30px; left: 370px; ">
            <i id="eye-icon" class="fas fa-eye-slash"></i>
          </span>

          <label for="passwordConfirmationUnique">Confirm Password</label>
          <input style="text-transform: none" type="password" name="password_confirmation"
            id="passwordConfirmationUnique" placeholder="Confirm Password" required />
          <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 cursor-pointer"
            onclick="confirmTogglePasswordVisibility()" style="position: relative; bottom: 30px; left: 370px;">
            <i id="eye-icon-confirm" class="fas fa-eye-slash"></i>
          </span>

          <script>
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
          </script>


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
    const form = document.getElementById('signupFormUnique');
    const modal = document.getElementById('signupModalUnique');
    const alertBox = document.getElementById('globalAlert');

    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      // Disable submit button and show loading text immediately
      const submitBtn = document.getElementById('signupSubmitButtonUnique');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Signing up...';

      const formData = new FormData(form);

      try {
        const res = await fetch(form.action, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: formData
        });

        const data = await res.json();

        if (data.success) {
          modal.classList.remove('show');
          showAlert('Please check your email for verification');
          // Optional: redirect or reset form
        } else if (data.errors) {
          let msg = '';
          Object.values(data.errors).forEach(err => {
            msg += `<div>${err}</div>`;
          });
          showAlert(msg, 'danger');
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
      alertBox.className = ''; // Clear existing classes
      alertBox.classList.add('alert-' + type);
      alertBox.classList.add('show'); // Add show class for visibility

      // Set the background color based on the alert type
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

      // Show the alert with animation
      alertBox.style.display = 'block'; // Show the alert
      alertBox.style.opacity = '0'; // Start invisible
      alertBox.style.transform = 'translateY(-20px)'; // Start slightly above

      // Trigger the fade-in effect
      setTimeout(() => {
        alertBox.style.opacity = '1'; // Fade in
        alertBox.style.transform = 'translateY(0)'; // Move to position
      }, 10); // Small timeout to trigger the animation

      setTimeout(() => {
        alertBox.style.opacity = '0'; // Fade out
        alertBox.style.transform = 'translateY(-20px)'; // Move up
        setTimeout(() => {
          alertBox.style.display = 'none'; // Hide after animation
          alertBox.classList.remove('show'); // Remove show class
        }, 500); // Match this with the fade-out duration
      }, 4000); // Display duration before fading out
    }
    function clearSignupForm() {
      const form = document.getElementById('signupFormUnique');
      form.reset();
      document.getElementById('addressError').style.display = 'none';
      document.getElementById('dobError').textContent = '';
    }

    document.getElementById('signupCloseButtonUnique').addEventListener('click', () => {
      const modal = document.getElementById('signupModalUnique');
      modal.classList.remove('show'); // Hide modal
      clearSignupForm(); // Clear inputs and errors
    });

  </script>

</body>

</html>