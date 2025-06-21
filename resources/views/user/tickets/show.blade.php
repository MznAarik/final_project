<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Tickets</title>
</head>

@php
    $categories = json_decode($ticket->event->ticket_category_price, true);
@endphp

<body>
    <h1>Ticket Details</h1>
    <p><strong>ID:</strong> {{ $ticket->id }}</p>
    <p>User Name: {{ $ticket->user->name }}</p>
    <p>Event Name: {{ $ticket->event->name }}</p>
    <p>Event Date: {{ $ticket->event->start_date }}</p>
    <p>Event Location: {{ $ticket->event->location }}</p>
    <p><strong>Status:</strong> {{ $ticket->status }}</p>
    <p><strong>Batch Number:</strong> {{ $ticket->batch_code}}</p>
    <h3>Ticket Categories</h3>
    <ul>
        @foreach($categories as $item)
            <li>
                <strong>Category:</strong>
                <t />{{ $item['category'] }} <br>
                <strong>Price:</strong>
                <t />{{ $ticket->event['currency']}}{{ number_format($item['price'], 2) }} <br>
                <strong>Quantity:</strong>
                <t />{{ $ticket->quantity }} <br>
                <strong>Subtotal:</strong>
                <t />
                {{ $ticket->event['currency']}}{{ number_format($item['price'] * $ticket->quantity, 2) }}
            </li>
        @endforeach
    </ul>
    <p><strong>Total Price:</strong>
        <t />{{ $ticket->event['currency']}}{{ $ticket->total_price }}
    </p>
    <p><strong>Deadline:</strong>
        <t />{{ $ticket->deadline }}
    </p>
    <p><strong>Cancellation Reason:</strong>
        <t /> {{ $ticket->cancellation_reason }}
    </p>
</body>

</html>