<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.chat_users.title') }}</title>

    <!-- External Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap{{ app()->getLocale() === 'ar' ? '.rtl' : '' }}.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&Tajawal:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    @vite(['resources/js/app.js'])

    <style>
        :root {
            --primary-font: 'Poppins', 'Tajawal', sans-serif;
            --shadow-sm: 0 4px 8px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 5px 10px rgba(0, 0, 0, 0.1);
            --spacing-unit: 1rem;
        }

        body {
            font-family: var(--primary-font);
            background: linear-gradient(135deg, #f8f9fa, #e3eaf4);
            transition: background-color 0.3s, color 0.3s;
        }

        [dir="rtl"] {
            direction: rtl;
        }

        [dir="ltr"] {
            direction: ltr;
        }

        .container {
            max-width: 700px;
            margin: 5rem auto;
        }

        .user-list {
            background: white;
            padding: var(--spacing-unit);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
        }

        .list-group-item {
            transition: all 0.3s ease;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        [dir="rtl"] .list-group-item,
        [dir="rtl"] .card-body {
            flex-direction: row-reverse;
        }

        .list-group-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            background: #eef3f7;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-inline-end: 8px;
        }

        .online { background-color: #28a745; }
        .offline { background-color: #6c757d; }

        .chat-button {
            opacity: 0;
            transition: opacity 0.2s;
            margin-inline-start: var(--spacing-unit);
        }

        .list-group-item:hover .chat-button {
            opacity: 1;
        }

        .logout-button {
            position: absolute;
            top: 20px;
            inset-inline-end: 20px;
        }

        [dir="rtl"] .logout-button {
            inset-inline-end: auto;
            inset-inline-start: 20px;
        }

        .dark-mode-toggle {
            position: absolute;
            top: 20px;
            inset-inline-start: 20px;
        }

        [dir="rtl"] .dark-mode-toggle {
            inset-inline-start: auto;
            inset-inline-end: 20px;
        }

        body.dark-mode {
            background: #212529;
            color: #f8f9fa;
        }

        body.dark-mode .user-list {
            background: #343a40;
        }

        body.dark-mode .list-group-item {
            background: #495057;
            color: #f8f9fa;
        }

        body.dark-mode .list-group-item:hover {
            background: #6c757d;
        }

        /* Bidirectional spacing */
        .me-3 {
            margin-inline-end: var(--spacing-unit) !important;
        }

        .ms-3 {
            margin-inline-start: var(--spacing-unit) !important;
        }

        .text-start {
            text-align: start !important;
        }

        [dir="rtl"] .text-muted {
            text-align: start !important;
        }
    </style>
</head>
<body>
<div class="container position-relative">
    <!-- Dark Mode Toggle -->
    <button class="btn btn-outline-secondary dark-mode-toggle" aria-label="{{ __('messages.chat_users.dark_mode') }}">🌙</button>

    <!-- Header -->
    <header class="text-center mb-4">
        <h1 class="display-5 fw-bold">{{ __('messages.chat_users.title') }}</h1>
        <p class="text-muted">{{ __('messages.chat_users.subtitle') }}</p>
    </header>

    <!-- Logout Form -->
    <form class="logout-button" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-danger">
            <i class="bi bi-box-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i> {{ __('messages.auth.logout') }}
        </button>
    </form>

    <!-- User List Section -->
    <section class="user-list">
        <!-- Search Input -->
        <div class="input-group mb-3">
            <input
                type="text"
                class="form-control"
                placeholder="{{ __('messages.chat_users.search_placeholder') }}"
                id="user-search"
                aria-label="{{ __('messages.chat_users.search_placeholder') }}"
            >
            <button class="btn btn-outline-secondary" type="button" aria-label="{{ __('messages.chat_users.search') }}">
                <i class="bi bi-search"></i>
            </button>
        </div>

        <!-- Current User Profile -->
        <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img
                        src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=64&background=random&bold=true"
                        alt="{{ __('messages.chat_users.my_profile_picture') }}"
                        class="rounded-circle me-3"
                        width="50"
                        height="50"
                        loading="lazy"
                    >
                    <div>
                        <h5 class="mb-0">{{ auth()->user()->name }}</h5>
                        <span class="text-muted">{{ __('messages.chat_users.you') }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="status-dot online"></span>
                    <span class="badge bg-success">{{ __('messages.status.online') }}</span>
                    <a
                        href="{{ route('profile') }}"
                        class="btn btn-primary btn-sm ms-3 profile-button"
                        style="display: none;"
                    >
                        {{ __('messages.chat_users.profile') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <ul class="list-group">
            @forelse ($users as $user)
                <li class="list-group-item" data-user-id="{{ $user->id }}">
                    <div class="d-flex align-items-center">
                        <img
                            src="{{ $user->profile_picture_url ?? "https://ui-avatars.com/api/?name=".urlencode($user->name)."&size=64&background=random&bold=true" }}"
                            alt="{{ sprintf(__('messages.chat_users.user_profile_picture'), $user->name) }}"
                            class="rounded-circle me-3"
                            width="50"
                            height="50"
                            loading="lazy"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=64&background=gray&color=white&bold=true'"
                        >
                        <a href="{{ route('chat', $user->id) }}" class="text-decoration-none text-dark fw-semibold">
                            {{ $user->name }}
                        </a>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="status-dot {{ $user->isOnline() ? 'online' : 'offline' }}"></span>
                        <span class="badge {{ $user->isOnline() ? 'bg-success' : 'bg-secondary' }}">
                                {{ $user->isOnline() ? __('messages.status.online') : __('messages.status.offline') }}
                            </span>
                        <a
                            href="{{ route('chat', $user->id) }}"
                            class="btn btn-primary btn-sm chat-button"
                        >
                            {{ __('messages.chat_users.chat') }}
                        </a>
                    </div>
                </li>
            @empty
                <li class="list-group-item text-center text-muted">
                    {{ __('messages.chat_users.no_users') }}
                </li>
            @endforelse
        </ul>
    </section>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Localization variables
        const translations = {
            online: '{{ __('messages.status.online') }}',
            offline: '{{ __('messages.status.offline') }}'
        };

        // Search functionality
        const searchInput = document.getElementById('user-search');
        const userListItems = document.querySelectorAll('.list-group-item');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();
            userListItems.forEach(item => {
                const userName = item.querySelector('a')?.textContent.toLowerCase() || '';
                item.style.display = userName.includes(searchTerm) ? 'flex' : 'none';
            });
        });

        // Profile button hover
        const profileCard = document.querySelector('.card');
        const profileButton = document.querySelector('.profile-button');
        if (profileCard && profileButton) {
            profileCard.addEventListener('mouseenter', () => profileButton.style.display = 'inline-block');
            profileCard.addEventListener('mouseleave', () => profileButton.style.display = 'none');
        }

        // Real-time status updates
        window.Echo.channel('user-status')
            .listen('UserOnline', (e) => {
                const userElement = document.querySelector(`[data-user-id="${e.user.id}"]`);
                if (userElement) {
                    userElement.querySelector('.status-dot').classList.replace('offline', 'online');
                    userElement.querySelector('.badge').className = 'badge bg-success';
                    userElement.querySelector('.badge').textContent = translations.online;
                }
            })
            .listen('UserOffline', (e) => {
                const userElement = document.querySelector(`[data-user-id="${e.user.id}"]`);
                if (userElement) {
                    userElement.querySelector('.status-dot').classList.replace('online', 'offline');
                    userElement.querySelector('.badge').className = 'badge bg-secondary';
                    userElement.querySelector('.badge').textContent = translations.offline;
                }
            });

        // Dark mode toggle
        const darkModeToggle = document.querySelector('.dark-mode-toggle');
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('dark-mode', document.body.classList.contains('dark-mode'));
        });

        // Load dark mode preference
        if (localStorage.getItem('dark-mode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    });
</script>
</body>
</html>
