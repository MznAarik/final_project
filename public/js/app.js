// Toggle custom dropdown (not Bootstrap's dropdown)
function toggleDropdown(event) {
    event.preventDefault();
    const dropdown = event.currentTarget.nextElementSibling;
    if (!dropdown) return;

    const isVisible = dropdown.style.display === 'flex';
    dropdown.style.display = isVisible ? 'none' : 'flex';
    dropdown.classList.toggle('show', !isVisible);
}

// Close dropdown if clicking outside
document.addEventListener('click', (event) => {
    const dropdown = document.querySelector('.dropdown-menu.show');
    const userIcon = document.querySelector('.user-dropdown > a');

    if (!dropdown || !userIcon) return;

    if (!dropdown.contains(event.target) && !userIcon.contains(event.target)) {
        dropdown.style.display = 'none';
        dropdown.classList.remove('show');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const loginModal = document.getElementById('loginModal');
    const signupModal = document.getElementById('signupModalUnique');
    const closeBtn = document.getElementById('signupCloseButtonUnique');
    const openSignupFromLogin = document.getElementById('openSignupFromLogin');
    const openSignupModalBtn = document.getElementById('openSignupModalUnique');

    // Function to open the signup modal and close the login modal
    function openSignup(e) {
        e.preventDefault();
        // Close login modal if it's open
        if (loginModal) {
            const bsLoginModal = bootstrap.Modal.getInstance(loginModal) || new bootstrap.Modal(loginModal);
            bsLoginModal.hide(); // Hide login modal
        }
        // Show signup modal
        signupModal.classList.add('show');
        signupModal.setAttribute('aria-hidden', 'false');
        // Allow background scroll
        document.body.style.overflow = ''; // Allow background scroll
    }

    // Function to open the login modal and close the signup modal
    function openLogin(e) {
        e.preventDefault();
        // Close signup modal if it's open
        if (signupModal) {
            signupModal.classList.remove('show'); // Hide signup modal
            signupModal.setAttribute('aria-hidden', 'true');
        }
        // Show login modal
        const bsLoginModal = new bootstrap.Modal(loginModal);
        bsLoginModal.show(); // Show login modal
        // Allow background scroll
        document.body.style.overflow = ''; // Allow background scroll
    }

    // Event listener for opening signup from login
    if (openSignupFromLogin) {
        openSignupFromLogin.addEventListener('click', openSignup); // Switch to signup
    }

    // Event listener for opening signup from navbar
    const signupButtons = document.querySelectorAll('.open-signup-btn');
signupButtons.forEach(btn => btn.addEventListener('click', openSignup));


    // Event listener for closing signup modal with the close button
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            signupModal.classList.remove('show'); // Hide signup modal
            signupModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = ''; // Allow background scroll
        });
    }

    // Close the signup modal when clicking outside of it
    signupModal.addEventListener('click', (e) => {
        if (e.target === signupModal) {
            closeBtn.click(); // Call close button action
        }
    });

    // Optionally, handle the "Don't have an account?" link in the login modal
    const signupLinkInLogin = document.getElementById('openSignupFromLogin');
    if (signupLinkInLogin) {
        signupLinkInLogin.addEventListener('click', openSignup); // Switch to signup
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const signupModal = document.getElementById('signupModalUnique');
    const loginModal = document.getElementById('loginModal');
    
    // Close signup modal when clicking the close button
    document.getElementById('signupCloseButtonUnique').addEventListener('click', () => {
        signupModal.classList.remove('show');
        document.body.style.overflow = ''; // Allow background scroll
    });

    // Close signup modal and open login modal when clicking the "Login" link
    document.getElementById('openLoginModal').addEventListener('click', (e) => {
        e.preventDefault(); // Prevent default anchor behavior
        signupModal.classList.remove('show'); // Close signup modal
        const bsLoginModal = new bootstrap.Modal(loginModal); // Bootstrap modal instance
        bsLoginModal.show(); // Show the login modal
        // Allow background scroll
        document.body.style.overflow = ''; // Allow background scroll
    });

    // Close the signup modal when clicking outside of it
    signupModal.addEventListener('click', (e) => {
        if (e.target === signupModal) {
            signupModal.classList.remove('show'); // Close signup modal
            document.body.style.overflow = ''; // Allow background scroll
        }
    });

    // Close the login modal when clicking outside of it
    loginModal.addEventListener('click', (e) => {
        if (e.target === loginModal) {
            loginModal.classList.remove('show'); // Close login modal
            document.body.style.overflow = ''; // Allow background scroll
        }
    });
});

function showSignupAlert(message, type = 'info') {
    const alertContainer = document.getElementById('signupAlertContainerUnique');
    alertContainer.innerHTML = `
      <div class="alertUnique ${type === 'danger' ? 'alertDangerUnique' : 'alertInfoUnique'}">
        ${message}
      </div>
    `;
    alertContainer.classList.add('showAlert');
}

// Example usage
document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');

  if (!loginForm) return;

  loginForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const loginBtn = document.getElementById('loginSubmitButton');
    if (loginBtn) {
      loginBtn.disabled = true;
      loginBtn.textContent = 'Logging in...';
    }

    const formData = new FormData(loginForm);

    try {
      const res = await fetch(loginForm.action, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
        },
        body: formData,
      });

      const data = await res.json();
      const alertContainer = document.getElementById('login-alert-container');
      if (alertContainer) alertContainer.innerHTML = '';

      if (data.success) {
        if (alertContainer) alertContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
        setTimeout(() => {
          window.location.href = data.redirect_url;
        }, 1500);
      } else {
        if (alertContainer) alertContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        if (loginBtn) {
          loginBtn.disabled = false;
          loginBtn.textContent = 'Login';
        }
      }
    } catch (error) {
      const alertContainer = document.getElementById('login-alert-container');
      if (alertContainer) alertContainer.innerHTML = `<div class="alert alert-danger">Something went wrong. Please try again.</div>`;
      if (loginBtn) {
        loginBtn.disabled = false;
        loginBtn.textContent = 'Login';
      }
    }
  });
});
document.getElementById('loginModal').addEventListener('hidden.bs.modal', function () {
    // Clear input fields
    const form = document.getElementById('loginForm');
    form.reset();

    // Also clear any dynamic alert messages if needed
    const alertContainer = document.getElementById('login-alert-container');
    alertContainer.innerHTML = '';
});
document.addEventListener('DOMContentLoaded', () => {
  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.querySelector('.mobile-menu');

  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      mobileMenu.classList.toggle('active');
    });
  }
});
// JS to show the modal
document.getElementById('previewModal').classList.add('show');
