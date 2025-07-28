@php
    $categories = json_decode($ticket->ticket_details, true);
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Event Ticket</title>
</head>

<body
    style="font-family: Arial, sans-serif; background-color: #fff1f2; margin: 0; padding: 20px; text-transform: capitalize;">
    <table align="center" cellpadding="0" cellspacing="0" border="0" width="100%"
        style="max-width: 400px; background-color: #ffffff; border-collapse: collapse;">
        <tr>
            <td align="center" bgcolor="#b91c1c" style="padding: 32px 24px; color: #ffffff;">
                <h1 style="margin: 0; font-size: 28px;">🎫 Event Ticket</h1>
                <h2 style="margin: 8px 0 0 0; font-size: 20px;">{{ $ticket->event->name ?? 'Event Name' }}</h2>
            </td>
        </tr>

        <tr>
            <td align="center" bgcolor="#ffe4e6" style="padding: 32px 24px;">
                @if (!empty($ticket->qr_code))
                    <img src="{{ Storage::url($ticket->qr_code) }}" alt="QR Code" width="200" height="200"
                        style="display:block; border: 2px solid #b91c1c; border-radius: 8px;">
                @else
                    <p style="color: #dc2626; font-weight: bold;">No QR code available for this ticket.</p>
                @endif
            </td>
        </tr>

        <tr>
            <td style="padding: 24px;">
                @if (!empty($categories) && is_array($categories))
                    @foreach($categories as $item)
                        @if (isset($item['category']) && isset($item['quantity']))
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin-bottom: 16px; border: 1px solid #fecaca; border-radius: 6px;">
                                <tr>
                                    <td colspan="2" style="padding: 12px 16px; font-weight: bold; background-color: #fef2f2;">
                                        {{ $item['category'] ?? 'Category' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 16px; color: #6b7280;">Quantity:</td>
                                    <td style="padding: 8px 16px; text-align: right; font-weight: bold;">
                                        {{ $item['quantity'] ?? 1 }} {{ ($item['quantity'] ?? 1) > 1 ? 'seats' : 'seat' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 16px; color: #6b7280;">Subtotal:</td>
                                    <td style="padding: 8px 16px; text-align: right; font-weight: bold; color: #b91c1c;">
                                        {{ $ticket->event['currency'] ?? '$' }}
                                        {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}
                                    </td>
                                </tr>
                            </table>
                        @endif
                    @endforeach
                @endif

                <table width="100%" cellpadding="0" cellspacing="0"
                    style="margin-top: 24px; background-color: #b91c1c; color: #ffffff; border-radius: 6px;">
                    <tr>
                        <td style="padding: 16px; font-weight: bold;">Total Amount</td>
                        <td
                            style="padding: 16px; text-align: right; font-size: 18px; font-weight: bold; text-transform: uppercase;">
                            {{ $ticket->event['currency'] ?? '$' }} {{ $ticket->total_price ?? '0.00' }}
                        </td>
                    </tr>
                </table>

                @if (!empty($ticket->deadline))
                    <div
                        style="margin-top: 16px; padding: 12px; background-color: #fff7ed; border: 1px solid #fdba74; border-radius: 6px;">
                        <strong style="color: #c2410c;">Event Deadline:</strong>
                        <div style="color: #c2410c;">{{ $ticket->deadline }}</div>
                    </div>
                @endif

                @if (!empty($ticket->cancellation_reason))
                    <div
                        style="margin-top: 16px; padding: 12px; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 6px;">
                        <strong style="color: #dc2626;">Cancellation Reason:</strong>
                        <div style="color: #dc2626;">{{ $ticket->cancellation_reason }}</div>
                    </div>
                @endif
            </td>
        </tr>

        <tr>
            <td align="center" style="padding: 20px; font-size: 12px; color: #6b7280;">
                <div style="margin-bottom: 8px; font-weight: bold; color: #059669;">
                    <span
                        style="display: inline-block; width: 8px; height: 8px; background-color: #10b981; border-radius: 50%; margin-right: 4px;"></span>
                    Ready to scan
                </div>
                Present this QR code at the event entrance for quick and secure access to your reserved seats.
            </td>
        </tr>
    </table>
</body>

</html>