<link rel="stylesheet" href="{{ asset('preview/preview.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div id="previewModal" class="preview-modal" style="display: none;">
  <div class="preview-modal-content">
    <button class="preview-close-btn">&times;</button>

    <div class="preview-body">
      <!-- Image box with image and footer -->
      <div class="image-box">
        <div class="image-box-floating floating">
          <img id="previewImage" src="" alt="Preview Image">
          <div class="image-footer">
            <div class="controls">
              <div class="share-wrapper">
              <div class="share-container">
                <i class="material-icons share-icon">share</i>
                <div class="share-options">
                  <a href="#" class="share-btn fb"><i class="fab fa-facebook-f"></i></a>
                  <a href="#" class="share-btn ig"><i class="fab fa-instagram"></i></a>
                  <a href="#" class="share-btn wa"><i class="fab fa-whatsapp"></i></a>
                  <a href="#" class="share-btn tw"><i class="fab fa-twitter"></i></a>
                  <a href="#" class="share-btn link"><i class="fas fa-link"></i></a>
                </div>
              </div>
</div>
              <i class="material-icons" id="favoriteIcon">favorite_border</i>
              <i class="material-icons" id="fullscreenIcon">fullscreen</i>
            </div>
          </div>
        </div>
      </div>

      <!-- Event details -->
      <div class="preview-details">
        <h2 id="previewTitle">Event Title</h2>
        <p id="previewLocation">
          Location:&nbsp;<span class="location-value"></span>
        </p>
        <p id="previewPrice"><strong>Rs. 0</strong></p>
        <button class="preview-action-btn">Book Now</button>
        <div class="fade-bottom">
          <p id="previewDescription">
        </div>

        </p>

      </div>
    </div>
  </div>
</div>

<script src="{{ asset('preview/preview.js') }}" defer></script>