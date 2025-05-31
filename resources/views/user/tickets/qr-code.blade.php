<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket #{{ $ticket->id }}</title>
</head>

<body>
    <div><img src="{{ $ticket->qr_code }}" alt="QR Code for Ticket #{{ $ticket->id }}"></div>
</body>

</html>