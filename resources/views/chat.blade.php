<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.chat_users.title') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

</head>
<body>
<div class="chat-layout">
    <aside class="chat-sidebar">
        <header class="chat-header">
            <div class="chat-title-container">
                <button class="menu-toggle" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
                <i class="bi bi-messenger chat-icon" aria-hidden="true"></i>
                <h1 class="chat-title">{{ __('messages.chat_users.title') }}</h1>
            </div>
            <nav class="chat-actions">
                <button class="action-btn toggle-theme" aria-label="Toggle dark mode"><span>🌙</span></button>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="action-btn logout-btn" aria-label="Log out">
                        <i class="bi bi-box-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                    </button>
                </form>
            </nav>
        </header>
        <section class="search-section">
            <div class="search-container">
                <i class="bi bi-search search-icon" aria-hidden="true"></i>
                <input type="text" class="search-input" id="user-search" placeholder="{{ __('messages.chat_users.search_placeholder') }}" aria-label="Search users">
                <button class="clear-btn" type="button" aria-label="Clear search"><i class="bi bi-x"></i></button>
            </div>
        </section>
        <section class="user-list">
            @forelse ($users as $user)
                <article class="user-item" data-user-id="{{ $user->id }}">
                    <div class="user-profile">
                        <img src="{{ $user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=64&background=random' }}"
                             alt="{{ sprintf(__('messages.chat_users.user_profile_picture'), $user->name) }}"
                             class="avatar">
                        <span class="user-name">{{ $user->name }}</span>
                    </div>
                    <div class="status-group">
                        <span class="status-dot {{ $user->isOnline() ? 'online' : 'offline' }}"></span>
                        <span class="status-text">{{ $user->isOnline() ? __('messages.status.online') : __('messages.status.offline') }}</span>
                    </div>
                </article>
            @empty
                <article class="user-item"><span class="status-text">{{ __('messages.chat_users.no_users') }}</span></article>
            @endforelse
        </section>
    </aside>
    <main class="chat-main">
        <button class="menu-toggle mobile-toggle" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
        <div class="status-bar" id="status-bar">
            <span id="connection-status">Connecting...</span>
            <span id="last-seen"></span>
        </div>
        <div id="chat-header" class="chat-header" style="display: none;">
            <div class="chat-header-content">
                <img id="receiver-avatar" class="avatar" alt="Receiver Avatar">
                <div class="chat-header-info">
                    <h4 id="receiver-name"></h4>
                    <div class="status-indicator">
                        <span id="receiver-status-dot" class="status-dot"></span>
                        <span id="receiver-status-text" class="status-text"></span>
                    </div>
                </div>
            </div>
        </div>
        <div id="chat-box" class="chat-box">
            <p style="color: var(--text-muted); text-align: center;">Select a user to start chatting</p>
            <div id="typing-indicator" class="typing-indicator">
                <span id="typing-user"></span> is typing
                <span class="dots">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </span>
            </div>
        </div>
        <div id="input-area" class="input-area" style="display: none;">
            <div class="input-wrapper">
                <form id="message-form" class="message-form" autocomplete="off">
                    @csrf
                    <div class="input-group">
                        <textarea id="message-input" placeholder="{{ __('messages.chat_users.type_message') }}" rows="1" maxlength="2000"></textarea>
                        <div class="action-buttons">
                            <button type="button" class="action-btn emoji-btn" id="emoji-toggle" aria-label="Open emoji picker">
                                <i class="bi bi-emoji-smile"></i>
                            </button>
                            <button type="button" class="action-btn attach-btn" id="attach-file" aria-label="Attach files">
                                <i class="bi bi-paperclip"></i>
                            </button>
                            <button type="button" class="action-btn voice-btn" id="voice-toggle" aria-label="Record voice message">
                                <i class="fas fa-microphone"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="send-btn" id="send-message" aria-label="Send message" disabled>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </form>
            </div>
            <x-emoji-panel />
            <div id="voice-recording-interface" class="voice-interface">
                <div class="controls">
                    <button id="start-recording" class="btn btn-start" aria-label="Start recording"><i class="fas fa-microphone"></i></button>
                    <button id="stop-recording" class="btn btn-stop d-none" aria-label="Stop recording"><i class="fas fa-stop"></i></button>
                    <span id="recording-timer" class="recording-timer">00:00</span>
                    <button id="cancel-recording" class="btn btn-cancel d-none" aria-label="Cancel recording"><i class="fas fa-times"></i></button>
                    <button id="send-recording" class="btn btn-send d-none" aria-label="Send voice message"><i class="fas fa-paper-plane"></i></button>
                </div>
                <div class="waveform"></div>
                <audio id="voice-preview" class="preview-audio" controls></audio>
                <textarea id="voice-text" class="voice-text-input" placeholder="Add an optional message..." rows="2"></textarea>
            </div>
            <div id="attachment-preview" class="attachment-preview">
                <div class="preview-header">
                    <h4>Selected Files</h4>
                    <span id="file-count" class="text-muted"></span>
                </div>
                <div class="preview-content" id="preview-content"></div>
                <div class="preview-actions">
                    <button id="replace-attachment" class="btn btn-replace"><i class="fas fa-sync-alt"></i> Replace</button>
                    <button id="send-attachment" class="btn btn-send"><i class="fas fa-paper-plane"></i> Send</button>
                    <button id="cancel-attachment" class="btn btn-cancel"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </div>
        </div>
    </main>
    <button class="fab" id="scroll-to-bottom" aria-label="Scroll to bottom">
        <i class="bi bi-chevron-down"></i>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('.chat-sidebar');
        const menuToggles = document.querySelectorAll('.menu-toggle');
        const searchInput = document.getElementById('user-search');
        const userItems = document.querySelectorAll('.user-item');
        const darkModeToggle = document.querySelector('.toggle-theme');
        const chatBox = document.getElementById('chat-box');
        const inputArea = document.getElementById('input-area');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-message');
        const emojiToggle = document.getElementById('emoji-toggle');
        const emojiPanel = document.getElementById('emoji-panel');
        const voiceToggle = document.getElementById('voice-toggle');
        const voiceInterface = document.getElementById('voice-recording-interface');
        const attachButton = document.getElementById('attach-file');
        const fileInput = document.getElementById('file-input') || document.createElement('input');
        fileInput.type = 'file';
        fileInput.id = 'file-input';
        fileInput.className = 'd-none';
        fileInput.accept = 'image/*';
        fileInput.multiple = true;
        inputArea.appendChild(fileInput);
        const typingIndicator = document.getElementById('typing-indicator');
        const typingUser = document.getElementById('typing-user');
        const scrollToBottomBtn = document.getElementById('scroll-to-bottom');
        const statusBar = document.getElementById('status-bar');
        const connectionStatus = document.getElementById('connection-status');
        const lastSeen = document.getElementById('last-seen');
        const senderId = {{ auth()->id() }};
        let currentReceiverId = null;
        let mediaRecorder, audioChunks = [];
        let typingTimeout, recordingTimer;
        let selectedFiles = [];

        // Toggle sidebar on mobile
        menuToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                if (sidebar.classList.contains('active')) {
                    toggle.innerHTML = '<i class="bi bi-x"></i>';
                } else {
                    toggle.innerHTML = '<i class="bi bi-list"></i>';
                }
            });
        });

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();
            userItems.forEach(item => {
                const userName = item.querySelector('.user-name').textContent.toLowerCase();
                item.style.display = userName.includes(searchTerm) ? 'flex' : 'none';
            });
            this.parentElement.classList.toggle('has-text', !!this.value.trim());
        });

        // Dark mode toggle
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('dark-mode', document.body.classList.contains('dark-mode'));
            darkModeToggle.querySelector('span').textContent = document.body.classList.contains('dark-mode') ? '☀️' : '🌙';
        });
        if (localStorage.getItem('dark-mode') === 'true') {
            document.body.classList.add('dark-mode');
            darkModeToggle.querySelector('span').textContent = '☀️';
        }

        // Load chat when a user is selected
        userItems.forEach(item => {
            item.addEventListener('click', function() {
                userItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                currentReceiverId = this.dataset.userId;
                loadChat(currentReceiverId);
                if (window.innerWidth <= 768) sidebar.classList.remove('active');
            });
        });

        function loadChat(receiverId) {
            fetch(`/chat/${receiverId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('chat-header').style.display = 'flex';
                    const avatarUrl = data.receiver.profile_picture_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.receiver.name)}&size=64&background=random`;
                    document.getElementById('receiver-avatar').src = avatarUrl;
                    document.getElementById('receiver-name').textContent = data.receiver.name;
                    document.getElementById('receiver-status-dot').classList.toggle('online', data.receiver.is_online);
                    document.getElementById('receiver-status-dot').classList.toggle('offline', !data.receiver.is_online);
                    document.getElementById('receiver-status-text').textContent = data.receiver.is_online ? 'Online' : 'Offline';
                    chatBox.innerHTML = '<p style="color: var(--text-muted); text-align: center;">Select a user to start chatting</p>';
                    chatBox.appendChild(typingIndicator);
                    data.messages.forEach(message => appendMessage(message));
                    inputArea.style.display = 'flex';
                    resetInputArea();
                    updateStatusBar(data.receiver);
                    chatBox.scrollTop = chatBox.scrollHeight;
                    toggleScrollToBottomBtn();
                })
                .catch(err => console.error('Error loading chat:', err));
        }

        function appendMessage(message) {
            const isSent = message.sender_id === senderId;
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
            let content = '';
            if (message.type === 'text') {
                content = `<div class="bubble">${message.message}</div>`;
            } else if (message.type === 'voice') {
                content = `<div class="bubble">`;
                if (message.message) content += `${message.message}<br>`;
                content += `<audio controls><source src="/storage/${message.voice_note}" type="audio/webm"></audio></div>`;
            } else if (message.type === 'attachment') {
                if (message.attachment_type?.startsWith('image/')) {
                    content = `<div class="bubble"><img src="/storage/${message.attachment}" class="file-preview" style="max-width: 200px; border-radius: 8px;"></div>`;
                } else {
                    content = `<div class="bubble file-preview-document"><i class="bi bi-file-earmark"></i><a href="/storage/${message.attachment}" target="_blank">${message.attachment_name}</a></div>`;
                }
            }
            messageDiv.innerHTML = `${content}<div class="timestamp">${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>`;
            chatBox.insertBefore(messageDiv, typingIndicator);
            chatBox.scrollTop = chatBox.scrollHeight;
            toggleScrollToBottomBtn();
        }

        // Send text message
        messageForm.addEventListener('submit', e => {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (message && currentReceiverId) {
                fetch(`/chat/${currentReceiverId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            appendMessage({ sender_id: senderId, message, type: 'text', created_at: new Date() });
                            messageInput.value = '';
                            sendButton.disabled = true;
                            adjustTextareaHeight(messageInput);
                            chatBox.scrollTop = chatBox.scrollHeight;
                        } else {
                            alert('Failed to send message. Please try again.');
                        }
                    })
                    .catch(err => {
                        console.error('Error sending text message:', err);
                        alert('An error occurred while sending the message. Please try again.');
                    });
            }
        });

        // Typing event
        let isTyping = false;
        messageInput.addEventListener('input', function() {
            sendButton.disabled = !this.value.trim();
            adjustTextareaHeight(this);
            if (!isTyping && currentReceiverId) {
                isTyping = true;
                broadcastTyping(true);
            }
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                isTyping = false;
                broadcastTyping(false);
            }, 2000);
        });

        function broadcastTyping(isTyping) {
            fetch(`/chat/${currentReceiverId}/typing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ typing: isTyping })
            });
        }

        // Auto-resize textarea
        function adjustTextareaHeight(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = `${Math.min(textarea.scrollHeight, 120)}px`;
        }

        // Reset input area
        function resetInputArea() {
            messageInput.value = '';
            sendButton.disabled = true;
            adjustTextareaHeight(messageInput);
            resetVoiceInterface();
            resetAttachmentPreview();
            emojiPanel.classList.remove('active');
            voiceInterface.classList.remove('active');
        }

        // Handle Enter key to send message
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                messageForm.dispatchEvent(new Event('submit'));
            }
        });

        // Emoji handling
        emojiToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            emojiPanel.classList.toggle('active');
            voiceInterface.classList.remove('active');
        });

        document.addEventListener('click', (event) => {
            if (!emojiPanel.contains(event.target) && event.target !== emojiToggle) {
                emojiPanel.classList.remove('active');
            }
        });

        document.querySelectorAll('.emoji').forEach(emoji => {
            emoji.addEventListener('click', () => {
                messageInput.value += emoji.dataset.emoji;
                messageInput.focus();
                adjustTextareaHeight(messageInput);
                sendButton.disabled = false;
                emojiPanel.classList.remove('active');
            });
        });

        // Voice recording
        voiceToggle.addEventListener('click', () => {
            if (!voiceInterface.classList.contains('active')) {
                voiceInterface.classList.add('active');
                initVoiceRecording();
                emojiPanel.classList.remove('active');
            } else {
                resetVoiceInterface();
            }
        });

        function initVoiceRecording() {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
                    audioChunks = [];

                    mediaRecorder.ondataavailable = e => audioChunks.push(e.data);

                    mediaRecorder.onstop = () => {
                        voiceInterface.classList.remove('recording');
                        document.getElementById('stop-recording').classList.add('d-none');
                        document.getElementById('start-recording').classList.remove('d-none');
                        document.getElementById('cancel-recording').classList.remove('d-none');
                        document.getElementById('send-recording').classList.remove('d-none');
                        clearInterval(recordingTimer);
                        const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                        const audioUrl = URL.createObjectURL(audioBlob);
                        const preview = document.getElementById('voice-preview');
                        preview.src = audioUrl;
                        preview.style.display = 'block';
                        stream.getTracks().forEach(track => track.stop());
                    };

                    const startButton = document.getElementById('start-recording');
                    const stopButton = document.getElementById('stop-recording');
                    const cancelButton = document.getElementById('cancel-recording');
                    const sendButton = document.getElementById('send-recording');
                    const timerDisplay = document.getElementById('recording-timer');

                    startButton.addEventListener('click', () => {
                        audioChunks = [];
                        mediaRecorder.start();
                        voiceInterface.classList.add('recording');
                        startButton.classList.add('d-none');
                        stopButton.classList.remove('d-none');
                        document.getElementById('voice-preview').style.display = 'none';
                        const startTime = Date.now();
                        recordingTimer = setInterval(() => {
                            const elapsed = Math.floor((Date.now() - startTime) / 1000);
                            const minutes = String(Math.floor(elapsed / 60)).padStart(2, '0');
                            const seconds = String(elapsed % 60).padStart(2, '0');
                            timerDisplay.textContent = `${minutes}:${seconds}`;
                        }, 1000);
                    });

                    stopButton.addEventListener('click', () => mediaRecorder.stop());

                    cancelButton.addEventListener('click', () => resetVoiceInterface());

                    sendButton.addEventListener('click', () => sendVoiceNote());
                })
                .catch(err => {
                    console.error('Error accessing microphone:', err);
                    alert('Could not access your microphone. Please ensure permissions are granted and try again.');
                    resetVoiceInterface();
                });
        }

        function sendVoiceNote() {
            if (audioChunks.length === 0) {
                alert('No audio recorded. Please record a message first.');
                return;
            }

            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const textMessage = document.getElementById('voice-text').value.trim();
            const formData = new FormData();
            formData.append('voice_note', audioBlob, 'voice_note.webm');
            formData.append('receiver_id', currentReceiverId);
            if (textMessage) formData.append('message', textMessage);

            const sendButton = document.getElementById('send-recording');
            sendButton.disabled = true;
            sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch('/chat/voice-note', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        appendMessage({
                            sender_id: senderId,
                            voice_note: data.voice_note_url.split('/storage/')[1],
                            message: textMessage || null,
                            type: 'voice',
                            created_at: new Date()
                        });
                        resetVoiceInterface();
                    } else {
                        alert('Failed to send voice note. Please try again.');
                    }
                })
                .catch(err => {
                    console.error('Error sending voice note:', err);
                    alert('An error occurred while sending the voice note. Please try again.');
                })
                .finally(() => {
                    sendButton.disabled = false;
                    sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
                });
        }

        function resetVoiceInterface() {
            voiceInterface.classList.remove('active', 'recording');
            document.getElementById('start-recording').classList.remove('d-none');
            document.getElementById('stop-recording').classList.add('d-none');
            document.getElementById('cancel-recording').classList.add('d-none');
            document.getElementById('send-recording').classList.add('d-none');
            document.getElementById('recording-timer').textContent = '00:00';
            document.getElementById('voice-preview').style.display = 'none';
            document.getElementById('voice-text').value = '';
            clearInterval(recordingTimer);
            audioChunks = [];
            if (mediaRecorder?.stream) {
                mediaRecorder.stream.getTracks().forEach(track => track.stop());
            }
        }

        // File attachment
        attachButton.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            const files = Array.from(this.files);
            if (files.length === 0 || !currentReceiverId) {
                return;
            }

            const previewContainer = document.getElementById('attachment-preview');
            const previewContent = document.getElementById('preview-content');
            const fileCount = document.getElementById('file-count');
            const sendButton = document.getElementById('send-attachment');
            const cancelButton = document.getElementById('cancel-attachment');
            const replaceButton = document.getElementById('replace-attachment');

            selectedFiles = files; // Assuming selectedFiles is declared in outer scope
            previewContent.innerHTML = '';
            fileCount.textContent = `${selectedFiles.length} file${selectedFiles.length !== 1 ? 's' : ''} selected`;

            selectedFiles.forEach(function(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const item = document.createElement('div');
                    item.className = 'preview-item';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = file.name;
                    const info = document.createElement('div');
                    info.className = 'file-info';
                    info.textContent = file.name;
                    item.appendChild(img);
                    item.appendChild(info);
                    previewContent.appendChild(item);
                };
                reader.readAsDataURL(file);
            });

            previewContainer.style.display = 'flex';
            previewContainer.classList.add('active');
            emojiPanel.classList.remove('active');
            voiceInterface.classList.remove('active');

            sendButton.onclick = function() {
                if (selectedFiles.length === 0) {
                    return;
                }
                const formData = new FormData();
                selectedFiles.forEach(function(file) {
                    formData.append('files[]', file); // Sending as an array
                });
                formData.append('receiver_id', currentReceiverId);

                fetch('/chat/attachment', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            data.attachments.forEach(function(attachment) {
                                appendMessage({
                                    sender_id: senderId,
                                    attachment: attachment.url.split('/storage/')[1],
                                    attachment_type: attachment.type,
                                    attachment_name: attachment.name,
                                    type: 'attachment',
                                    created_at: new Date()
                                });
                            });
                            resetAttachmentPreview();
                        } else {
                            console.error('Upload failed:', data.message); // Log the error
                        }
                    })
                    .catch(function(err) {
                        console.error('Error sending attachment:', err);
                    });
            };

            replaceButton.onclick = function() {
                fileInput.click();
            };

            cancelButton.onclick = function() {
                resetAttachmentPreview();
            };
        });

        function resetAttachmentPreview() {
            const previewContainer = document.getElementById('attachment-preview');
            previewContainer.style.display = 'none';
            previewContainer.classList.remove('active');
            document.getElementById('preview-content').innerHTML = '';
            fileInput.value = '';
            selectedFiles = [];
        }

        // Scroll to bottom button
        scrollToBottomBtn.addEventListener('click', () => {
            chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
        });

        chatBox.addEventListener('scroll', toggleScrollToBottomBtn);

        function toggleScrollToBottomBtn() {
            const isAtBottom = chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < 50;
            scrollToBottomBtn.style.display = isAtBottom ? 'none' : 'flex';
        }

        // Status bar updates
        function updateStatusBar(receiver) {
            connectionStatus.textContent = 'Connected';
            connectionStatus.style.color = 'var(--secondary)'; // Use as a string
            lastSeen.textContent = receiver.is_online ? '' : `Last seen: ${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
        }

        // Real-time updates
        window.Echo.private(`chat.${senderId}`).listen('MessageSent', e => {
            if (e.message.receiver_id === senderId && e.message.sender_id === parseInt(currentReceiverId)) {
                appendMessage(e.message);
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });

        window.Echo.private(`chat.${senderId}`).listen('UserTyping', e => {
            if (e.sender_id === parseInt(currentReceiverId)) {
                typingUser.textContent = document.getElementById('receiver-name').textContent;
                typingIndicator.classList.toggle('active', e.typing);
            }
        });

        window.Echo.channel('user-status')
            .listen('UserOnline', e => {
                const userElement = document.querySelector(`[data-user-id="${e.user.id}"]`);
                if (userElement) {
                    userElement.querySelector('.status-dot').classList.replace('offline', 'online');
                    userElement.querySelector('.status-text').textContent = 'Online';
                }
                if (e.user.id === parseInt(currentReceiverId)) {
                    document.getElementById('receiver-status-dot').classList.replace('offline', 'online');
                    document.getElementById('receiver-status-text').textContent = 'Online';
                    updateStatusBar({ is_online: true });
                }
            })
            .listen('UserOffline', e => {
                const userElement = document.querySelector(`[data-user-id="${e.user.id}"]`);
                if (userElement) {
                    userElement.querySelector('.status-dot').classList.replace('online', 'offline');
                    userElement.querySelector('.status-text').textContent = 'Offline';
                }
                if (e.user.id === parseInt(currentReceiverId)) {
                    document.getElementById('receiver-status-dot').classList.replace('online', 'offline');
                    document.getElementById('receiver-status-text').textContent = 'Offline';
                    updateStatusBar({ is_online: false });
                }
            });

        // Initial status bar setup
        connectionStatus.textContent = 'Connected';
        connectionStatus.style.color = 'var(--secondary)';
    });
</script>
</body>
</html>
