<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
</head>

<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (isset($message))
            <p>{{ $message }}</p>
        @endif
        <p>Welcome, {{ Auth::user()->name }}!</p>

    </div>
</body>

</html>