@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-red-50 to-rose-50 py-6 px-4">
  <div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8">
      <h1 class="text-4xl font-bold bg-gradient-to-r from-red-600 to-rose-600 bg-clip-text text-transparent mb-2">
        Search Results for "{{ $query }}"
      </h1>
      <p id="resultsCount" class="text-gray-600 text-lg">
        @if($events->isEmpty())
          No events found.
        @else
          Found {{ $events->count() }} events
        @endif
      </p>
    </div>

    @if(!$events->isEmpty())
    <div class="flex flex-col lg:flex-row gap-6">
      <!-- Main Content Area -->
      <div class="flex-1">
        <!-- Sort Controls -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-xl border border-white/20 mb-6">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4 flex-wrap">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V4z"></path>
                </svg>
                <span class="font-semibold text-gray-700">Sort by:</span>
              </div>
              
              <select id="sortSelect" class="px-4 py-2 bg-white border-2 border-gray-200 rounded-xl focus:border-red-500 focus:outline-none transition-all duration-300 font-medium">
                <option value="relevance">Relevance</option>
                <option value="date_asc">Date (Earliest First)</option>
                <option value="date_desc">Date (Latest First)</option>
                <option value="price_asc">Price (Low to High)</option>
                <option value="price_desc">Price (High to Low)</option>
                <option value="name_asc">Name (A-Z)</option>
                <option value="name_desc">Name (Z-A)</option>
                <option value="location_asc">Location (A-Z)</option>
                <option value="status">Status</option>
              </select>
            </div>

            <!-- View Toggle & Filter Toggle -->
            <div class="flex items-center gap-3">
              <!-- View Toggle -->
              <div class="flex items-center bg-gray-100 rounded-xl p-1">
                <button id="gridView" class="view-toggle active px-3 py-2 rounded-lg transition-all duration-300">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                  </svg>
                </button>
                <button id="listView" class="view-toggle px-3 py-2 rounded-lg transition-all duration-300">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                  </svg>
                </button>
              </div>

              <!-- Filter Toggle -->
              <button id="filterToggle" class="lg:hidden bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl font-medium transition-all duration-300 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"></path>
                </svg>
                Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Events Container -->
        <section class="event-section">
          <div id="eventCards" class="event-cards">
            @foreach ($events as $event)
              @php
                $ticketData = is_array($event->ticket_category_price)
                  ? $event->ticket_category_price
                  : json_decode($event->ticket_category_price, true);

                $prices = collect($ticketData)->pluck('price')->filter();
                $minPrice = $prices->min();
                $maxPrice = $prices->max();
                $priceRange = $prices->isEmpty() ? 'N/A' : ($minPrice == $maxPrice ? $minPrice : "$minPrice - $maxPrice");
              @endphp

              <div class="event-card open-previewmodal-trigger"
                data-id="{{ $event->id }}"
                data-name="{{ $event->name }}"
                data-image="{{ asset('storage/' . $event->img_path) }}"
                data-location="{{ $event->location }}"
                data-venue="{{ $event->venue }}"
                data-organizer="{{ $event->organizer }}"
                data-contact="{{ $event->contact_info }}"
                data-price="{{ $priceRange }}"
                data-min-price="{{ $minPrice ?? 0 }}"
                data-max-price="{{ $maxPrice ?? 0 }}"
                data-description="{{ $event->description }}"
                data-startdate="{{ $event->start_date }}"
                data-enddate="{{ $event->end_date }}"
                data-status="{{ $event->status }}"
                data-ticketdata='@json($ticketData)'>

                <p class="ribbon {{ strtolower($event->status ?? '') }}">
                  <i class="fas fa-info-circle"></i> {{ ucfirst($event->status ?? 'Status not specified') }}
                </p>
                <img src="{{ asset('storage/' . $event->img_path) }}" alt="{{ $event->name }}">
                <div class="card-content">
                  <h3 class="bold">{{ $event->name }}</h3>
                  <p class="event-date"><i class="fas fa-calendar-alt"></i>
                    {{ $event->start_date ?? 'Date not specified' }} - {{ $event->end_date ?? '' }}</p>
                  <p class="event-location"><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</p>
                  <div class="price-button-container">
                    <p class="event-price"><strong>Rs. {{ $priceRange }}</strong></p>
                    <button class="small-button">Book Now</button>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </section>
      </div>

      <!-- Advanced Filters Sidebar -->
      <div id="filterSidebar" class="lg:w-80 lg:block hidden">
        <div class="sticky top-6">
          <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-xl border border-white/20">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"></path>
                </svg>
                Advanced Filters
              </h3>
              <button id="clearFilters" class="text-sm text-red-600 hover:text-red-800 font-medium">Clear All</button>
            </div>

            <!-- Date Range Filter -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-3">Date Range</label>
              <div class="space-y-3">
                <div>
                  <label class="block text-xs text-gray-600 mb-1">From</label>
                  <input type="date" id="dateFrom" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:outline-none transition-all">
                </div>
                <div>
                  <label class="block text-xs text-gray-600 mb-1">To</label>
                  <input type="date" id="dateTo" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:outline-none transition-all">
                </div>
              </div>
            </div>

            <!-- Price Range Filter -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-3">Price Range</label>
              <div class="space-y-3">
                <div class="flex items-center gap-2">
                  <input type="number" id="priceMin" placeholder="Min" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:outline-none transition-all">
                  <span class="text-gray-500">-</span>
                  <input type="number" id="priceMax" placeholder="Max" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:outline-none transition-all">
                </div>
                <div class="flex items-center gap-2 text-sm">
                  <span class="text-gray-600">Rs.</span>
                  <input type="range" id="priceRange" min="0" max="10000" step="100" class="flex-1">
                  <span id="priceRangeDisplay" class="text-gray-600 font-medium">0 - 10000</span>
                </div>
              </div>
            </div>

            <!-- Status Filter -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-3">Event Status</label>
              <div class="space-y-2">
                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                  <input type="checkbox" class="status-filter w-4 h-4 text-red-600 border-2 border-gray-300 rounded focus:ring-red-500" value="upcoming">
                  <span class="text-sm">Upcoming</span>
                  <span class="ml-auto text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Active</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                  <input type="checkbox" class="status-filter w-4 h-4 text-red-600 border-2 border-gray-300 rounded focus:ring-red-500" value="ongoing">
                  <span class="text-sm">Ongoing</span>
                  <span class="ml-auto text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Live</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                  <input type="checkbox" class="status-filter w-4 h-4 text-red-600 border-2 border-gray-300 rounded focus:ring-red-500" value="completed">
                  <span class="text-sm">Completed</span>
                  <span class="ml-auto text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full">Past</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                  <input type="checkbox" class="status-filter w-4 h-4 text-red-600 border-2 border-gray-300 rounded focus:ring-red-500" value="cancelled">
                  <span class="text-sm">Cancelled</span>
                  <span class="ml-auto text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Cancelled</span>
                </label>
              </div>
            </div>

            <!-- Location Filter -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-3">Location</label>
              <input type="text" id="locationFilter" placeholder="Enter location..." class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:outline-none transition-all">
              <div id="locationSuggestions" class="mt-2 space-y-1 max-h-32 overflow-y-auto"></div>
            </div>

            <!-- Quick Filters -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-3">Quick Filters</label>
              <div class="flex flex-wrap gap-2">
                <button class="quick-filter px-3 py-2 text-sm bg-gray-100 hover:bg-red-100 hover:text-red-700 rounded-full transition-all" data-filter="today">Today</button>
                <button class="quick-filter px-3 py-2 text-sm bg-gray-100 hover:bg-red-100 hover:text-red-700 rounded-full transition-all" data-filter="weekend">This Weekend</button>
                <button class="quick-filter px-3 py-2 text-sm bg-gray-100 hover:bg-red-100 hover:text-red-700 rounded-full transition-all" data-filter="free">Free Events</button>
                <button class="quick-filter px-3 py-2 text-sm bg-gray-100 hover:bg-red-100 hover:text-red-700 rounded-full transition-all" data-filter="premium">Premium</button>
              </div>
            </div>

            <!-- Apply Filters Button -->
            <button id="applyFilters" class="w-full bg-gradient-to-r from-red-600 to-rose-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
              Apply Filters
            </button>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>

  <!-- Mobile Filter Overlay -->
  <div id="mobileFilterOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden lg:hidden">
    <div class="fixed inset-y-0 right-0 w-80 bg-white shadow-2xl transform translate-x-full transition-transform duration-300" id="mobileFilterPanel">
      <div class="h-full overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-gray-800">Filters</h3>
          <button id="closeMobileFilter" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
          </button>
        </div>
        <!-- Filter content will be duplicated here for mobile -->
      </div>
    </div>
  </div>
