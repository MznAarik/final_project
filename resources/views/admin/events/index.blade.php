<!-- resources/views/events/index.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center">All events</h1>

        @if($events->isEmpty())
            <p class="text-center text-gray-600">No events found.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <div class="bg-white p-4 rounded shadow hover:shadow-md transition">
                        <h2 class="text-xl font-semibold mb-2">{{ $event->name }}</h2>
                        <p><strong>Holder:</strong> {{ $event->user->name }}</p>
                        <p><strong>Status:</strong> {{ $event->status }}</p>
                        <p><strong>Deadline:</strong> {{ $event->deadline }}</p>

                        @if($event->img_path)
                            <img src="{{ Storage::url($event->img_path) }}" alt="Event Image" class="mt-4 w-32 h-32 mx-auto border">
                        @else
                            <p class="text-red-600 text-sm mt-2">No event image available</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>

</html>