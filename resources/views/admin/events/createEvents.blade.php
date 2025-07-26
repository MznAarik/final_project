@extends('admin.layouts.app')

@section('content')
<div style="background-color: #f3f4f6; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem;">
    <div style="width: 48rem; margin-left: auto; margin-right: auto; background-color: #ffffff; padding: 2rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-radius: 0.5rem; position: relative; z-index: 1;">
        <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem; text-align: center; color: #1f2937;">Add Event</h1>
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

            <!-- Venue -->
            <div style="margin-bottom: 1.5rem;">
                <label for="venue" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Venue</label>
                <input type="text" id="venue" name="venue" value="{{ old('venue') }}" required maxlength="255"
                    style="width: 100%; border: 1px solid; @error('venue') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                @error('venue')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <label for="location" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" required maxlength="255"
                        style="width: 100%; border: 1px solid; @error('location') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('location')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div style="flex: 1;">
                    <label for="status" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Status</label>
                    <select id="status" name="status"
                        style="width: 100%; border: 1px solid; @error('status') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                        <option value="">-- Select Status --</option>
                        @foreach(['upcoming', 'active', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(old('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Capacity -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
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
                <label for="description" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Description</label>
                <textarea id="description" name="description" rows="10" required
                    style="width: 100%; border: 1px solid; @error('description') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; height: 5rem; resize: vertical; transition: border-color 0.2s;">{{ old('description') }}</textarea>
                @error('description')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Email -->
            <div style="margin-bottom: 1.5rem;">
                <label for="contact_info" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Contact Email</label>
                <input type="email" id="contact_info" name="contact_info" value="{{ old('contact_info') }}" required
                    style="width: 100%; border: 1px solid; @error('contact_info') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                @error('contact_info')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dates -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
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

            <!-- Organizer -->
            <div style="margin-bottom: 1.5rem;">
                <label for="organizer" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Organizer</label>
                <input type="text" id="organizer" name="organizer" value="{{ old('organizer') }}" required maxlength="255"
                    style="width: 100%; border: 1px solid; @error('organizer') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                @error('organizer')
                    <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Country, Province, District -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <label for="country_name" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Country</label>
                    <input type="text" id="country_name" name="country_name" value="{{ old('country_name') }}" required
                        style="width: 100%; border: 1px solid; @error('country_name') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('country_name')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label for="province_name" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Province</label>
                    <input type="text" id="province_name" name="province_name" value="{{ old('province_name') }}" required
                        style="width: 100%; border: 1px solid; @error('province_name') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('province_name')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label for="district_name" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">District</label>
                    <input type="text" id="district_name" name="district_name" value="{{ old('district_name') }}" required
                        style="width: 100%; border: 1px solid; @error('district_name') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('district_name')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Currency and Event Category -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <label for="currency" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Currency</label>
                    <input type="text" id="currency" name="currency" value="{{ old('currency') }}" required
                        style="width: 100%; border: 1px solid; @error('currency') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('currency')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div style="flex: 1;">
                    <label for="event_category" style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Event Category</label>
                    <input type="text" id="event_category" name="event_category" value="{{ old('event_category') }}" maxlength="100"
                        style="width: 100%; border: 1px solid; @error('event_category') border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    @error('event_category')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Ticket Categories -->
            <div id="ticket-categories" style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.25rem; color: #374151;">Ticket Categories</label>
                @php $ticketCats = old('ticket_category_price') ?? [['category' => '', 'price' => '']]; @endphp
                @foreach($ticketCats as $i => $ticketCat)
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <input type="text" name="ticket_category_price[{{ $i }}][category]" placeholder="Category" required maxlength="50" value="{{ $ticketCat['category'] ?? ''}}"
                        style="flex: 1; border: 1px solid; @error("ticket_category_price.$i.category") border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                    <input type="number" name="ticket_category_price[{{ $i }}][price]" placeholder="Price" required min="0" step="0.01" value="{{ $ticketCat['price'] ?? ''}}"
                        style="flex: 1; border: 1px solid; @error("ticket_category_price.$i.price") border-color: #ef4444; @else border-color: #d1d5db; @enderror border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
                </div>
                @endforeach
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <button type="button" onclick="addCategory()"
                        style="width: 100%; background-color: #3b82f6; color: #ffffff; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s;">
                        Add Category
                    </button>
                </div>
                <div style="flex: 1;">
                    <button type="submit"
                        style="width: 100%; background-color: #10b981; color: #ffffff; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s;">
                        Submit Event
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let categoryIndex = {{ count(old('ticket_category_price', [])) }};
    function addCategory() {
        const container = document.getElementById('ticket-categories');
        const div = document.createElement('div');
        div.style.cssText = 'display: flex; gap: 1rem; margin-bottom: 1rem;';
        div.innerHTML = `
            <input type="text" name="ticket_category_price[${categoryIndex}][category]" placeholder="Category" required maxlength="50"
                style="flex: 1; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
            <input type="number" name="ticket_category_price[${categoryIndex}][price]" placeholder="Price" required min="0" step="0.01"
                style="flex: 1; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 1rem; transition: border-color 0.2s;">
        `;
        container.appendChild(div);
        categoryIndex++;
    }
</script>

@endsection