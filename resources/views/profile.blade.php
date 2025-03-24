<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.profile.title') }}</title>

    <!-- External Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap{{ app()->getLocale() === 'ar' ? '.rtl' : '' }}.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@6.6.6/css/flag-icons.min.css">
    @vite(['resources/js/app.js'])

    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --bg-gradient: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            --card-bg: #ffffff;
            --text-color: #212529;
            --muted-text: #6c757d;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --border-radius: 16px;
            --sidebar-bg: #f8f9fa;
            --top-bar-bg: #f8f9fa;
            --border-color: #e9ecef;
        }

        body.dark-mode {
            --bg-gradient: linear-gradient(135deg, #1a1d21, #343a40);
            --card-bg: #2c3036;
            --text-color: #e9ecef;
            --muted-text: #adb5bd;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            --sidebar-bg: #343a40;
            --top-bar-bg: #343a40;
            --border-color: #495057;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .container {
            max-width: 900px;
            margin: 2rem auto;
        }

        .card {
            background: var(--card-bg);
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            animation: fadeIn 0.5s ease-in;
            position: relative;
        }

        .sidebar {
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
        }

        .sidebar .nav-link {
            color: var(--text-color);
            transition: color 0.2s;
        }

        body.dark-mode .sidebar .nav-link {
            color: var(--text-color);
        }

        .sidebar .nav-link:hover {
            color: var(--primary-color);
        }

        .avatar {
            width: 90px;
            height: 90px;
            background: linear-gradient(45deg, #0d6efd, #4dabf7);
            color: white;
            font-size: 2.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s, background-color 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
            background-color: #0b5ed7;
        }

        .btn-outline-secondary {
            border-color: var(--secondary-color);
            color: var(--secondary-color);
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            transition: transform 0.2s, background-color 0.3s;
        }

        body.dark-mode .btn-outline-secondary {
            border-color: #adb5bd;
            color: #adb5bd;
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
            background-color: #e9ecef;
            color: var(--secondary-color);
        }

        body.dark-mode .btn-outline-secondary:hover {
            background-color: #495057;
            color: #e9ecef;
        }

        .btn-outline-danger {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            transition: transform 0.2s, background-color 0.3s;
        }

        body.dark-mode .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            transform: translateY(-2px);
            background-color: #f8d7da;
        }

        body.dark-mode .btn-outline-danger:hover {
            background-color: #dc3545;
            color: #fff;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .online { background-color: #28a745; }

        .collapse-toggle {
            cursor: pointer;
            color: var(--primary-color);
            text-decoration: underline;
        }

        .profile-details h5 {
            margin-bottom: 1.5rem;
        }

        .profile-details label {
            font-size: 0.9rem;
            color: var(--muted-text);
            font-weight: 500;
        }

        .profile-details p {
            margin-bottom: 0;
        }

        .top-bar {
            background: var(--top-bar-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1rem;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }

        .top-bar h5 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .language-dropdown .btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        body.dark-mode .language-dropdown .btn {
            background-color: #495057;
            border-color: var(--border-color);
            color: var(--text-color);
        }

        .language-dropdown .btn:hover {
            background-color: #f1f3f5;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        body.dark-mode .language-dropdown .btn:hover {
            background-color: #6c757d;
        }

        .language-dropdown .dropdown-menu {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideDown 0.3s ease;
            min-width: 180px;
            background-color: var(--card-bg);
        }

        .language-dropdown .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            color: var(--text-color);
            transition: all 0.2s ease;
        }

        .language-dropdown .dropdown-item:hover {
            background-color: #e9ecef;
        }

        body.dark-mode .language-dropdown .dropdown-item:hover {
            background-color: #495057;
        }

        .language-dropdown .dropdown-item.active {
            background-color: var(--primary-color);
            color: white;
        }

        .language-dropdown .dropdown-item .fi {
            margin-right: 0.5rem;
            width: 20px;
            height: 20px;
        }

        [dir="rtl"] .language-dropdown .dropdown-item .fi {
            margin-right: 0;
            margin-left: 0.5rem;
        }

        .dark-mode-toggle {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }

        [dir="rtl"] .dark-mode-toggle {
            right: auto;
            left: 1rem;
        }

        .text-muted {
            color: var(--muted-text) !important;
        }

        .badge.bg-success {
            background-color: #28a745 !important;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .sidebar { margin-bottom: 2rem; }
            .card { padding: 1.5rem; }
            .avatar { width: 70px; height: 70px; font-size: 1.8rem; }
            .top-bar { padding: 0.75rem; }
            .top-bar .col-12 { margin-bottom: 0.5rem; }
            .top-bar .btn { width: 100%; }
            .dark-mode-toggle { top: 0.5rem; right: 0.5rem; }
        }


    </style>
</head>
<body>
<div class="container">
    <div class="row top-bar">
        <div class="col-12 col-md-6 d-flex align-items-center">
            <h5>{{ __('messages.profile.title') }}</h5>
        </div>
        <div class="col-12 col-md-6 d-flex justify-content-end gap-3">
            <div class="dropdown language-dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="fi fi-{{ app()->getLocale() === 'en' ? 'gb' : 'sa' }} me-1"></span>{{ strtoupper(app()->getLocale()) }}
                </button>
                <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('change-language', 'en') }}">
                            <span class="fi fi-gb me-2"></span>English (EN)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ route('change-language', 'ar') }}">
                            <span class="fi fi-sa me-2"></span>العربية (AR)
                        </a>
                    </li>
                </ul>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="{{ __('messages.auth.logout') }}">
                    <i class="bi bi-box-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-3">
            <div class="sidebar">
                <h5 class="p-3 border-bottom mb-0" style="color: var(--muted-text);">Navigation</h5>
                <ul class="nav flex-column py-2">
                    <li class="nav-item">
                        <a href="{{ route('users') }}" class="nav-link d-flex align-items-center">
                            <i class="fas fa-users me-2 text-primary"></i>
                            <span>Users</span>
                        </a>
                    </li>
                   
                </ul>
            </div>
        </div>

        <div class="col-12 col-md-9">
            <div class="card">
                <button class="btn btn-outline-secondary dark-mode-toggle" id="darkModeToggle" aria-label="{{ __('messages.chat_users.dark_mode') }}">
                    <i class="bi bi-moon"></i>
                </button>

                <div class="text-center">
                    <div class="avatar mx-auto">
                        {{ substr($user->name, 0, 1) }}{{ substr(explode(' ', $user->name)[1] ?? '', 0, 1) }}
                    </div>
                    <h2>{{ $user->name }}</h2>
                    <p class="text-muted">{{ $user->email }}</p>
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <span class="status-dot online"></span>
                        <span class="badge bg-success">{{ __('messages.status.online') }}</span>
                    </div>
                </div>

                <hr>

                <div class="profile-details">
                    <h5>{{ __('messages.profile.details') }}</h5>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label>{{ __('messages.profile.name') }}</label>
                            <p>{{ $user->name }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label>{{ __('messages.profile.email') }}</label>
                            <p>{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.querySelector('#darkModeToggle');
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('dark-mode', isDarkMode);
            darkModeToggle.innerHTML = isDarkMode ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon"></i>';
            darkModeToggle.setAttribute('aria-label', isDarkMode ? 'Switch to light mode' : 'Switch to dark mode');
        });

        if (localStorage.getItem('dark-mode') === 'true') {
            document.body.classList.add('dark-mode');
            darkModeToggle.innerHTML = '<i class="bi bi-sun"></i>';
            darkModeToggle.setAttribute('aria-label', 'Switch to light mode');
        }

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });
</script>
</body>
</html>
