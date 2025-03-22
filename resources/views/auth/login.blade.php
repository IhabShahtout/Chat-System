<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    @vite(['resources/js/app.js'])
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9fa, #e3eaf4);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            transition: transform 0.3s ease;
        }
        .login-card:hover {
            transform: translateY(-5px);
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .input-group-text {
            background: #f8f9fa;
            border-right: none;
        }
        .form-control {
            border-left: none;
        }
        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .dark-mode-toggle {
            position: absolute;
            top: 20px;
            left: 20px;
            cursor: pointer;
        }
        body.dark-mode {
            background: #212529;
            color: #f8f9fa;
        }
        body.dark-mode .login-card {
            background: #343a40;
            color: #f8f9fa;
        }
        body.dark-mode .form-control {
            background: #495057;
            border-color: #6c757d;
            color: #f8f9fa;
        }
        body.dark-mode .input-group-text {
            background: #495057;
            border-color: #6c757d;
            color: #f8f9fa;
        }
        body.dark-mode .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }
    </style>
</head>
<body>
<!-- Dark Mode Toggle -->
<button class="btn btn-outline-secondary dark-mode-toggle"><i class="bi bi-moon-stars"></i></button>

<div class="login-card">
    <div class="text-center">
        <!-- Chat System Logo -->
        <img src="{{asset('assets/images/logo3.jpg')}}" alt="Chat System Logo" class="logo">
        <h1 class="h4 fw-bold">Chat Login</h1>
        <p class="text-muted">Sign in to start chatting!</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email/Username Field with Icon -->
        <div class="mb-3">
            <label for="email" class="form-label">Email or Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus aria-describedby="emailHelp">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Password Field with Icon -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required aria-describedby="passwordHelp">
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

{{--        <!-- Remember Me with Icon -->--}}
{{--        <div class="mb-3 form-check">--}}
{{--            <input type="checkbox" class="form-check-input" id="remember" name="remember">--}}
{{--            <label class="form-check-label" for="remember"><i class="bi bi-check-circle me-1"></i> Remember Me</label>--}}
{{--        </div>--}}

        <!-- Submit Button with Icon -->
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>

        <!-- Forgot Password Link with Icon -->
{{--        @if (Route::has('password.request'))--}}
{{--            <div class="text-center mt-3">--}}
{{--                <a href="{{ route('password.request') }}" class="text-decoration-none"><i class="bi bi-question-circle me-1"></i>Forgot your password?</a>--}}
{{--            </div>--}}
{{--        @endif--}}
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.querySelector('.dark-mode-toggle');
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('dark-mode', document.body.classList.contains('dark-mode'));
        });

        if (localStorage.getItem('dark-mode') === 'true') {
            document.body.classList.add('dark-mode');
        }
        // Add subtle form input animation
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('shadow-sm');
            });
            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('shadow-sm');
            });
        });
    });
</script>
</body>
</html>
