<!-- resources/views/components/preview.blade.php -->
<div>
  <div id="previewModal" class="preview-modal" style="display: none;">
    <div class="preview-modal-content">
      <button class="preview-close-btn">×</button>

      <div class="preview-body">
        <!-- Image box with image and footer -->
        <div class="image-box">
          <div class="image-box-floating floating">
            <img id="previewImage" alt="{{ $name }}">
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
          <h2 id="previewTitle">{{ $name }}</h2>
          <p id="previewLocation">
            Location: <span class="location-value">{{ $location ?? '' }}</span>
          </p>
          <p id="previewPrice"><strong>Rs. {{ $ticketData['price'] ?? 0 }}</strong></p>
          <form action="{{ route('cart.addToCart') }}" method="POST">
            @csrf
            <input type="hidden" name="d" value="{{ $id }}">
            <button class="preview-action-btn" type="submit">Book Now</button>
          </form>
          <div class="fade-bottom">
            <p id="previewDescription">{{ $description ?? '' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/preview.js') }}" defer></script>