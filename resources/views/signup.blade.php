<!-- resources/views/auth/signup.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>

<body>
    <div class="container">
        <h2>Sign Up</h2>
        @if (session('message'))
            <div class="alert alert-{{ session('status') ?: 'info' }}">
                {{ session('message') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div>
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
            <div>
                <label for="gender">Gender</label>
                <select name="gender" id="gender" required>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div>
                <label for="phoneno">Phone Number</label>
                <input type="text" name="phoneno" id="phoneno" value="{{ old('phoneno') }}" required>
            </div>
            <div>
                <label for="address">Address</label>
                <textarea name="address" id="address" required>{{ old('address') }}</textarea>
            </div>
            <div>
                <label for="district_id">District ID</label>
                <input type="number" name="district_id" id="district_id" value="{{ old('district_id') }}">
            </div>
            <div>
                <label for="province_id">Province ID</label>
                <input type="number" name="province_id" id="province_id" value="{{ old('province_id') }}">
            </div>
            <input type="hidden" name="role" value="user">
            <div>
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}">
            </div>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
</body>

</html>