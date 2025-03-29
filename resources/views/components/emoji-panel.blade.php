<div class="emoji-panel" id="emoji-panel">
    @php
        $categories = [
            'Smilies & People' => [
                '😀', '😂', '😍', '😎', '🥳', '😢', '😡', '🤓', '😜', '🤩', '😴', '🤔', '😷', '🤠', '👀', '💀',
                '🤯', '🤗', '😇', '🥵', '🥶', '😈', '👽', '🤖', '💩', '👻', '🤡', '👶', '🧒', '👦', '👧', '🧑', '👨', '👩',
                '👴', '👵', '🧔', '👱‍♂️', '👱‍♀️', '🧑‍🎓', '🧑‍⚕️', '🧑‍🚀', '🧑‍🔬', '🧑‍🎨', '🧑‍💼', '👰', '🤵', '🤰', '🤱', '🧑‍🍼'
            ],
            'Animals & Nature' => [
                '🐶', '🐱', '🌳', '🌸', '🐻', '🦁', '🌞', '🌙', '🐘', '🦄', '🐬', '🐢', '🐍', '🐜', '🌻', '🌊',
                '🐵', '🦊', '🐺', '🐗', '🐯', '🐴', '🐒', '🦓', '🦥', '🦦', '🦨', '🦘', '🐇', '🐿️', '🦔', '🐉', '🦚',
                '🐨', '🐼', '🦆', '🐔', '🦢', '🦉', '🐊', '🦎', '🐀', '🦇', '🐙', '🦑', '🦐', '🦞', '🦀', '🐡'
            ],
            'Food & Drink' => [
                '🍕', '🍔', '🍣', '🍷', '🍎', '🍓', '☕', '🍰', '🌮', '🍩', '🍉', '🍗', '🥗', '🍹', '🍞', '🥑',
                '🍇', '🍍', '🥥', '🍒', '🥕', '🌽', '🌶️', '🥒', '🍔', '🍟', '🍗', '🍛', '🍜', '🥘', '🍲', '🥂',
                '🥖', '🧀', '🍖', '🥩', '🥞', '🧇', '🧁', '🍮', '🥜', '🍯', '🍵', '🥃', '🍼'
            ],
            'Travel & Places' => [
                '🚗', '✈️', '🚀', '🚲', '🏝️', '🏔️', '🏰', '🌆', '⛩️', '🚂', '🗽', '🌍', '🛳️', '⛵', '🚁', '🚦',
                '🚆', '🚤', '🚜', '🏟️', '🏕️', '🛣️', '🗻', '🏜️', '🏙️', '🌇', '🏚️', '🏠', '🏢', '🕌', '⛪', '🛕',
                '🏖️', '🏗️', '🏥', '🏫', '🏨', '🕍', '🎡', '🎢', '🎠', '🗼', '🏯', '🏩'
            ],
            'Activities & Sports' => [
                '⚽', '🏀', '🏈', '⚾', '🎾', '🏐', '🏓', '🥊', '🏆', '🎯', '🎮', '🃏', '🎸', '🎤', '🎻', '🎭',
                '🎳', '⛳', '🥌', '🛹', '🎱', '🤿', '🏄', '🪂', '🏋️‍♂️', '🤼‍♂️', '🤸‍♀️', '⛷️', '🏂', '🏇', '🚴‍♂️', '🏊‍♂️',
                '🧗‍♂️', '🚣‍♂️', '🏏', '🏑', '🥏', '🥎', '🏒', '🥍', '🛼'
            ],
            'Objects' => [
                '📱', '💻', '🖥️', '⌚', '🔑', '📷', '📚', '💡', '🎁', '💰', '📞', '🎈', '🛠️', '🔋', '🕰️', '🖊️',
                '📜', '🖍️', '📎', '✂️', '📌', '🛏️', '🚪', '🛋️', '🚽', '🛁', '🚿', '🔦', '🎤', '📺', '🎧', '📠',
                '📀', '💿', '🖱️', '🖲️', '📡', '🔬', '🔭', '🕹️', '🖇️', '🖨️'
            ],
            'Symbols' => [
                '❤️', '💔', '⭐', '🎵', '✔️', '❌', '🔔', '🔒', '💯', '🆘', '☢️', '⚠️', '🔆', '🎀', '💌', '📛',
                '💟', '💲', '🔣', '➕', '➖', '💠', '♾️', '⚜️', '🔱', '📳', '♻️', '🔄', '🔃', '❗', '💤', '🔥',
                '💠', '🌀', '🏧', '🈹', '🈲', '🉐', '⚕️', '🛑', '🚸'
            ],
            'Flags' => [
                '🏳️', '🏴', '🏁', '🚩', '🇺🇸', '🇬🇧', '🇨🇦', '🇦🇺', '🇩🇪', '🇫🇷', '🇮🇹', '🇪🇸', '🇯🇵', '🇰🇷', '🇧🇷', '🇲🇽',
                '🇷🇺', '🇨🇳', '🇮🇳', '🇹🇷', '🇸🇦', '🇿🇦', '🇦🇪', '🇦🇷', '🇸🇬', '🇳🇴', '🇸🇪', '🇳🇱', '🇧🇪', '🇨🇭', '🇩🇰', '🇵🇭',
                '🇮🇩', '🇵🇰', '🇵🇹', '🇮🇪', '🇳🇿', '🇬🇷', '🇵🇱', '🇨🇴', '🇪🇬', '🇲🇾', '🇹🇭', '🇻🇳'
            ]
        ];

    @endphp

    @foreach ($categories as $category => $emojis)
        <div class="emoji-category">
            <h4>{{ $category }}</h4>
            <div class="emoji-grid">
                @foreach ($emojis as $emoji)
                    <div class="emoji" data-emoji="{{ $emoji }}">{{ $emoji }}</div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