</div>

<style>
.view-toggle {
  color: #6b7280;
}

.view-toggle.active {
  background-color: white;
  color: #ef4444 ;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.event-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  transition: all 0.3s ease;
}

.event-cards.list-view {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.event-cards.list-view .event-card {
  display: flex;
  flex-direction: row;
  max-width: none;
  height: auto;
}

.event-cards.list-view .event-card img {
  width: 200px;
  height: 150px;
  object-fit: cover;
  flex-shrink: 0;
}

.event-cards.list-view .card-content {
  flex: 1;
  padding: 1.5rem;
}

.quick-filter.active {
  background-color: #ef4444 ;
  color: white;
}

.status-filter:checked {
  background-color: #ef4444 ;
  border-color: #ef4444 ;
}

/* Animation for filter application */
.event-card.filtering {
  transform: scale(0.95);
  opacity: 0.7;
  transition: all 0.3s ease;
}

.event-card.filtered-out {
  display: none;
}

/* Loading state */
.loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 1rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;
  border-top: 4px solid #ef4444 ;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Mobile filter panel animation */
#mobileFilterOverlay.show #mobileFilterPanel {
  transform: translateX(0);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const eventCards = document.getElementById('eventCards');
  const sortSelect = document.getElementById('sortSelect');
  const gridViewBtn = document.getElementById('gridView');
  const listViewBtn = document.getElementById('listView');
  const filterToggle = document.getElementById('filterToggle');
  const mobileFilterOverlay = document.getElementById('mobileFilterOverlay');
  const closeMobileFilter = document.getElementById('closeMobileFilter');
  const clearFiltersBtn = document.getElementById('clearFilters');
  const applyFiltersBtn = document.getElementById('applyFilters');
  const resultsCount = document.getElementById('resultsCount');
  
  let originalCards = Array.from(document.querySelectorAll('.event-card'));
  let filteredCards = [...originalCards];
  
  // View toggle functionality
  gridViewBtn.addEventListener('click', () => {
    eventCards.classList.remove('list-view');
    gridViewBtn.classList.add('active');
    listViewBtn.classList.remove('active');
  });
  
  listViewBtn.addEventListener('click', () => {
    eventCards.classList.add('list-view');
    listViewBtn.classList.add('active');
    gridViewBtn.classList.remove('active');
  });
  
  // Mobile filter toggle
  filterToggle?.addEventListener('click', () => {
    mobileFilterOverlay.classList.remove('hidden');
    setTimeout(() => mobileFilterOverlay.classList.add('show'), 10);
  });
  
  closeMobileFilter?.addEventListener('click', () => {
    mobileFilterOverlay.classList.remove('show');
    setTimeout(() => mobileFilterOverlay.classList.add('hidden'), 300);
  });
  
  // Sorting functionality
  sortSelect.addEventListener('change', function() {
    const sortBy = this.value;
    const cards = Array.from(eventCards.children);
    
    // Add loading state
    addLoadingState();
    
    setTimeout(() => {
      const sortedCards = cards.sort((a, b) => {
        switch(sortBy) {
          case 'date_asc':
            return new Date(a.dataset.startdate) - new Date(b.dataset.startdate);
          case 'date_desc':
            return new Date(b.dataset.startdate) - new Date(a.dataset.startdate);
          case 'price_asc':
            return parseFloat(a.dataset.minPrice || 0) - parseFloat(b.dataset.minPrice || 0);
          case 'price_desc':
            return parseFloat(b.dataset.maxPrice || 0) - parseFloat(a.dataset.maxPrice || 0);
          case 'name_asc':
            return a.dataset.name.localeCompare(b.dataset.name);
          case 'name_desc':
            return b.dataset.name.localeCompare(a.dataset.name);
          case 'location_asc':
            return a.dataset.location.localeCompare(b.dataset.location);
          case 'status':
            const statusOrder = {'upcoming': 1, 'ongoing': 2, 'completed': 3, 'cancelled': 4};
            return (statusOrder[a.dataset.status.toLowerCase()] || 5) - (statusOrder[b.dataset.status.toLowerCase()] || 5);
          default:
            return 0; // relevance - keep original order
        }
      });
      
      // Clear and re-append sorted cards
      eventCards.innerHTML = '';
      sortedCards.forEach(card => {
        card.style.transform = 'scale(0.9)';
        card.style.opacity = '0';
        eventCards.appendChild(card);
      });
      
      // Animate cards back in
      setTimeout(() => {
        sortedCards.forEach((card, index) => {
          setTimeout(() => {
            card.style.transform = 'scale(1)';
            card.style.opacity = '1';
            card.style.transition = 'all 0.3s ease';
          }, index * 50);
        });
      }, 100);
      
      removeLoadingState();
    }, 500);
  });
  
  // Filter functionality
  function applyFilters() {
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const priceMin = parseFloat(document.getElementById('priceMin').value) || 0;
    const priceMax = parseFloat(document.getElementById('priceMax').value) || Infinity;
    const selectedStatuses = Array.from(document.querySelectorAll('.status-filter:checked')).map(cb => cb.value);
    const locationFilter = document.getElementById('locationFilter').value.toLowerCase();
    
    addLoadingState();
    
    setTimeout(() => {
      filteredCards = originalCards.filter(card => {
        // Date filter
        if (dateFrom) {
          const cardDate = new Date(card.dataset.startdate);
          if (cardDate < new Date(dateFrom)) return false;
        }
        if (dateTo) {
          const cardDate = new Date(card.dataset.startdate);
          if (cardDate > new Date(dateTo)) return false;
        }
        
        // Price filter
        const cardMinPrice = parseFloat(card.dataset.minPrice) || 0;
        const cardMaxPrice = parseFloat(card.dataset.maxPrice) || 0;
        if (cardMinPrice < priceMin || (priceMax !== Infinity && cardMaxPrice > priceMax)) {
          return false;
        }
        
        // Status filter
        if (selectedStatuses.length > 0) {
          if (!selectedStatuses.includes(card.dataset.status.toLowerCase())) {
            return false;
          }
        }
        
        // Location filter
        if (locationFilter && !card.dataset.location.toLowerCase().includes(locationFilter)) {
          return false;
        }
        
        return true;
      });
      
      // Update display
      eventCards.innerHTML = '';
      filteredCards.forEach(card => {
        card.style.transform = 'scale(0.9)';
        card.style.opacity = '0';
        eventCards.appendChild(card);
      });
      
      // Animate filtered cards
      setTimeout(() => {
        filteredCards.forEach((card, index) => {
          setTimeout(() => {
            card.style.transform = 'scale(1)';
            card.style.opacity = '1';
            card.style.transition = 'all 0.3s ease';
          }, index * 30);
        });
      }, 100);
      
      // Update results count
      resultsCount.textContent = `Found ${filteredCards.length} events`;
      
      removeLoadingState();
      
      // Close mobile filter if open
      if (mobileFilterOverlay.classList.contains('show')) {
        closeMobileFilter.click();
      }
    }, 300);
  }
  
  // Quick filters
  document.querySelectorAll('.quick-filter').forEach(btn => {
    btn.addEventListener('click', function() {
      // Toggle active state
      this.classList.toggle('active');
      
      const filter = this.dataset.filter;
      const today = new Date().toISOString().split('T')[0];
      
      switch(filter) {
        case 'today':
          document.getElementById('dateFrom').value = today;
          document.getElementById('dateTo').value = today;
          break;
        case 'weekend':
          const now = new Date();
          const saturday = new Date(now.setDate(now.getDate() + (6 - now.getDay())));
          const sunday = new Date(saturday);
          sunday.setDate(saturday.getDate() + 1);
          
          document.getElementById('dateFrom').value = saturday.toISOString().split('T')[0];
          document.getElementById('dateTo').value = sunday.toISOString().split('T')[0];
          break;
        case 'free':
          document.getElementById('priceMin').value = '0';
          document.getElementById('priceMax').value = '0';
          break;
        case 'premium':
          document.getElementById('priceMin').value = '1000';
          break;
      }
      
      applyFilters();
    });
  });
  
  // Real-time price range updates
  const priceRange = document.getElementById('priceRange');
  const priceRangeDisplay = document.getElementById('priceRangeDisplay');
  const priceMin = document.getElementById('priceMin');
  const priceMax = document.getElementById('priceMax');
  
  if (priceRange) {
    priceRange.addEventListener('input', function() {
      const value = this.value;
      priceRangeDisplay.textContent = `0 - ${value}`;
      priceMax.value = value;
    });
  }
  
  // Clear all filters
  clearFiltersBtn.addEventListener('click', function() {
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    document.getElementById('priceMin').value = '';
    document.getElementById('priceMax').value = '';
    document.getElementById('locationFilter').value = '';
    
    document.querySelectorAll('.status-filter').forEach(cb => cb.checked = false);
    document.querySelectorAll('.quick-filter').forEach(btn => btn.classList.remove('active'));
    
    // Reset to show all cards
    filteredCards = [...originalCards];
    eventCards.innerHTML = '';
    originalCards.forEach(card => eventCards.appendChild(card));
    
    resultsCount.textContent = `Found ${originalCards.length} events`;
  });
  
  // Apply filters button
  applyFiltersBtn.addEventListener('click', applyFilters);
  
  // Real-time filtering on input changes
  ['dateFrom', 'dateTo', 'priceMin', 'priceMax', 'locationFilter'].forEach(id => {
    const element = document.getElementById(id);
    if (element) {
      element.addEventListener('input', debounce(applyFilters, 500));
    }
  });
  
  document.querySelectorAll('.status-filter').forEach(checkbox => {
    checkbox.addEventListener('change', applyFilters);
  });
  
  // Location suggestions
  const locationFilter = document.getElementById('locationFilter');
  const locationSuggestions = document.getElementById('locationSuggestions');
  
  if (locationFilter) {
    locationFilter.addEventListener('input', function() {
      const query = this.value.toLowerCase();
      const locations = [...new Set(originalCards.map(card => card.dataset.location))];
      const matches = locations.filter(loc => loc.toLowerCase().includes(query));
      
      locationSuggestions.innerHTML = '';
      matches.slice(0, 5).forEach(location => {
        const suggestion = document.createElement('div');
        suggestion.className = 'px-3 py-2 hover:bg-red-50 cursor-pointer rounded-lg transition-colors text-sm';
        suggestion.textContent = location;
        suggestion.addEventListener('click', () => {
          locationFilter.value = location;
          locationSuggestions.innerHTML = '';
          applyFilters();
        });
        locationSuggestions.appendChild(suggestion);
      });
    });
  }
  
  // Utility functions
  function addLoadingState() {
    eventCards.style.position = 'relative';
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = '<div class="spinner"></div>';
    eventCards.appendChild(loadingOverlay);
  }
  
  function removeLoadingState() {
    const loadingOverlay = eventCards.querySelector('.loading-overlay');
    if (loadingOverlay) {
      loadingOverlay.remove();
    }
  }
  
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }
  
  // Initialize tooltips and animations
  function initializeInteractions() {
    // Add hover effects to cards
    document.querySelectorAll('.event-card').forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px) scale(1.02)';
        this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
      });
      
      card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
        this.style.boxShadow = '';
      });
    });
    
    // Add ripple effect to buttons
    document.querySelectorAll('button').forEach(button => {
      button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        this.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
      });
    });
  }
  
  // Advanced search functionality
  function initializeAdvancedSearch() {
    // Add search within results
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search within results...';
    searchInput.className = 'w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:outline-none transition-all duration-300 mb-4';
    searchInput.id = 'withinResultsSearch';
    
    const filterSidebar = document.getElementById('filterSidebar');
    if (filterSidebar) {
      const firstChild = filterSidebar.querySelector('.bg-white\\/80').children[1];
      firstChild.insertBefore(searchInput, firstChild.firstChild);
      
      // Add search icon
      const searchIcon = document.createElement('div');
      searchIcon.className = 'absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none';
      searchIcon.innerHTML = '<svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>';
      
      const searchContainer = document.createElement('div');
      searchContainer.className = 'relative mb-4';
      searchContainer.appendChild(searchIcon);
      searchContainer.appendChild(searchInput);
      
      firstChild.insertBefore(searchContainer, firstChild.children[1]);
      
      // Search functionality
      searchInput.addEventListener('input', debounce(function() {
        const query = this.value.toLowerCase();
        const cards = document.querySelectorAll('.event-card');
        
        cards.forEach(card => {
          const name = card.dataset.name.toLowerCase();
          const description = card.dataset.description.toLowerCase();
          const location = card.dataset.location.toLowerCase();
          const organizer = card.dataset.organizer.toLowerCase();
          
          const matches = name.includes(query) || 
                         description.includes(query) || 
                         location.includes(query) || 
                         organizer.includes(query);
          
          if (matches || query === '') {
            card.style.display = 'block';
            card.style.opacity = '1';
          } else {
            card.style.display = 'none';
            card.style.opacity = '0';
          }
        });
        
        // Update count
        const visibleCards = document.querySelectorAll('.event-card[style*="display: block"], .event-card:not([style*="display: none"])');
        resultsCount.textContent = `Found ${visibleCards.length} events`;
      }, 300));
    }
  }
  
  // Initialize everything
  initializeInteractions();
  initializeAdvancedSearch();
  
  // Keyboard shortcuts
  document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
      switch(e.key) {
        case 'f':
          e.preventDefault();
          document.getElementById('withinResultsSearch').focus();
          break;
        case '1':
          e.preventDefault();
          gridViewBtn.click();
          break;
        case '2':
          e.preventDefault();
          listViewBtn.click();
          break;
      }
    }
    
    if (e.key === 'Escape') {
      if (mobileFilterOverlay.classList.contains('show')) {
        closeMobileFilter.click();
      }
    }
  });
  
  // Performance optimization - Virtual scrolling for large result sets
  function implementVirtualScrolling() {
    if (originalCards.length > 50) {
      // Implement virtual scrolling for better performance
      let visibleStart = 0;
      const visibleCount = 20;
      
      function updateVisibleCards() {
        const scrollTop = window.pageYOffset;
        const cardHeight = 400; // Approximate card height
        const newVisibleStart = Math.floor(scrollTop / cardHeight);
        
        if (newVisibleStart !== visibleStart) {
          visibleStart = newVisibleStart;
          renderVisibleCards();
        }
      }
      
      function renderVisibleCards() {
        eventCards.innerHTML = '';
        const endIndex = Math.min(visibleStart + visibleCount, filteredCards.length);
        
        for (let i = visibleStart; i < endIndex; i++) {
          if (filteredCards[i]) {
            eventCards.appendChild(filteredCards[i]);
          }
        }
      }
      
      window.addEventListener('scroll', debounce(updateVisibleCards, 16));
    }
  }
  
  // Analytics tracking for filter usage
  function trackFilterUsage(filterType, value) {
    // Implement analytics tracking
    console.log(`Filter used: ${filterType} = ${value}`);
    // You can integrate with Google Analytics, Mixpanel, etc.
  }
  
  // Save user preferences
  function saveFilterPreferences() {
    const preferences = {
      sortBy: sortSelect.value,
      viewType: eventCards.classList.contains('list-view') ? 'list' : 'grid',
      filters: {
        dateFrom: document.getElementById('dateFrom').value,
        dateTo: document.getElementById('dateTo').value,
        priceMin: document.getElementById('priceMin').value,
        priceMax: document.getElementById('priceMax').value,
        location: document.getElementById('locationFilter').value,
        statuses: Array.from(document.querySelectorAll('.status-filter:checked')).map(cb => cb.value)
      }
    };
    
    localStorage.setItem('eventFilterPreferences', JSON.stringify(preferences));
  }
  
  // Load user preferences
  function loadFilterPreferences() {
    const preferences = localStorage.getItem('eventFilterPreferences');
    if (preferences) {
      const prefs = JSON.parse(preferences);
      
      if (prefs.sortBy) sortSelect.value = prefs.sortBy;
      if (prefs.viewType === 'list') listViewBtn.click();
      
      if (prefs.filters) {
        if (prefs.filters.dateFrom) document.getElementById('dateFrom').value = prefs.filters.dateFrom;
        if (prefs.filters.dateTo) document.getElementById('dateTo').value = prefs.filters.dateTo;
        if (prefs.filters.priceMin) document.getElementById('priceMin').value = prefs.filters.priceMin;
        if (prefs.filters.priceMax) document.getElementById('priceMax').value = prefs.filters.priceMax;
        if (prefs.filters.location) document.getElementById('locationFilter').value = prefs.filters.location;
        
        prefs.filters.statuses.forEach(status => {
          const checkbox = document.querySelector(`[value="${status}"]`);
          if (checkbox) checkbox.checked = true;
        });
        
        applyFilters();
      }
    }
  }
  
  // Save preferences on change
  [sortSelect, gridViewBtn, listViewBtn, applyFiltersBtn].forEach(element => {
    if (element) {
      element.addEventListener('click', saveFilterPreferences);
      element.addEventListener('change', saveFilterPreferences);
    }
  });
  
  // Load preferences on page load
  loadFilterPreferences();
});
</script>

