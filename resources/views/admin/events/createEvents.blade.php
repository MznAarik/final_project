<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 py-10">
    <div class="max-w-3xl mx-auto bg-white p-8 shadow-lg rounded-lg">
        <h1 class="text-2xl font-bold mb-6 text-center">Add Event</h1>
        <!-- Inside your Blade view -->
        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Event Title -->
            <div>
                <label for="name" class="block font-semibold mb-1">Event Title</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255"
                    class="w-full border @error('name') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Venue -->
            <div>
                <label for="venue" class="block font-semibold mb-1">Venue</label>
                <input type="text" id="venue" name="venue" value="{{ old('venue') }}" required maxlength="255"
                    class="w-full border @error('venue') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                @error('venue')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div class="flex gap-4 mb-2">
                <div class="w-1/2">
                    <label for="location" class="block font-semibold mb-1">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" required
                        maxlength="255"
                        class="w-full border @error('location') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="w-1/2"> <label for="status" class="block font-semibold mb-1">Status</label>
                    <select id="status" name="status"
                        class="w-full border @error('status') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                        <option value="">-- Select Status --</option>
                        @foreach(['upcoming', 'active', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(old('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Capacity -->
            <div class="flex gap-4 mb-2">
                <div class="w-1/2">
                    <label for="capacity" class="block font-semibold mb-1">Capacity</label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity') }}" min="1" required
                        class="w-full border @error('capacity') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('capacity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-1/2">
                    <label for="image" class="block font-semibold mb-1">Event Image</label>
                    <input type="file" id="image" name="image" accept="image/*"
                        class="w-full border @error('image') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <!-- Description -->
            <div>
                <label for="description" class="block font-semibold mb-1">Description</label>
                <textarea id="description" name="description" required
                    class="w-full border @error('description') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2 h-28">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Email -->
            <div>
                <label for="contact_info" class="block font-semibold mb-1">Contact Email</label>
                <input type="email" id="contact_info" name="contact_info" value="{{ old('contact_info') }}" required
                    class="w-full border @error('contact_info') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                @error('contact_info')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dates -->
            <div class="flex gap-4">
                <div class="flex-1">
                    <label for="start_date" class="block font-semibold mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required
                        class="w-full border @error('start_date') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-1">
                    <label for="end_date" class="block font-semibold mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required
                        class="w-full border @error('end_date') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Organizer -->
            <div>
                <label for="organizer" class="block font-semibold mb-1">Organizer</label>
                <input type="text" id="organizer" name="organizer" value="{{ old('organizer') }}" required
                    maxlength="255"
                    class="w-full border @error('organizer') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                @error('organizer')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tickets Sold -->
            <div class="flex gap-4 mb-2">
                <div class="w-1/3">
                    <label for="country_name" class="block font-semibold mb-1">Country </label>
                    <input type="text" id="country_name" name="country_name" value="{{ old('country_name') }}" required
                        class="w-full border @error('country_name') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('country_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-1/3">
                    <label for="province_name" class="block font-semibold mb-1">Province </label>
                    <input type="text" id="province_name" name="province_name" value="{{ old('province_name') }}"
                        required
                        class="w-full border @error('province_name') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('province_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-1/3">
                    <label for="district_name" class="block font-semibold mb-1">District </label>
                    <input type="text" id="district_name" name="district_name" value="{{ old('district_name') }}"
                        required
                        class="w-full border @error('district_name') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('district_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Event Category -->
            <div class="flex gap-4 mb-2">
                <div class="w-1/2">
                    <label for="currency" class="block font-semibold mb-1">Currency</label>
                    <input type="text" id="currency" name="currency" value="{{ old('currency') }}" required
                        class="w-full border @error('currency') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('currency')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-1/2">
                    <label for="event_category" class="block font-semibold mb-1">Event Category</label>
                    <input type="text" id="event_category" name="event_category" value="{{ old('event_category') }}"
                        maxlength="100"
                        class="w-full border @error('event_category') border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    @error('event_category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Ticket Categories -->
            <div id="ticket-categories">
                <label class="block font-semibold mb-1">Ticket Categories</label>
                @php $ticketCats = old('ticket_category_price', [['category' => '', 'price' => '']]); @endphp
                @foreach($ticketCats as $i => $ticketCat)
                <div class="flex gap-4 mb-2">
                    <input type="text" name="ticket_category_price[{{ $i }}][category]" placeholder="Category" required
                        maxlength="50" value="{{ $ticketCat['category'] ?? '' }}"
                        class="flex-1 border @error("ticket_category_price.$i.category") border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                    <input type="number" name="ticket_category_price[{{ $i }}][price]" placeholder="Price" required
                        min="0" step="0.01" value="{{ $ticketCat['price'] ?? '' }}"
                        class="flex-1 border @error("ticket_category_price.$i.price") border-red-500 @else border-gray-300 @enderror rounded px-4 py-2">
                </div>
                @endforeach
            </div>

            <!-- Image -->
            <div class="flex gap-4 mb-2">
                <div class="w-1/2">
                    <!-- Add Category Button -->
                    <button type="button" onclick="addCategory()"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 ">Add
                        Category</button>
                </div>
                <div class="w-1/2">
                    <!-- Submit -->
                    <button type="submit"
                        class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700">
                        Submit Event
                    </button>
                </div>
            </div>
        </form>

    </div>

    <script>
        let categoryIndex = 1;
        function addCategory() {
            const container = document.getElementById('ticket-categories');
            const div = document.createElement('div');
            div.classList.add('flex', 'gap-4', 'mb-2');
            div.innerHTML = `
                <input type="text" name="ticket_category_price[${categoryIndex}][category]" placeholder="Category" required maxlength="50"
                    class="flex-1 border border-gray-300 rounded px-4 py-2">
                <input type="number" name="ticket_category_price[${categoryIndex}][price]" placeholder="Price" required min="0" step="1"
                    class="flex-1 border border-gray-300 rounded px-4 py-2">
            `;
            container.appendChild(div);
            categoryIndex++;
        }
    </script>
</body>

</html>