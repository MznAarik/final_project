<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Tickets</title>
</head>

<body>
    <h1>Ticket Details</h1>
    <p><strong>ID:</strong> {{ $ticket->id }}</p>
    <p>User Name: {{ $ticket->user->name }}</p>
    <p>Event Name: {{ $ticket->event->name }}</p>
    <p>Event Date: {{ $ticket->event->start_date }}</p>
    <p>Event Location: {{ $ticket->event->location }}</p>
    <p><strong>Status:</strong> {{ $ticket->status }}</p>
    <label>Price per Ticket: {{ number_format($ticket->event->ticket_price, 2) }}</label><br>
    <p><strong>Quantity:</strong> {{ $ticket->quantity }}</p>
    <p><strong>Total Price:</strong> {{ $ticket->total_price }}</p>
    <p><strong>Deadline:</strong> {{ $ticket->deadline }}</p>
    <p><strong>Cancellation Reason:</strong> {{ $ticket->cancellation_reason }}</p>
</body>

</html>