<!-- Additional CSS for ripple effect -->
<style>
.ripple {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.6);
  transform: scale(0);
  animation: ripple 0.6s linear;
  pointer-events: none;
}

@keyframes ripple {
  to {
    transform: scale(4);
    opacity: 0;
  }
}

/* Smooth transitions for all interactive elements */
* {
  transition: all 0.2s ease;
}

/* Custom scrollbar for filter sidebar */
#filterSidebar::-webkit-scrollbar {
  width: 6px;
}

#filterSidebar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

#filterSidebar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

#filterSidebar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Enhanced focus states for accessibility */
input:focus, select:focus, button:focus {
  outline: 2px solid #ef4444 ;
  outline-offset: 2px;
}

/* Loading skeleton for better UX */
.skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* Print styles */
@media print {
  #filterSidebar, .view-toggle, #filterToggle, .quick-filter {
    display: none !important;
  }
  
  .event-card {
    break-inside: avoid;
    margin-bottom: 1rem;
  }
}
</style>
@endsection


<div id="previewModal" class="preview-modal" style="display: none;">
    <div class="preview-modal-content">
        <button class="preview-close-btn">×</button>
        <div class="preview-body">
            <div class="image-box">
                <div class="image-box-floating floating">
                    <img id="previewImage" src="" alt="Event Image">
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

            <div class="preview-details">
                <h2 id="previewTitle">Event Name</h2>

                <div style="max-height: 250px; overflow-y: auto; border: 1px solid #ccc; padding: 8px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr>
                            <td style="padding: 2px 4px; font-weight: bold;">Organizer:</td>
                            <td style="padding: 2px 4px;"><span id="previewOrganizer">N/A</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 4px; font-weight: bold;">Contact:</td>
                            <td style="padding: 2px 4px;"><span id="previewContact" class="lowercase">N/A</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 4px; font-weight: bold;">Venue:</td>
                            <td style="padding: 2px 4px;"><span id="previewVenue">N/A</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 4px; font-weight: bold;">Date:</td>
                            <td style="padding: 2px 4px;"><span id="previewDate">N/A</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 4px; font-weight: bold;">Location:</td>
                            <td style="padding: 2px 4px;"><span id="previewLocation" class="location-value">N/A</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Fade effect for description -->
                <div class="fade-bottom">
                    <p id="previewDescription">Event Description</p>
                </div>

                <div style="flex-shrink: 0; padding: 15px 20px; text-align: center;">
                    <p style="margin: 8px 0;"><strong>Ticket Categories</strong></p>

                    <div id="previewTicketCategories" class="ticket-categories">
                        <!-- Ticket categories will be inserted here -->
                    </div>

                    <form id="bookForm" action="{{ route('cart.add') }}" method="POST" style="text-align: center;">
                        @csrf
                        <input type="hidden" id="previewEventId" name="id">
                        <input type="hidden" id="ticketQuantities" name="ticketQuantities">
                        <button class="preview-action-btn" type="submit"
                            style="padding: 8px 20px; font-weight: bold;">Book Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Styles and Scripts -->
