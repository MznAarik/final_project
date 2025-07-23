<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = session()->get('cart', []);
        return view('user.cart', compact('cartItems'));
    }

    public function addCart(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:events,id',
            'ticketQuantities' => 'required|json',
        ]);

        $eventId = $request->input('id');
        $ticketQuantities = json_decode($request->input('ticketQuantities'), true);

        $event = Event::findOrFail($eventId);
        if ($event->status == 'cancelled') {
            return redirect()->back()->withErrors(['event' => "Event {$event->name} is cancelled"]);
        }

        $ticketData = is_array($event->ticket_category_price) ? $event->ticket_category_price : json_decode($event->ticket_category_price, true);
        $validCategories = array_column($ticketData, 'category');
        $ticketsSold = $event->tickets_sold ?? 0;
        $eventCapacity = $event->capacity ?? 0;

        $enrichedTickets = [];
        $totalRequestedQuantity = 0;

        foreach ($ticketQuantities as $index => $item) {
            $category = $item['category'];
            $quantity = $item['quantity'];

            if (!in_array($category, $validCategories)) {
                return redirect()->back()->with(['status' => 2, 'error' => "Invalid ticket category: {$category}"]);
            }

            // Check available tickets
            $totalRequestedQuantity += $quantity;
            if ($ticketsSold + $totalRequestedQuantity > $eventCapacity) {
                return redirect()->back()->with(['status' => 2, 'error' => "Tickets Exceeded! Only " . ($eventCapacity - $ticketsSold) . " tickets available for {$event->name}"]);
            }

            $price = collect($ticketData)->firstWhere('category', $category)['price'] ?? 0;
            $subtotal = $price * $quantity;

            $enrichedTickets[$index] = [
                'category' => $category,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'eventName' => $event->name,
                'eventImage' => $event->img_path,
                'eventCurrency' => $event->currency,
            ];
        }

        // Store enriched data in session
        $sessionCart = session()->get('cart', []);
        $sessionCart[$eventId] = $enrichedTickets;
        session()->put('cart', $sessionCart);

        return redirect()->route('cart.index')->with(['status' => 1, 'message' => 'Ticket added to cart successfully!']);
    }

    public function updateCart(Request $request)
    {
        $cartItems = session()->get('cart', []);

        foreach ($request->input('quantity', []) as $eventId => $quantities) {
            if (isset($cartItems[$eventId])) {
                $event = Event::findOrFail($eventId);
                $ticketsSold = $event->tickets_sold ?? 0;
                $eventCapacity = $event->capacity ?? 0;
                $totalRequestedQuantity = 0;

                foreach ($quantities as $index => $quantity) {
                    if (isset($cartItems[$eventId][$index])) {
                        $totalRequestedQuantity += $quantity;
                    }
                }

                if ($ticketsSold + $totalRequestedQuantity > $eventCapacity) {
                    return redirect()->back()->with(['status' => 2, 'quantity' => "Tickets Exceeded! Only " . ($eventCapacity - $ticketsSold) . " tickets available for {$event->name}"]);
                }

                foreach ($quantities as $index => $quantity) {
                    if (isset($cartItems[$eventId][$index])) {
                        $quantity = max(1, (int) $quantity); // Ensure quantity is at least 1
                        $cartItems[$eventId][$index]['quantity'] = $quantity;
                        $cartItems[$eventId][$index]['subtotal'] = $cartItems[$eventId][$index]['price'] * $quantity;
                    }
                }
            }
        }

        session()->put('cart', $cartItems);
        return redirect()->back()->with(['status' => 1, 'message' => 'Cart updated successfully.']);
    }

    public function removeCart($eventId)
    {
        $cartItems = session()->get('cart', []);
        if (isset($cartItems[$eventId])) {
            unset($cartItems[$eventId]);
            session()->put('cart', $cartItems);
        }

        return redirect()->back()->with(['status' => 1, 'message' => 'Item removed from cart.']);
    }

    public function checkout(Request $request)
    {
        $cartItems = session()->get('cart', []);
        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Cart is empty.');
        }

        session()->forget('cart');
        return redirect()->route('home')->with('success', 'Checkout successful!');
    }

    public function removeSingle(string $eventId, $index)
    {
        $cartItems = session()->get('cart', []);
        if (isset($cartItems[$eventId])) {
            if ($index === '0' || $index === '-1') { // Use -1 or 0 to trigger "Remove All"
                unset($cartItems[$eventId]); // Remove all items for the event
            } elseif (isset($cartItems[$eventId][$index])) {
                unset($cartItems[$eventId][$index]);
                if (empty($cartItems[$eventId])) {
                    unset($cartItems[$eventId]);
                }
            }
            session()->put('cart', $cartItems);
        }
        return redirect()->back()->with(['status' => 3, 'message' => $index === '0' || $index === '-1' ? 'All items removed from cart.' : 'Item removed from cart.']);
    }
}