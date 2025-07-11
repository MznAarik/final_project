@extends('admin.layouts.app')

@section('content')
    <div class="container text-center grid align-center justify-items-center font-bold underline">
        <h2>Admin Dashboard</h2>
        @if(session('success'))
            <p>Session success exists: {{ session('success') }}</p>
        @endif
        @if (isset($message))
            <p>{{ $message }}</p>
        @endif
        <p>Welcome, {{ Auth::user()->name }}!</p>

    </div>
@endsection