<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Events</title>
</head>

<body>
    <h1>Add Events </h1>
    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Event Title:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="date">Event Date:</label>
            <input type="date" id="date" name="date" required>
        </div>
        <div>
            <label for="location">Event Location:</label>
            <input type="text" id="location" name="location" required>
        </div>
        <div>
            <label for="description">Event Description:</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        <button type="submit">Add Event</button>
    </form>
</body>

</html>