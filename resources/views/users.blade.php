<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    @vite(['resources/js/app.js'])
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9fa, #e3eaf4);
        }
        .container {
            max-width: 700px;
            margin: auto;
        }
        .user-list {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .list-group-item {
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        .list-group-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            background: #eef3f7;
        }
        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .online {
            background-color: #28a745;
        }
        .offline {
            background-color: #6c757d;
        }
        .chat-button {
            display: none;
            transition: opacity 0.2s;
        }
        .list-group-item:hover .chat-button {
            display: inline-block;
        }
        .logout-button {
            position: absolute;
            top: 20px;
            right: 20px;
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
        body.dark-mode .user-list {
            background: #343a40;
            color: #f8f9fa;
        }
        body.dark-mode .list-group-item {
            background: #495057;
            color: #f8f9fa;
        }
        body.dark-mode .list-group-item:hover {
            background: #6c757d;
        }
    </style>
</head>
<body>
<div class="container mt-5 position-relative">
    <!-- Dark Mode Toggle -->
    <button class="btn btn-outline-secondary dark-mode-toggle">🌙</button>

    <div class="text-center mb-4">
        <h1 class="display-5 fw-bold">Chat Users</h1>
        <p class="text-muted">Connect with your friends and colleagues in real-time.</p>
    </div>

    <!-- Logout Button -->
    <form class="logout-button" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>

    <div class="user-list">
        <!-- Search Bar -->
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Search users..." id="user-search">
            <button class="btn btn-outline-secondary" type="button">
                <i class="bi bi-search"></i>
            </button>
        </div>

        <!-- Your Profile -->
        <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img
                        src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=64&background=random&bold=true"
                        alt="My Profile Picture"
                        class="rounded-circle me-3"
                        width="50"
                        height="50">
                    <div>
                        <h5 class="mb-0">{{ auth()->user()->name }}</h5>
                        <span class="text-muted">You</span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="status-dot online"></span>
                    <span class="badge bg-success">Online</span>
                    <a href="{{ route('profile') }}" class="btn btn-primary btn-sm ms-3 profile-button" style="display: none;">
                        Profile
                    </a>
                </div>
            </div>
        </div>
        <ul class="list-group">
            @foreach ($users as $user)
                <li class="list-group-item d-flex justify-content-between align-items-center" data-user-id="{{ $user->id }}">
                    <div class="d-flex align-items-center">
                        <img
                            src="{{ $user->profile_picture_url ?? "https://ui-avatars.com/api/?name=".urlencode($user->name)."&size=64&background=random&bold=true" }}"
                            alt="{{ $user->name }}'s Profile Picture"
                            class="rounded-circle me-3"
                            width="50"
                            height="50"
                            loading="lazy"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=64&background=gray&color=white&bold=true'">
                        <a href="{{ route('chat', $user->id) }}" class="text-decoration-none text-dark fw-semibold">
                            {{ $user->name }}
                        </a>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="status-dot {{ $user->isOnline() ? 'online' : 'offline' }}"></span>
                        <span class="badge {{ $user->isOnline() ? 'bg-success' : 'bg-secondary' }}">
                            {{ $user->isOnline() ? 'Online' : 'Offline' }}
                        </span>
                        <a href="{{ route('chat', $user->id) }}" class="btn btn-primary btn-sm ms-3 chat-button">
                            Chat
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('user-search');
        const userListItems = document.querySelectorAll('.list-group-item');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            userListItems.forEach(item => {
                const userName = item.querySelector('a').textContent.toLowerCase();
                item.style.display = userName.includes(searchTerm) ? 'flex' : 'none';
            });
        });

        // Add hover effect for profile button
        const profileCard = document.querySelector('.card.mb-3');
        const profileButton = document.querySelector('.profile-button');

        if (profileCard && profileButton) {
            profileCard.addEventListener('mouseenter', () => {
                profileButton.style.display = 'inline-block';
            });

            profileCard.addEventListener('mouseleave', () => {
                profileButton.style.display = 'none';
            });
        }

        // Add your profile to the list (optional)
        {{--const usersList = document.querySelector('.list-group');--}}
        {{--const myProfile = `--}}
        {{--<li class="list-group-item d-flex justify-content-between align-items-center">--}}
        {{--    <div class="d-flex align-items-center">--}}
        {{--        <img--}}
        {{--            src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=64&background=random&bold=true"--}}
        {{--            alt="My Profile Picture"--}}
        {{--            class="rounded-circle me-3"--}}
        {{--            width="50"--}}
        {{--            height="50">--}}
        {{--        <a href="#" class="text-decoration-none text-dark fw-semibold">--}}
        {{--            {{ auth()->user()->name }}--}}
        {{--        </a>--}}
        {{--     </div>--}}
        {{--    <div class="d-flex align-items-center">--}}
        {{--        <span class="status-dot online"></span>--}}
        {{--        <span class="badge bg-success">Online</span>--}}
        {{--    </div>--}}
        {{--</li>`;--}}
        {{--usersList.insertAdjacentHTML('afterbegin', myProfile);--}}
        window.Echo.channel('user-status').listen('UserOnline', (e) => {
            const userElement = document.querySelector(`[data-user-id="${e.user.id}"]`);
            if (userElement) {
                userElement.querySelector('.status-dot').classList.replace('offline', 'online');
                userElement.querySelector('.badge').className = 'badge bg-success';
                userElement.querySelector('.badge').textContent = 'Online';
            }
        });

        window.Echo.channel('user-status').listen('UserOffline', (e) => {
            const userElement = document.querySelector(`[data-user-id="${e.user.id}"]`);
            if (userElement) {
                userElement.querySelector('.status-dot').classList.replace('online', 'offline');
                userElement.querySelector('.badge').className = 'badge bg-secondary';
                userElement.querySelector('.badge').textContent = 'Offline';
            }
        });

        // Dark Mode Toggle
        const darkModeToggle = document.querySelector('.dark-mode-toggle');
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('dark-mode', document.body.classList.contains('dark-mode'));
        });

        // Load saved dark mode state
        if (localStorage.getItem('dark-mode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    });
</script>
</body>
</html>
