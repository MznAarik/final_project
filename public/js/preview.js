document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('previewModal');
  const closeBtn = modal.querySelector('.preview-close-btn');

  // Target elements inside modal
  // const previewImage = modal.querySelector('#previewImage');
  // const previewTitle = modal.querySelector('#previewTitle');
  // const previewLocation = modal.querySelector('#previewLocation .location-value');
  // const previewPrice = modal.querySelector('#previewPrice');
  // const previewDesc = modal.querySelector('#previewDescription');

  document.querySelectorAll('.open-previewmodal-trigger').forEach(trigger => {
    trigger.addEventListener('click', (e) => {
      const card = e.target.closest('.event-card');
      if (!card) return;

      // // Extract data from card
      // const img = card.querySelector('img')?.getAttribute('src') || '';
      // const title = card.querySelector('h3')?.innerText || 'Event';
      // const location = card.querySelector('.event-location')?.innerText || 'Location';
      // const price = card.querySelector('.event-price')?.innerText || 'Price';

      // // Set data into modal
      // previewImage.src = img;
      // previewTitle.textContent = title;
      // previewLocation.textContent = location;
      // previewPrice.innerHTML = price;
      // previewDesc.innerHTML = `<p>Experience "${title}" at ${location}. Join now and enjoy an unforgettable moment!Experience "${title}" at ${location}. 
      // Join now and enjoy an unforgettable moment!</p>
      // <p>Experience "${title}" at ${location}. Join now and enjoy an unforgettable moment!</p><p>Experience "${title}" at ${location}. Join now and enjoy an unforgettable moment!Experience "${title}" at ${location}. 
      // Join now and enjoy an unforgettable moment!</p>
      // <p>Experience "${title}" at ${location}. Join now and enjoy an unforgettable moment!</p><p>Experience "${title}" at ${location}. Join now and enjoy an unforgettable moment!Experience "${title}" at ${location}. 
      // Join now and enjoy an unforgettable moment!</p>
      // <p>Experience "${title}" at ${location}. Join now and enjoy an unforgettable moment!</p>
      // `;

      // Show modal
      modal.style.display = 'flex';
    });
  });

  closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });
});
document.querySelectorAll('.photo-thumbnails .thumb').forEach(thumb => {
  thumb.addEventListener('click', function () {
    const mainImage = document.getElementById('previewImage');
    mainImage.src = this.src;

    document.querySelectorAll('.photo-thumbnails .thumb').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
  });
});
document.addEventListener('DOMContentLoaded', () => {
  const fullscreenIcon = document.getElementById('fullscreenIcon');
  const favIcon = document.getElementById('favoriteIcon');
  const img = document.getElementById('previewImage');

  fullscreenIcon.addEventListener('click', () => {
    if (!document.fullscreenElement) {
      if (img.requestFullscreen) {
        img.requestFullscreen();
      } else if (img.webkitRequestFullscreen) {
        img.webkitRequestFullscreen();
      } else if (img.msRequestFullscreen) {
        img.msRequestFullscreen();
      }
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      }
    }
  });
const userLoggedIn = true; // Replace with your actual login check

// On load, check if favorite saved
if (userLoggedIn) {
  const isFav = localStorage.getItem('favoriteActive') === 'true';
  if (isFav) {
    favIcon.textContent = 'favorite';
    favIcon.classList.add('fav-active');
  }
}

favIcon.addEventListener('click', () => {
  if (!userLoggedIn) {
    alert('Please log in to favorite items.');
    return;
  }

  if (favIcon.textContent === 'favorite_border') {
    favIcon.textContent = 'favorite';
    favIcon.classList.add('fav-active');
    localStorage.setItem('favoriteActive', 'true');
  } else {
    favIcon.textContent = 'favorite_border';
    favIcon.classList.remove('fav-active');
    localStorage.setItem('favoriteActive', 'false');
  }
});
document.querySelectorAll('.share-container').forEach((shareWrapper) => {
  const shareIcon = shareWrapper.querySelector('.share-icon');
  const shareOptions = shareWrapper.querySelector('.share-options');
  const card = shareWrapper.closest('.image-box-floating');

  // Toggle share buttons and persistent hover on share icon click
  shareIcon.addEventListener('click', (e) => {
    e.stopPropagation(); // prevent document click from firing
    const isExpanded = shareWrapper.classList.toggle('expanded');
    shareOptions.classList.toggle('active', isExpanded);
    card.classList.toggle('share-active', isExpanded); // keep hover style
  });

  // Clicking outside removes the hover/expanded states
  document.addEventListener('click', (e) => {
    if (!shareWrapper.contains(e.target)) {
      shareWrapper.classList.remove('expanded');
      shareOptions.classList.remove('active');
      card.classList.remove('share-active');
    }
  });
});
});
