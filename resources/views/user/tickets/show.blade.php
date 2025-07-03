@php
    $categories = json_decode($ticket->ticket_details, true);
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Ticket</title>
</head>

<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; text-transform:capitalize;">
    <div style="width:100%; padding:40px 0; text-align:center;">
        <div
            style="max-width: 400px; margin: auto; border:1px solid #ccc; border-radius:5px; background-color:#f9f9f9; padding:15px;">
            <h2>Your Ticket</h2>

            @if ($ticket->qr_code)
                <img src="{{ Storage::url($ticket->qr_code) }}" alt="Ticket QR Code"
                    style="width:200px; height:200px; border:1px solid #ccc;">
            @else
                <p style="color:red;">No QR code available for this ticket.</p>
            @endif

            <p><strong>Event Name:</strong> {{ $ticket->event->name }}</p>

            <div style="margin:0 80px;">
                <ul style="padding: 0; list-style: none; text-align: left;">
                    @foreach($categories as $item)
                        <li style="margin-bottom: 10px;">
                            <strong>Seat Category:</strong> {{ $item['category'] }}<br>
                            <strong>Quantity:</strong> {{ $item['quantity'] }}<br>
                            <strong>Subtotal:</strong> {{ $ticket->event['currency'] }}
                            {{ number_format($item['price'] * $item['quantity'], 2) }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <p><strong>Total Price: <u>{{ $ticket->event['currency'] }} {{ $ticket->total_price }}</u></strong></p>
            <p><strong>Deadline:</strong> {{ $ticket->deadline }}</p>
            <p><strong>Cancellation Reason:</strong> {{ $ticket->cancellation_reason }}</p>
        </div>

        <p style="font-size:14px; color:#555; margin-top:15px;">
            To use your ticket, show this QR code at the event.
        </p>
    </div>
</body>

</html>