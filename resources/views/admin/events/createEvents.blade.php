@extends('admin.layouts.app')

@section('content')
<div style="background-color: #f3f4f6; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem;">
    <div style="width: 48rem; margin-left: auto; margin-right: auto; background-color: #ffffff; padding: 2rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-radius: 0.5rem; position: relative; z-index: 1;">
        <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem; text-align: center; color: #1f2937;">Add Event</h1>
        
        @if ($errors->any())
            <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 1rem; margin-bottom: 1.5rem; border-radius: 0.375rem;">
                <ul style="list-style-type: disc; padding-left: 1.25rem; color: #dc2626;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 1.5rem;">
            @csrf            
            <!-- Event Title -->
            <div style="margin-bottom: 1.5rem;">
                <label for="name" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Event Title</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255"
                    style="width: 100%; border: 1px solid; @error('name') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                @error('name')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Venue and Status Row -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="flex: 1;">
                    <label for="venue" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Venue</label>
                    <input type="text" id="venue" name="venue" value="{{ old('venue') }}" required maxlength="255"
                        style="width: 100%; border: 1px solid; @error('venue') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('venue')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="flex: 1;">
                    <label for="status" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Status</label>
                    <select id="status" name="status" required
                        style="width: 100%; border: 1px solid; @error('status') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                        <option value="">-- Select Status --</option>
                        @foreach(['upcoming', 'exclusive', 'active', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(old('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Location -->
            <div style="margin-bottom: 1.5rem;">
                <label for="location" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Location</label>
                <input type="text" id="location" name="location" value="{{ old('location') }}" required maxlength="255"
                    style="width: 100%; border: 1px solid; @error('location') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                @error('location')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Country, Province, District Row -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="flex: 1;">
                    <label for="country_name" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Country</label>
                    <input type="text" id="country_name" name="country_name" value="{{ old('country_name', 'Nepal') }}" required
                        style="width: 100%; border: 1px solid; @error('country_name') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('country_name')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label for="province_name" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Province</label>
                    <select id="province_name" name="province_name" required
                        style="width: 100%; border: 1px solid; @error('province_name') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                        <option value="">-- Select Province --</option>
                        <option value="Koshi Pradesh" {{ old('province_name') == 'Koshi Pradesh' ? 'selected' : '' }}>Koshi Pradesh</option>
                        <option value="Madhesh Pradesh" {{ old('province_name') == 'Madhesh Pradesh' ? 'selected' : '' }}>Madhesh Pradesh</option>
                        <option value="Bagmati Pradesh" {{ old('province_name') == 'Bagmati Pradesh' ? 'selected' : '' }}>Bagmati Pradesh</option>
                        <option value="Gandaki Pradesh" {{ old('province_name') == 'Gandaki Pradesh' ? 'selected' : '' }}>Gandaki Pradesh</option>
                        <option value="Lumbini Pradesh" {{ old('province_name') == 'Lumbini Pradesh' ? 'selected' : '' }}>Lumbini Pradesh</option>
                        <option value="Karnali Pradesh" {{ old('province_name') == 'Karnali Pradesh' ? 'selected' : '' }}>Karnali Pradesh</option>
                        <option value="Sudurpashchim Pradesh" {{ old('province_name') == 'Sudurpashchim Pradesh' ? 'selected' : '' }}>Sudurpashchim Pradesh</option>
                    </select>
                    @error('province_name')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label for="district_name" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">District</label>
                    <select id="district_name" name="district_name" required
                        style="width: 100%; border: 1px solid; @error('district_name') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                        <option value="">-- Select District --</option>
                    </select>
                    @error('district_name')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Capacity and Image Row -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="flex: 1;">
                    <label for="capacity" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Capacity</label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity') }}" min="1" required
                        style="width: 100%; border: 1px solid; @error('capacity') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('capacity')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label for="image" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Event Image</label>
                    <input type="file" id="image" name="image" accept="image/*"
                        style="width: 100%; border: 1px solid; @error('image') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('image')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
          <div style="margin-bottom: 1.5rem;">
  <label for="description" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">
    Description
  </label>
  <textarea id="description" name="description" rows="5" required
    style="width: 100%; border: 1px solid; 
           @error('description') border-color: #ef4444; @else border-color: #d1d5db; @enderror
           border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; resize: vertical; transition: border-color 0.2s;">
    {{ old('description') }}
  </textarea>
  @error('description')
    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">
      {{ $message }}
    </p>
  @enderror
</div>


            <!-- Contact Email and Organizer Row -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="flex: 1;">
                    <label for="contact_info" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Contact Email</label>
                    <input type="email" id="contact_info" name="contact_info" value="{{ old('contact_info') }}" required
                        style="width: 100%; border: 1px solid; @error('contact_info') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('contact_info')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label for="organizer" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Organizer</label>
                    <input type="text" id="organizer" name="organizer" value="{{ old('organizer') }}" required maxlength="255"
                        style="width: 100%; border: 1px solid; @error('organizer') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('organizer')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dates Row -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="flex: 1;">
                    <label for="start_date" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Start Date & Time</label>
                    <input type="datetime-local" id="start_date" name="start_date" value="{{ old('start_date') }}" required
                        style="width: 100%; border: 1px solid; @error('start_date') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('start_date')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label for="end_date" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">End Date & Time</label>
                    <input type="datetime-local" id="end_date" name="end_date" value="{{ old('end_date') }}" required
                        style="width: 100%; border: 1px solid; @error('end_date') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('end_date')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Currency and Event Category Row -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="flex: 1;">
                    <label for="currency" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Currency</label>
                    <select id="currency" name="currency" required
                        style="width: 100%; border: 1px solid; @error('currency') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                        <option value="">-- Select Currency --</option>
                        <option value="NPR" {{ old('currency') == 'NPR' ? 'selected' : '' }}>NPR (Nepalese Rupee)</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                        <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>INR (Indian Rupee)</option>
                    </select>
                    @error('currency')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label for="event_category" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Event Category</label>
                    <select id="event_category" name="event_category"
                        style="width: 100%; border: 1px solid; @error('event_category') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                        <option value="">-- Select Category --</option>
                        <option value="Conference" {{ old('event_category') == 'Conference' ? 'selected' : '' }}>Conference</option>
                        <option value="Workshop" {{ old('event_category') == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                        <option value="Seminar" {{ old('event_category') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                        <option value="Concert" {{ old('event_category') == 'Concert' ? 'selected' : '' }}>Concert</option>
                        <option value="Festival" {{ old('event_category') == 'Festival' ? 'selected' : '' }}>Festival</option>
                        <option value="Exhibition" {{ old('event_category') == 'Exhibition' ? 'selected' : '' }}>Exhibition</option>
                        <option value="Sports" {{ old('event_category') == 'Sports' ? 'selected' : '' }}>Sports</option>
                        <option value="Other" {{ old('event_category') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('event_category')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Ticket Categories -->
            <div id="ticket-categories" style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Ticket Categories</label>
                @php 
                    $ticketCats = old('ticket_category_price') ?? [['category' => '', 'price' => '']]; 
                @endphp
                @foreach($ticketCats as $i => $ticketCat)
                <div class="ticket-category-row" style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: center;">
                    <input type="text" name="ticket_category_price[{{ $i }}][category]" placeholder="Category (e.g., VIP, General, Student)" required maxlength="50" value="{{ $ticketCat['category'] ?? ''}}"
                        style="flex: 1; border: 1px solid; @error("ticket_category_price.$i.category") border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    <input type="number" name="ticket_category_price[{{ $i }}][price]" placeholder="Price" required min="0" step="0.01" value="{{ $ticketCat['price'] ?? ''}}"
                        style="flex: 1; border: 1px solid; @error("ticket_category_price.$i.price") border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @if($i > 0)
                    <button type="button" onclick="removeCategory(this)" 
                        style="background-color: #ef4444; color: white; padding: 0.5rem; border: none; border-radius: 0.375rem; cursor: pointer; font-size: 0.875rem;">
                        Remove
                    </button>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <button type="button" onclick="addCategory()"
                    style="flex: 1; background-color: #3b82f6; color: #ffffff; padding: 0.75rem 1rem; border: none; border-radius: 0.375rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s;">
                    Add Category
                </button>
                <button type="submit"
                    style="flex: 2; background-color: #10b981; color: #ffffff; padding: 0.75rem 1rem; border: none; border-radius: 0.375rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s;">
                    Submit Event
                </button>
                <a href="{{ route('events.index') }}" 
                    style="flex: 1; background-color: #6b7280; color: #ffffff; padding: 0.75rem 1rem; border: none; border-radius: 0.375rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s; text-decoration: none; text-align: center; display: inline-block;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
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

    const provinceSelect = document.getElementById('province_name');
    const districtSelect = document.getElementById('district_name');

    // Province-District functionality
    provinceSelect.addEventListener('change', function() {
        const selectedProvince = this.value;
        const districts = provinceDistrictData[selectedProvince] || [];
        
        // Clear existing district options
        districtSelect.innerHTML = '<option value="">-- Select District --</option>';
        
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

    // Initialize district dropdown based on old input (for form validation errors)
    document.addEventListener('DOMContentLoaded', function() {
        const oldProvince = "{{ old('province_name') }}";
        const oldDistrict = "{{ old('district_name') }}";
        
        if (oldProvince) {
            provinceSelect.value = oldProvince;
            const districts = provinceDistrictData[oldProvince] || [];
            
            districtSelect.innerHTML = '<option value="">-- Select District --</option>';
            districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district;
                option.textContent = district;
                if (district === oldDistrict) {
                    option.selected = true;
                }
                districtSelect.appendChild(option);
            });
            districtSelect.disabled = false;
        } else {
            districtSelect.disabled = true;
        }
    });

    // Ticket category management
    let categoryIndex = {{ count(old('ticket_category_price', [['category' => '', 'price' => '']])) }};
    
    function addCategory() {
        const container = document.getElementById('ticket-categories');
        const div = document.createElement('div');
        div.className = 'ticket-category-row';
        div.style.cssText = 'display: flex; gap: 1rem; margin-bottom: 1rem; align-items: center;';
        div.innerHTML = `
            <input type="text" name="ticket_category_price[${categoryIndex}][category]" placeholder="Category (e.g., VIP, General, Student)" required maxlength="50"
                style="flex: 1; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
            <input type="number" name="ticket_category_price[${categoryIndex}][price]" placeholder="Price" required min="0" step="0.01"
                style="flex: 1; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
            <button type="button" onclick="removeCategory(this)" 
                style="background-color: #ef4444; color: white; padding: 0.5rem; border: none; border-radius: 0.375rem; cursor: pointer; font-size: 0.875rem;">
                Remove
            </button>
        `;
        container.appendChild(div);
        categoryIndex++;
    }

    function removeCategory(button) {
        button.parentElement.remove();
    }

    // Date validation
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = new Date(this.value);
        const endDateInput = document.getElementById('end_date');
        
        // Set minimum end date to start date
        endDateInput.min = this.value;
        
        // Clear end date if it's before start date
        if (endDateInput.value && new Date(endDateInput.value) < startDate) {
            endDateInput.value = '';
        }
    });

    document.getElementById('end_date').addEventListener('change', function() {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(this.value);
        
        if (endDate < startDate) {
            alert('End date cannot be before start date');
            this.value = '';
        }
    });
</script>

@endsection