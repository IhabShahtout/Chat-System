<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with {{ $receiver->name }}</title>
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
            padding: 20px;
        }
        .chat-container {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }
        .chat-header {
            background: #f1f3f5;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .back-button {
            color: #333;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
        }

        .back-button i {
            font-size: 1.2rem;
        }

        .back-button:hover {
            color: #28a745;
        }

        .back-button span {
            font-size: 0.95rem;
        }

        .chat-header-content {
            display: flex;
            align-items: center;
        }
        .chat-header-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }
        .chat-header-info h4 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }
        .status-indicator {
            display: flex;
            align-items: center;
        }
        .status-circle {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .online { background-color: #28a745; }
        .offline { background-color: #dc3545; }
        .status-text {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .chat-box {
            height: 500px;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .message {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            opacity: 0;
            animation: fadeIn 0.3s ease forwards;
        }
        @keyframes fadeIn {
            to { opacity: 1; }
        }
        .message.sent { align-items: flex-end; }
        .message.received { align-items: flex-start; }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-bottom: 5px;
        }
        .bubble {
            max-width: 70%;
            padding: 12px 15px;
            border-radius: 20px;
            word-break: break-word;
            font-size: 1rem;
            position: relative;
        }
        .message.sent .bubble {
            background: #28a745;
            color: white;
            border-radius: 20px 20px 20px 5px;
        }
        .message.received .bubble {
            background: #e9ecef;
            color: #333;
            border-radius: 20px 20px 5px 20px;
        }
        .timestamp {
            font-size: 0.8rem;
            color: #888;
            margin-top: 4px;
        }
        .typing-indicator {
            font-size: 0.9rem;
            color: #6c757d;
            padding: 10px 20px;
            display: none;
        }
        .input-area {
            display: flex;
            padding: 15px;
            border-top: 1px solid #e9ecef;
            background: #fff;
        }
        #message-input {
            flex: 1;
            border: 1px solid #ced4da;
            border-radius: 25px;
            padding: 10px 15px;
            margin-right: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        #message-input:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            outline: none;
        }
        .send-button {
            background: #28a745;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            color: white;
            transition: background-color 0.3s ease;
        }
        .send-button:hover {
            background: #218838;
        }
        .emoji-button {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6c757d;
            margin-right: 10px;
            transition: color 0.3s ease;
        }
        .emoji-button:hover {
            color: #28a745;
        }

        .emoji-panel {
            position: absolute;
            bottom: 70px;
            right: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 10px;
            display: none;
            z-index: 10;
            width: 300px;
            max-height: 200px;
            overflow-y: auto;
        }

        .emoji-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        .emoji {
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .emoji:hover {
            background: #f1f3f5;
            border-radius: 50%;
        }


        .dark-mode-toggle {
            position: absolute;
            top: 20px;
            left: 20px;
            cursor: pointer;
        }
        body.dark-mode {
            background: #212529;
        }
        body.dark-mode .chat-container {
            background: #343a40;
            color: #f8f9fa;
        }
        body.dark-mode .chat-header {
            background: #495057;
            border-bottom: 1px solid #6c757d;
        }
        body.dark-mode .chat-box {
            background: #212529;
        }
        body.dark-mode .message.received .bubble {
            background: #495057;
            color: #f8f9fa;
        }
        body.dark-mode .input-area {
            background: #343a40;
            border-top: 1px solid #6c757d;
        }
        body.dark-mode #message-input {
            background: #495057;
            border-color: #6c757d;
            color: #f8f9fa;
        }
    </style>

</head>
<body>
<!-- Dark Mode Toggle -->
<button class="btn btn-outline-secondary dark-mode-toggle"><i class="bi bi-moon-stars"></i></button>

<div class="chat-container">
    <div class="chat-header">
        <a href="{{ route('users') }}" class="back-button d-flex align-items-center">
            <i class="bi bi-arrow-left me-2"></i>
            <span>Back to Users</span>
        </a>
        <div class="chat-header-content">
            <img src="{{ $receiver->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($receiver->name).'&size=64' }}"
                 alt="{{ $receiver->name }}'s Avatar" class="chat-header-avatar">
            <div class="chat-header-info">
                <h4>{{ $receiver->name }}</h4>
                <div class="status-indicator">
                    <span class="status-circle {{ $receiver->isOnline() ? 'online' : 'offline' }}"></span>
                    <span class="status-text">{{ $receiver->isOnline() ? 'Online' : 'Offline' }}</span>
                </div>
            </div>
        </div>
        <p class="text-muted small m-0">
            Last active: {{ $lastMessage ? $lastMessage->created_at->format('h:i A') : 'Never' }}
        </p>
    </div>

    <div id="chat-box" class="chat-box">
        @foreach ($messages as $message)
            <div class="message {{ $message->sender_id == auth()->id() ? 'sent' : 'received' }}">
                <img src="{{ $message->sender_id == auth()->id() ? auth()->user()->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=64' : $receiver->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($receiver->name).'&size=64' }}"
                     alt="Avatar" class="avatar">
                <div class="bubble">{{ $message->message }}</div>
                <div class="timestamp">{{ $message->created_at->format('h:i A') }}</div>
            </div>
        @endforeach
    </div>

    <div id="typing-indicator" class="typing-indicator">
        <i class="bi bi-three-dots me-2"></i>{{ $receiver->name }} is typing...
    </div>

    <div class="input-area">
        <form id="message-form" class="d-flex w-100">
            @csrf
            <input type="text" id="message-input" placeholder="Type a message..." class="form-control">
            <button type="button" class="emoji-button" id="emoji-toggle"><i class="bi bi-emoji-smile"></i></button>
            <button type="submit" class="send-button"><i class="bi bi-send me-2"></i>Send</button>
        </form>
        <div class="emoji-panel" id="emoji-panel">
            <div class="emoji-grid">
                <!-- Smilies & People -->
                <div class="emoji" data-emoji="😀">😀</div>
                <div class="emoji" data-emoji="😁">😁</div>
                <div class="emoji" data-emoji="😂">😂</div>
                <div class="emoji" data-emoji="🤣">🤣</div>
                <div class="emoji" data-emoji="😃">😃</div>
                <div class="emoji" data-emoji="😄">😄</div>
                <div class="emoji" data-emoji="😅">😅</div>
                <div class="emoji" data-emoji="😆">😆</div>
                <div class="emoji" data-emoji="😉">😉</div>
                <div class="emoji" data-emoji="😊">😊</div>
                <div class="emoji" data-emoji="😋">😋</div>
                <div class="emoji" data-emoji="😎">😎</div>
                <div class="emoji" data-emoji="😍">😍</div>
                <div class="emoji" data-emoji="😘">😘</div>
                <div class="emoji" data-emoji="🥰">🥰</div>
                <div class="emoji" data-emoji="😗">😗</div>
                <div class="emoji" data-emoji="😙">😙</div>
                <div class="emoji" data-emoji="😚">😚</div>
                <div class="emoji" data-emoji="🙂">🙂</div>
                <div class="emoji" data-emoji="🤗">🤗</div>
                <div class="emoji" data-emoji="🤩">🤩</div>
                <div class="emoji" data-emoji="🤔">🤔</div>
                <div class="emoji" data-emoji="🤨">🤨</div>
                <div class="emoji" data-emoji="😐">😐</div>
                <div class="emoji" data-emoji="😑">😑</div>
                <div class="emoji" data-emoji="😶">😶</div>
                <div class="emoji" data-emoji="🙄">🙄</div>
                <div class="emoji" data-emoji="😏">😏</div>
                <div class="emoji" data-emoji="😣">😣</div>
                <div class="emoji" data-emoji="😥">😥</div>
                <div class="emoji" data-emoji="😮">😮</div>
                <div class="emoji" data-emoji="🤐">🤐</div>
                <div class="emoji" data-emoji="😯">😯</div>
                <div class="emoji" data-emoji="😪">😪</div>
                <div class="emoji" data-emoji="😫">😫</div>
                <div class="emoji" data-emoji="🥱">🥱</div>
                <div class="emoji" data-emoji="😴">😴</div>
                <div class="emoji" data-emoji="😌">😌</div>
                <div class="emoji" data-emoji="😛">😛</div>
                <div class="emoji" data-emoji="😜">😜</div>
                <div class="emoji" data-emoji="😝">😝</div>
                <div class="emoji" data-emoji="🤤">🤤</div>

                <!-- Animals & Nature -->
                <div class="emoji" data-emoji="🐶">🐶</div>
                <div class="emoji" data-emoji="🐱">🐱</div>
                <div class="emoji" data-emoji="🐭">🐭</div>
                <div class="emoji" data-emoji="🐹">🐹</div>
                <div class="emoji" data-emoji="🐰">🐰</div>
                <div class="emoji" data-emoji="🦊">🦊</div>
                <div class="emoji" data-emoji="🐻">🐻</div>
                <div class="emoji" data-emoji="🐼">🐼</div>
                <div class="emoji" data-emoji="🐨">🐨</div>
                <div class="emoji" data-emoji="🐯">🐯</div>
                <div class="emoji" data-emoji="🦁">🦁</div>
                <div class="emoji" data-emoji="🐮">🐮</div>
                <div class="emoji" data-emoji="🐷">🐷</div>
                <div class="emoji" data-emoji="🐸">🐸</div>
                <div class="emoji" data-emoji="🐵">🐵</div>
                <div class="emoji" data-emoji="🐒">🐒</div>
                <div class="emoji" data-emoji="🦍">🦍</div>
                <div class="emoji" data-emoji="🦧">🦧</div>
                <div class="emoji" data-emoji="🐘">🐘</div>
                <div class="emoji" data-emoji="🦛">🦛</div>
                <div class="emoji" data-emoji="🦘">🦘</div>
                <div class="emoji" data-emoji="🦒">🦒</div>
                <div class="emoji" data-emoji="🦓">🦓</div>
                <div class="emoji" data-emoji="🐴">🐴</div>
                <div class="emoji" data-emoji="🦄">🦄</div>
                <div class="emoji" data-emoji="🦓">🦓</div>
                <div class="emoji" data-emoji="🦌">🦌</div>
                <div class="emoji" data-emoji="🦬">🦬</div>
                <div class="emoji" data-emoji="🦏">🦏</div>
                <div class="emoji" data-emoji="🦛">🦛</div>

                <!-- Food & Drink -->
                <div class="emoji" data-emoji="🍕">🍕</div>
                <div class="emoji" data-emoji="🍔">🍔</div>
                <div class="emoji" data-emoji="🍟">🍟</div>
                <div class="emoji" data-emoji="🌭">🌭</div>
                <div class="emoji" data-emoji="🌮">🌮</div>
                <div class="emoji" data-emoji="🌯">🌯</div>
                <div class="emoji" data-emoji="🌮">🌮</div>
                <div class="emoji" data-emoji="🥗">🥗</div>
                <div class="emoji" data-emoji="🥘">🥘</div>
                <div class="emoji" data-emoji="🍲">🍲</div>
                <div class="emoji" data-emoji="🍱">🍱</div>
                <div class="emoji" data-emoji="🍘">🍘</div>
                <div class="emoji" data-emoji="🍙">🍙</div>
                <div class="emoji" data-emoji="🍚">🍚</div>
                <div class="emoji" data-emoji="🍛">🍛</div>
                <div class="emoji" data-emoji="🍜">🍜</div>
                <div class="emoji" data-emoji="🍝">🍝</div>
                <div class="emoji" data-emoji="🍝">🍝</div>
                <div class="emoji" data-emoji="🍠">🍠</div>
                <div class="emoji" data-emoji="🍢">🍢</div>
                <div class="emoji" data-emoji="🍣">🍣</div>
                <div class="emoji" data-emoji="🍤">🍤</div>
                <div class="emoji" data-emoji="🍥">🍥</div>
                <div class="emoji" data-emoji="🥮">🥮</div>
                <div class="emoji" data-emoji="🍡">🍡</div>
                <div class="emoji" data-emoji="🥟">🥟</div>
                <div class="emoji" data-emoji="🥠">🥠</div>
                <div class="emoji" data-emoji="🥡">🥡</div>

                <!-- Activities & Sports -->
                <div class="emoji" data-emoji="⚽">⚽</div>
                <div class="emoji" data-emoji="🏀">🏀</div>
                <div class="emoji" data-emoji="🏈">🏈</div>
                <div class="emoji" data-emoji="⚾">⚾</div>
                <div class="emoji" data-emoji="🥎">🥎</div>
                <div class="emoji" data-emoji="🎾">🎾</div>
                <div class="emoji" data-emoji="🏐">🏐</div>
                <div class="emoji" data-emoji="🏉">🏉</div>
                <div class="emoji" data-emoji="🎱">🎱</div>
                <div class="emoji" data-emoji="🎳">🎳</div>
                <div class="emoji" data-emoji="🏏">🏏
                </div>
                <div class="emoji" data-emoji="🏑">🏑</div>
                <div class="emoji" data-emoji="🏒">🏒</div>
                <div class="emoji" data-emoji="🥍">🥍</div>
                <div class="emoji" data-emoji="🏓">🏓</div>
                <div class="emoji" data-emoji="🏸">🏸</div>
                <div class="emoji" data-emoji="🥊">🥊</div>
                <div class="emoji" data-emoji="🥋">🥋</div>
                <div class="emoji" data-emoji="⛳">⛳</div>
                <div class="emoji" data-emoji="⛸️">⛸️</div>
                <div class="emoji" data-emoji="🎣">🎣</div>
                <div class="emoji" data-emoji="🤿">🤿</div>
                <div class="emoji" data-emoji="🎽">🎽</div>
                <div class="emoji" data-emoji="🎿">🎿</div>
                <div class="emoji" data-emoji="🛷">🛷</div>
                <div class="emoji" data-emoji="⛸️">⛸️</div>
                <div class="emoji" data-emoji="🎣">🎣</div>

                <!-- Objects -->
                <div class="emoji" data-emoji="⌚">⌚</div>
                <div class="emoji" data-emoji="📱">📱</div>
                <div class="emoji" data-emoji="📲">📲</div>
                <div class="emoji" data-emoji="☎️">☎️</div>
                <div class="emoji" data-emoji="📞">📞</div>
                <div class="emoji" data-emoji="📟">📟</div>
                <div class="emoji" data-emoji="📠">📠</div>
                <div class="emoji" data-emoji="💻">💻</div>
                <div class="emoji" data-emoji="🖥️">🖥️</div>
                <div class="emoji" data-emoji="🖨️">🖨️</div>
                <div class="emoji" data-emoji="⌨️">⌨️</div>
                <div class="emoji" data-emoji="🖱️">🖱️</div>
                <div class="emoji" data-emoji="🖲️">🖲️</div>
                <div class="emoji" data-emoji="💽">💽</div>
                <div class="emoji" data-emoji="💾">💾</div>
                <div class="emoji" data-emoji="💿">💿</div>
                <div class="emoji" data-emoji="📀">📀</div>
                <div class="emoji" data-emoji="🎥">🎥</div>
                <div class="emoji" data-emoji="🎞️">🎞️</div>
                <div class="emoji" data-emoji="📺">📺</div>
                <div class="emoji" data-emoji="📷">📷</div>
                <div class="emoji" data-emoji="📸">📸</div>
                <div class="emoji" data-emoji="📹">📹</div>
                <div class="emoji" data-emoji="📼">📼</div>
                <div class="emoji" data-emoji="📻">📻</div>
                <div class="emoji" data-emoji="📼">📼</div>

                <!-- Symbols -->
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="🧡">🧡</div>
                <div class="emoji" data-emoji="💛">💛</div>
                <div class="emoji" data-emoji="💚">💚</div>
                <div class="emoji" data-emoji="💙">💙</div>
                <div class="emoji" data-emoji="💜">💜</div>
                <div class="emoji" data-emoji="🖤">🖤</div>
                <div class="emoji" data-emoji="🤎">🤎</div>
                <div class="emoji" data-emoji="🤍">🤍</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
                <div class="emoji" data-emoji="❤️">❤️</div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatBox = document.getElementById('chat-box');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const typingIndicator = document.getElementById('typing-indicator');
        const receiverId = {{ $receiver->id }};
        const senderId = {{ auth()->id() }};
        const emojiToggleButton = document.getElementById('emoji-toggle');
        const emojiPanel = document.getElementById('emoji-panel');
        const emojis = document.querySelectorAll('.emoji');
        const darkModeToggle = document.querySelector('.dark-mode-toggle');


        // Audio notification
        let audioContext;
        let soundBuffer; // Declare soundBuffer in the outer scope

        // Initialize audio context and load sound
        function initAudio() {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();

            // Load sound file
            fetch('{{ asset('assets/audio/massage-alert1.mp3') }}')
                .then(response => response.arrayBuffer())
                .then(arrayBuffer => audioContext.decodeAudioData(arrayBuffer))
                .then(decodedData => {
                    soundBuffer = decodedData; // Assign the decoded data to soundBuffer
                    console.log('Audio loaded successfully');
                })
                .catch(error => {
                    console.error('Error loading sound file:', error);
                });
        }

        // Play notification sound
        function playNotificationSound() {
            if (!audioContext) {
                initAudio(); // Initialize audio context if not already done
                return; // Exit since the sound isn't loaded yet
            }
            if (soundBuffer) { // Check if soundBuffer is ready
                const source = audioContext.createBufferSource();
                source.buffer = soundBuffer;
                source.connect(audioContext.destination);
                source.start(0);
            } else {
                console.log('Sound buffer not yet loaded');
            }
        }

        // Call initAudio on page load to preload the sound
        initAudio();


        // Auto-scroll to bottom
        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        scrollToBottom();

        // Dark Mode Toggle
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('dark-mode', document.body.classList.contains('dark-mode'));
        });
        if (localStorage.getItem('dark-mode') === 'true') {
            document.body.classList.add('dark-mode');
        }

        // Emoji Panel Toggle
        emojiToggleButton.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent the click from propagating to the document
            console.log('Emoji toggle clicked');
            if (emojiPanel.style.display === 'block') {
                emojiPanel.style.display = 'none';
            } else {
                emojiPanel.style.display = 'block';
            }
        });

        document.addEventListener('click', (event) => {
            console.log('Document clicked');
            if (!emojiPanel.contains(event.target) && event.target !== emojiToggleButton) {
                console.log('Hiding emoji panel');
                emojiPanel.style.display = 'none';
            }
        });

        emojis.forEach(emoji => {
            emoji.addEventListener('click', () => {
                console.log('Emoji clicked:', emoji.getAttribute('data-emoji'));
                messageInput.value += emoji.getAttribute('data-emoji');
                messageInput.focus();
            });
        });

        // Listen for new messages
        window.Echo.private('chat.' + senderId).listen('MessageSent', (e) => {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message received';
            messageDiv.innerHTML = `
                    <img src="{{ $receiver->profile_picture_url ?? 'https://ui-avatars.com/api/?size=64' }}"
                         alt="{{ $receiver->name }}'s Avatar" class="avatar">
                    <div class="bubble">${e.message.message}</div>
                    <div class="timestamp">${new Date(e.message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                `;
            chatBox.appendChild(messageDiv);
            scrollToBottom();

            // Play notification sound
            playNotificationSound();
        });

        // Send message
        messageForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (message) {
                fetch(`/chat/${receiverId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message })
                });

                const messageDiv = document.createElement('div');
                messageDiv.className = 'message sent';
                messageDiv.innerHTML = `
                        <img src="{{ auth()->user()->profile_picture_url ?? 'https://ui-avatars.com/api/?size=64' }}"
                             alt="Your Avatar" class="avatar">
                        <div class="bubble">${message}</div>
                        <div class="timestamp">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                    `;
                chatBox.appendChild(messageDiv);
                scrollToBottom();
                messageInput.value = '';
                emojiPanel.style.display = 'none';
            }
        });

        // Typing indicator
        window.Echo.private('typing.' + receiverId).listen('UserTyping', (e) => {
            if (e.typerId === receiverId) {
                typingIndicator.style.display = 'block';
                setTimeout(() => typingIndicator.style.display = 'none', 3000);
            }
        });

        messageInput.addEventListener('input', function () {
            fetch(`/chat/typing`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
        });


        fetch('/user/online', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // Mark user as offline when window closes
        window.addEventListener('beforeunload', function () {
            fetch('/user/offline', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        });
    });
</script>
</body>
</html>
