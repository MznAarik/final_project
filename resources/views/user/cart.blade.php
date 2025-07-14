@extends('layouts.app')

@section('title', 'Cart')

@section('content')
    <div class="flex flex-col justify-center items-center h-screen bg-amber-100 ">

        <h1>Your Cart</h1>
        <p>View and manage your selected tickets before checkout.</p>
        @csrf

        <select name="category" required>
            @foreach($ticket_categories as $category => $price)
                <option value="{{ $category }}">{{ $category }} - Rs {{ $price }}</option>
            @endforeach
        </select>

        <input type="number" name="quantity" value="1" min="1" required>
        <button type="submit">Add to Cart</button>

    </div>
@endsection