<link rel="stylesheet" href="{{ asset('css/preview.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('previewModal');
        const closeBtn = modal.querySelector('.preview-close-btn');
        const fullscreenIcon = document.getElementById('fullscreenIcon');
        const favIcon = document.getElementById('favoriteIcon');
        const img = document.getElementById('previewImage');
        const userLoggedIn = true; // replace with actual auth check
        const bookForm = document.getElementById('bookForm');
        const previewTicketCategories = document.getElementById('previewTicketCategories');
        const previewEventId = document.getElementById('previewEventId');
        const ticketQuantitiesInput = document.getElementById('ticketQuantities');

        const previewTitle = document.getElementById('previewTitle');
        const previewOrganizer = document.getElementById('previewOrganizer');
        const previewContact = document.getElementById('previewContact');
        const previewLocation = document.getElementById('previewLocation');
        const previewVenue = document.getElementById('previewVenue');
        const previewDate = document.getElementById('previewDate');
        const previewDesc = document.getElementById('previewDescription');

        let ticketData = [];

        function renderTicketCategories() {
            previewTicketCategories.innerHTML = '';

            ticketData.forEach((ticket, index) => {
                const div = document.createElement('div');
                div.className = 'ticket-box';
                div.style.border = '1px solid #ccc';
                div.style.padding = '8px';
                div.style.borderRadius = '4px';
                div.style.width = '100px';         // Force consistent narrow width
                div.style.margin = '0 5px';        // Optional horizontal gap
                div.style.boxSizing = 'border-box';
                div.style.textAlign = 'center';
                div.innerHTML = `
                <strong>${ticket.category.toUpperCase()}</strong><br>
                <p class="price" id="price-${index}">Rs. ${ticket.price}</p>
                <div class="quantity flex mt-2" data-index="${index}">
                    <button class="sub-btn m-2 text-2xl" type="button">-</button>
                    <input type="text" class="text-quantity" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '')" style="width: 40px; text-align: center;">
                    <button class="add-btn m-2 text-2xl" type="button">+</button>
                </div>
            `;
                previewTicketCategories.appendChild(div);
            });
        }

        function updatePrice(index, quantity) {
            const priceElement = document.getElementById(`price-${index}`);
            if (!priceElement || !ticketData[index]) return;
            const unitPrice = ticketData[index].price || 0;
            const totalPrice = quantity > 0 ? unitPrice * quantity : unitPrice;
            priceElement.textContent = `Rs. ${totalPrice}`;
        }

        previewTicketCategories.addEventListener('click', (e) => {
            const btn = e.target.closest('.add-btn, .sub-btn');
            if (!btn) return;

            const quantityDiv = btn.closest('.quantity');
            const input = quantityDiv.querySelector('.text-quantity');
            const index = parseInt(quantityDiv.dataset.index);
            const ticketBox = btn.closest('.ticket-box');

            let value = parseInt(input.value) || 0;

            if (btn.classList.contains('add-btn')) {
                if (value === 0) {
                    // Enforce exclusive selection
                    previewTicketCategories.querySelectorAll('.text-quantity').forEach(i => {
                        if (i !== input) i.value = '0';
                    });
                    previewTicketCategories.querySelectorAll('.ticket-box').forEach(box => box.classList.remove('active'));
                    ticketBox.classList.add('active');
                }
                value += 1;
            } else if (btn.classList.contains('sub-btn') && value > 0) {
                value -= 1;
                if (value === 0) ticketBox.classList.remove('active');
            }

            input.value = value;
            updatePrice(index, value);
        });

        previewTicketCategories.addEventListener('input', (e) => {
            if (!e.target.classList.contains('text-quantity')) return;

            const input = e.target;
            const quantityDiv = input.closest('.quantity');
            const index = parseInt(quantityDiv.dataset.index);
            let value = parseInt(input.value) || 0;

            if (value > 0) {
                previewTicketCategories.querySelectorAll('.text-quantity').forEach(i => {
                    if (i !== input) i.value = '0';
                });
                previewTicketCategories.querySelectorAll('.ticket-box').forEach(box => box.classList.remove('active'));
                input.closest('.ticket-box').classList.add('active');
            } else {
                input.closest('.ticket-box').classList.remove('active');
            }

            input.value = value;
            updatePrice(index, value);
        });

        bookForm.addEventListener('submit', (e) => {
            const quantities = Array.from(previewTicketCategories.querySelectorAll('.text-quantity'))
                .map(input => parseInt(input.value) || 0);
            const totalQuantity = quantities.reduce((sum, qty) => sum + qty, 0);

            if (totalQuantity === 0) {
                e.preventDefault();
                alert('Please select at least one ticket.');
                return;
            }

            const selectedTickets = [];
            previewTicketCategories.querySelectorAll('.quantity').forEach((div, index) => {
                const quantity = parseInt(div.querySelector('.text-quantity').value) || 0;
                if (quantity > 0 && ticketData[index]) {
                    selectedTickets.push({
                        category: ticketData[index].category,
                        quantity: quantity
                    });
                }
            });

            ticketQuantitiesInput.value = JSON.stringify(selectedTickets);
        });

        document.querySelectorAll('.open-previewmodal-trigger').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                const card = e.target.closest('.event-card');
                if (!card) return;

                img.src = card.dataset.image || '';
                previewTitle.textContent = card.dataset.name || 'N/A';
                previewOrganizer.textContent = card.dataset.organizer || 'N/A';
                previewContact.textContent = card.dataset.contact || 'N/A';
                previewLocation.textContent = card.dataset.location || 'N/A';
                previewVenue.textContent = card.dataset.venue || 'N/A';

                const start = new Date(card.dataset.startdate);
                const end = new Date(card.dataset.enddate);
                previewDate.textContent = (isNaN(start) || isNaN(end))
                    ? 'N/A'
                    : `${start.toLocaleDateString()} - ${end.toLocaleDateString()}`;

                previewDesc.textContent = card.dataset.description || 'No description';
                previewEventId.value = card.dataset.id || '';

                try {
                    ticketData = JSON.parse(card.dataset.ticketdata || '[]');
                } catch {
                    ticketData = [];
                }

                renderTicketCategories();
                modal.style.display = 'flex';
            });
        });

        closeBtn.addEventListener('click', () => modal.style.display = 'none');
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });

        fullscreenIcon.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                img.requestFullscreen?.();
                img.webkitRequestFullscreen?.();
                img.msRequestFullscreen?.();
            } else {
                document.exitFullscreen?.();
            }
        });

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
            const isFavorite = favIcon.textContent === 'favorite';
            favIcon.textContent = isFavorite ? 'favorite_border' : 'favorite';
            favIcon.classList.toggle('fav-active', !isFavorite);
            localStorage.setItem('favoriteActive', String(!isFavorite));
        });

        document.querySelectorAll('.share-container').forEach((shareWrapper) => {
            const shareIcon = shareWrapper.querySelector('.share-icon');
            const shareOptions = shareWrapper.querySelector('.share-options');
            const card = shareWrapper.closest('.image-box-floating');

            shareIcon.addEventListener('click', (e) => {
                e.stopPropagation();
                const isExpanded = shareWrapper.classList.toggle('expanded');
                shareOptions.classList.toggle('active', isExpanded);
                card.classList.toggle('share-active', isExpanded);
            });

            document.addEventListener('click', (e) => {
                if (!shareWrapper.contains(e.target)) {
                    shareWrapper.classList.remove('expanded');
                    shareOptions.classList.remove('active');
                    card.classList.remove('share-active');
                }
            });
        });

        document.querySelectorAll('.photo-thumbnails .thumb').forEach(thumb => {
            thumb.addEventListener('click', function () {
                img.src = this.src;
                document.querySelectorAll('.photo-thumbnails .thumb').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>

