// Toggle custom dropdown (not Bootstrap's dropdown)
function toggleDropdown(event) {
    event.preventDefault();
    const dropdown = event.currentTarget.nextElementSibling;
    if (!dropdown) return;

    const isVisible = dropdown.style.display === "flex";
    dropdown.style.display = isVisible ? "none" : "flex";
    dropdown.classList.toggle("show", !isVisible);
}

// Close dropdown if clicking outside
document.addEventListener("click", (event) => {
    const dropdown = document.querySelector(".dropdown-menu.show");
    const userIcon = document.querySelector(".user-dropdown > a");

    if (!dropdown || !userIcon) return;

    if (!dropdown.contains(event.target) && !userIcon.contains(event.target)) {
        dropdown.style.display = "none";
        dropdown.classList.remove("show");
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const loginModal = document.getElementById("loginModal");
    const signupModal = document.getElementById("signupModalUnique");
    const closeBtn = document.getElementById("signupCloseButtonUnique");
    const openSignupFromLogin = document.getElementById("openSignupFromLogin");
    const openSignupModalBtn = document.getElementById("openSignupModalUnique");

    // Ensure signup modal is hidden on page load
    if (signupModal) {
        signupModal.classList.remove("show");
        signupModal.setAttribute("aria-hidden", "true");
        document.body.style.overflow = ""; // Ensure background scroll is enabled
    }

    // Function to open the signup modal and close the login modal
    function openSignup(e) {
        e.preventDefault();
        if (loginModal) {
            const bsLoginModal =
                bootstrap.Modal.getInstance(loginModal) ||
                new bootstrap.Modal(loginModal);
            bsLoginModal.hide(); // Hide login modal
        }
        if (signupModal) {
            signupModal.classList.add("show");
            signupModal.setAttribute("aria-hidden", "false");
            document.body.style.overflow = ""; // Allow background scroll
        }
    }

    // Function to open the login modal and close the signup modal
    function openLogin(e) {
        e.preventDefault();
        if (signupModal) {
            signupModal.classList.remove("show");
            signupModal.setAttribute("aria-hidden", "true");
        }
        if (loginModal) {
            const bsLoginModal = new bootstrap.Modal(loginModal);
            bsLoginModal.show(); // Show login modal
            document.body.style.overflow = ""; // Allow background scroll
        }
    }

    // Event listener for opening signup from login
    if (openSignupFromLogin) {
        openSignupFromLogin.addEventListener("click", openSignup); // Switch to signup
    }

    // Event listener for opening signup from navbar
    const signupButtons = document.querySelectorAll(".open-signup-btn");
    signupButtons.forEach((btn) => btn.addEventListener("click", openSignup));

    // Event listener for closing signup modal with the close button
    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            if (signupModal) {
                signupModal.classList.remove("show");
                signupModal.setAttribute("aria-hidden", "true");
                document.body.style.overflow = ""; // Allow background scroll
            }
        });
    }

    // Close the signup modal when clicking outside of it
    if (signupModal) {
        signupModal.addEventListener("click", (e) => {
            if (e.target === signupModal) {
                closeBtn.click(); // Call close button action
            }
        });
    }

    // Optionally, handle the "Don't have an account?" link in the login modal
    const signupLinkInLogin = document.getElementById("openSignupFromLogin");
    if (signupLinkInLogin) {
        signupLinkInLogin.addEventListener("click", openSignup); // Switch to signup
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const signupModal = document.getElementById("signupModalUnique");
    const loginModal = document.getElementById("loginModal");

    // Ensure signup modal is hidden on load
    if (signupModal) {
        signupModal.classList.remove("show");
        signupModal.setAttribute("aria-hidden", "true");
        document.body.style.overflow = ""; // Allow background scroll
    }

    // Close signup modal when clicking the close button
    document
        .getElementById("signupCloseButtonUnique")
        .addEventListener("click", () => {
            if (signupModal) {
                signupModal.classList.remove("show");
                document.body.style.overflow = ""; // Allow background scroll
            }
        });

    // Close signup modal and open login modal when clicking the "Login" link
    document.getElementById("openLoginModal").addEventListener("click", (e) => {
        e.preventDefault(); // Prevent default anchor behavior
        if (signupModal) {
            signupModal.classList.remove("show"); // Close signup modal
        }
        if (loginModal) {
            const bsLoginModal = new bootstrap.Modal(loginModal); // Bootstrap modal instance
            bsLoginModal.show(); // Show the login modal
            document.body.style.overflow = ""; // Allow background scroll
        }
    });

    // Close the signup modal when clicking outside of it
    if (signupModal) {
        signupModal.addEventListener("click", (e) => {
            if (e.target === signupModal) {
                signupModal.classList.remove("show"); // Close signup modal
                document.body.style.overflow = ""; // Allow background scroll
            }
        });
    }

    // Close the login modal when clicking outside of it
    if (loginModal) {
        loginModal.addEventListener("click", (e) => {
            if (e.target === loginModal) {
                loginModal.classList.remove("show"); // Close login modal
                document.body.style.overflow = ""; // Allow background scroll
            }
        });
    }
});



document
    .getElementById("loginModal")
    .addEventListener("hidden.bs.modal", function () {
        // Clear input fields
        const form = document.getElementById("loginForm");
        if (form) form.reset();

        // Also clear any dynamic alert messages if needed
        const alertContainer = document.getElementById("login-alert-container");
        if (alertContainer) alertContainer.innerHTML = "";
    });

document.addEventListener("DOMContentLoaded", () => {
    const hamburger = document.querySelector(".hamburger");
    const mobileMenu = document.querySelector(".mobile-menu");

    if (hamburger && mobileMenu) {
        hamburger.addEventListener("click", () => {
            mobileMenu.classList.toggle("active");
        });
    }
});


document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('searchInput');
  const suggestionsBox = document.getElementById('suggestionsBox');

  let debounceTimeout;

  input.addEventListener('keyup', () => {
    const query = input.value.trim();

    if (debounceTimeout) clearTimeout(debounceTimeout);

    if (query.length === 0) {
      suggestionsBox.innerHTML = '';
      suggestionsBox.style.display = 'none';
      return;
    }

    debounceTimeout = setTimeout(() => {
      fetch(`/search/suggestions?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
          if (data.length === 0) {
            suggestionsBox.innerHTML = '<div class="suggestion-item">No suggestions</div>';
          } else {
            suggestionsBox.innerHTML = data.map(item => 
              `<div class="suggestion-item">${item.name}</div>`
            ).join('');
          }
          suggestionsBox.style.display = 'block';

          // Add click event to suggestions to fill input and optionally submit form
          suggestionsBox.querySelectorAll('.suggestion-item').forEach(el => {
            el.addEventListener('click', () => {
              input.value = el.textContent;
              suggestionsBox.style.display = 'none';
              // Optionally submit form here if you want auto-search on click
              // input.form.submit();
            });
          });
        })
        .catch(() => {
          suggestionsBox.innerHTML = '<div class="suggestion-item">Error loading suggestions</div>';
          suggestionsBox.style.display = 'block';
        });
    }, 300); // debounce 300ms to avoid too many requests
  });

  // Hide suggestions if clicked outside
  document.addEventListener('click', (e) => {
    if (!input.contains(e.target) && !suggestionsBox.contains(e.target)) {
      suggestionsBox.style.display = 'none';
    }
  });
});
