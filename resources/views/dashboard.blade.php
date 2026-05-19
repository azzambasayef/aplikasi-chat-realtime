@extends('layouts.app')

@section('content')
@php
    $availableUsers = collect($users ?? []);
    $userGroups = collect($chatGroups ?? []);
    $chatMessages = collect($messages ?? []);

    $activeUserId = isset($selectedUser) ? (int) $selectedUser->getKey() : null;
    $activeGroupId = isset($selectedGroup) ? (int) $selectedGroup->getKey() : null;
@endphp

<div class="row g-3">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold">
                Private Chat
            </div>

            <div class="card-body p-0">
                @forelse ($availableUsers as $privateUser)
                    @php
                        $privateUserId = (int) $privateUser->getKey();
                        $privateUserName = $privateUser->getAttribute('name');
                        $privateUserEmail = $privateUser->getAttribute('email');
                    @endphp

                    <a 
                        href="{{ route('private.chat', $privateUserId) }}" 
                        class="text-decoration-none text-dark"
                    >
                        <div class="d-flex align-items-center justify-content-between border-bottom p-3 {{ $activeUserId === $privateUserId ? 'bg-light' : '' }}">
                            <div>
                                <div class="fw-semibold">{{ $privateUserName }}</div>
                                <small class="text-muted">{{ $privateUserEmail }}</small>
                            </div>

                            <span 
                                class="badge bg-secondary"
                                data-presence-user-id="{{ $privateUserId }}"
                            >
                                Offline
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-3 text-muted">
                        Belum ada user lain.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white">
                @isset($selectedUser)
                    <div class="fw-bold">{{ $selectedUser->getAttribute('name') }}</div>
                    <small class="text-muted">{{ $selectedUser->getAttribute('email') }}</small>
                @elseif(isset($selectedGroup))
                    <div class="fw-bold">{{ $selectedGroup->getAttribute('name') }}</div>
                    <small class="text-muted">
                        {{ $selectedGroup->getAttribute('description') ?? 'Group chat' }}
                    </small>
                @else
                    <div class="fw-bold">Ruang Chat</div>
                    <small class="text-muted">
                        Pilih user atau group untuk mulai chat.
                    </small>
                @endisset
            </div>

            <div id="chat-messages" class="card-body" style="min-height: 420px; max-height: 420px; overflow-y: auto;">
                @if(isset($selectedUser) || isset($selectedGroup))
                    @forelse ($chatMessages as $chatMessage)
                        @php
                            $senderId = (int) $chatMessage->getAttribute('sender_id');
                            $messageText = $chatMessage->getAttribute('message');
                            $createdAt = $chatMessage->getAttribute('created_at');
                            $sender = $chatMessage->getRelationValue('sender');

                            $isOwnMessage = $senderId === (int) Auth::id();
                            $senderName = $isOwnMessage ? 'Saya' : ($sender?->getAttribute('name') ?? 'User');
                        @endphp

                        <div class="d-flex mb-3 {{ $isOwnMessage ? 'justify-content-end' : 'justify-content-start' }}">
                            <div 
                                class="p-3 rounded shadow-sm {{ $isOwnMessage ? 'bg-primary text-white' : 'bg-light' }}"
                                style="max-width: 75%;"
                            >
                                <div class="small fw-bold mb-1">
                                    {{ $senderName }}
                                </div>

                                <div>
                                    {{ $messageText }}
                                </div>

                                <div class="small mt-2 {{ $isOwnMessage ? 'text-white-50' : 'text-muted' }}">
                                    {{ $createdAt ? $createdAt->format('H:i') : '-' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted mt-5">
                            <h5>Belum ada pesan</h5>
                            <p class="mb-0">
                                Mulai percakapan dengan mengirim pesan pertama.
                            </p>
                        </div>
                    @endforelse
                @else
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center text-muted">
                            <h5>Belum ada percakapan dipilih</h5>
                            <p class="mb-0">
                                Pilih user atau group untuk membuka ruang chat.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="card-footer bg-white">
                @isset($selectedUser)
                    <form action="{{ route('private.chat.send', $selectedUser->getKey()) }}" method="POST">
                        @csrf

                        <div class="input-group">
                            <input 
                                type="text" 
                                name="message"
                                class="form-control @error('message') is-invalid @enderror" 
                                placeholder="Tulis pesan..." 
                                autocomplete="off"
                                required
                            >
                            <button class="btn btn-primary" type="submit">
                                Kirim
                            </button>

                            @error('message')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </form>
                @elseif(isset($selectedGroup))
                    <form action="{{ route('group.chat.send', $selectedGroup->getKey()) }}" method="POST">
                        @csrf

                        <div class="input-group">
                            <input 
                                type="text" 
                                name="message"
                                class="form-control @error('message') is-invalid @enderror" 
                                placeholder="Tulis pesan ke group..." 
                                autocomplete="off"
                                required
                            >
                            <button class="btn btn-primary" type="submit">
                                Kirim
                            </button>

                            @error('message')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </form>
                @else
                    <div class="input-group">
                        <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Tulis pesan..." 
                            disabled
                        >
                        <button class="btn btn-primary" disabled>
                            Kirim
                        </button>
                    </div>
                    <small class="text-muted">
                        Pilih user atau group terlebih dahulu untuk mengirim pesan.
                    </small>
                @endisset
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white fw-bold">
                Group Chat
            </div>

            <div class="card-body p-0">
                @forelse ($userGroups as $group)
                    @php
                        $groupId = (int) $group->getKey();
                        $groupName = $group->getAttribute('name');
                        $groupDescription = $group->getAttribute('description') ?? 'Tidak ada deskripsi';
                    @endphp

                    <a 
                        href="{{ route('group.chat', $groupId) }}" 
                        class="text-decoration-none text-dark"
                    >
                        <div class="border-bottom p-3 {{ $activeGroupId === $groupId ? 'bg-light' : '' }}">
                            <div class="fw-semibold">{{ $groupName }}</div>
                            <small class="text-muted">
                                {{ $groupDescription }}
                            </small>
                        </div>
                    </a>
                @empty
                    <div class="p-3 text-muted">
                        Belum ada group chat.
                    </div>
                @endforelse
            </div>
        </div>

        @if (isset($selectedGroup))
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white fw-bold">
                Anggota Group
            </div>

            <div class="card-body p-0">
                @forelse (($selectedGroupMembers ?? collect()) as $member)
                    @php
                        $memberId = (int) $member->getKey();
                        $memberName = $member->getAttribute('name');
                        $memberEmail = $member->getAttribute('email');
                        $groupCreatorId = (int) $selectedGroup->getAttribute('created_by');
                        $isCreator = $memberId === $groupCreatorId;
                    @endphp

                    <div class="border-bottom p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">
                                    {{ $memberName }}

                                    @if ($isCreator)
                                        <span class="badge bg-primary ms-1">Creator</span>
                                    @endif
                                </div>

                                <small class="text-muted">
                                    {{ $memberEmail }}
                                </small>
                            </div>

                            @if (($canManageSelectedGroup ?? false) && !$isCreator)
                                <form 
                                    action="{{ route('group.members.remove', [$selectedGroup->getKey(), $memberId]) }}" 
                                    method="POST"
                                    onsubmit="return confirm('Hapus anggota ini dari group?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-3 text-muted">
                        Belum ada anggota group.
                    </div>
                @endforelse
            </div>
        </div>

        @if ($canManageSelectedGroup ?? false)
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white fw-bold">
                    Tambah Anggota
                </div>

                <div class="card-body">
                    @if (($availableGroupMembers ?? collect())->count() > 0)
                        <form action="{{ route('group.members.add', $selectedGroup->getKey()) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="user_id" class="form-label">Pilih User</label>
                                <select 
                                    name="user_id" 
                                    id="user_id" 
                                    class="form-select @error('user_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">-- Pilih anggota --</option>

                                    @foreach ($availableGroupMembers as $availableUser)
                                        <option value="{{ $availableUser->getKey() }}">
                                            {{ $availableUser->getAttribute('name') }} - {{ $availableUser->getAttribute('email') }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('user_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-outline-primary w-100">
                                Tambah ke Group
                            </button>
                        </form>
                    @else
                        <p class="text-muted mb-0">
                            Semua user sudah menjadi anggota group ini.
                        </p>
                    @endif
                </div>
            </div>
        @endif
    @endif

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white fw-bold">
                Buat Group
            </div>

            <div class="card-body">
                <form action="{{ route('groups.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Group</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Masukkan nama group"
                            required
                        >

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea 
                            name="description" 
                            id="description" 
                            rows="2" 
                            class="form-control"
                            placeholder="Deskripsi singkat group"
                        ></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Anggota</label>

                        @forelse ($availableUsers as $memberUser)
                            @php
                                $memberUserId = (int) $memberUser->getKey();
                                $memberUserName = $memberUser->getAttribute('name');
                            @endphp

                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    name="member_ids[]" 
                                    value="{{ $memberUserId }}" 
                                    id="member-{{ $memberUserId }}"
                                >
                                <label class="form-check-label" for="member-{{ $memberUserId }}">
                                    {{ $memberUserName }}
                                </label>
                            </div>
                        @empty
                            <small class="text-muted">
                                Belum ada user lain yang dapat ditambahkan.
                            </small>
                        @endforelse
                    </div>

                    <button type="submit" class="btn btn-outline-primary w-100">
                        Buat Group
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                User Presence
            </div>

            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-semibold">Daftar User Online</span>
                    <span id="online-users-count" class="badge bg-success">
                        0 online
                    </span>
                </div>

                <div id="online-users-list" class="small text-muted">
                    Memuat status online...
                </div>

                <p class="text-muted mb-0 mt-3">
                    Status online/offline ditampilkan menggunakan Laravel Reverb dan presence channel.
                </p>
            </div>
        </div>
    </div>
</div>

@if (isset($selectedConversation))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const conversationId = Number("{{ $selectedConversation->getKey() }}");
        const authUserId = Number("{{ Auth::id() }}");
        const chatBox = document.getElementById('chat-messages');

        if (!window.Echo || !conversationId || !chatBox) {
            return;
        }

        window.Echo.private('conversation.' + conversationId)
            .listen('.private.message.sent', function (event) {
                const data = event.message;

                if (Number(data.sender_id) === authUserId) {
                    return;
                }

                appendMessage(data);
            });

        function appendMessage(data) {
            const wrapper = document.createElement('div');
            wrapper.className = 'd-flex mb-3 justify-content-start';

            wrapper.innerHTML = `
                <div class="p-3 rounded shadow-sm bg-light" style="max-width: 75%;">
                    <div class="small fw-bold mb-1">
                        ${escapeHtml(data.sender_name)}
                    </div>

                    <div>
                        ${escapeHtml(data.message)}
                    </div>

                    <div class="small mt-2 text-muted">
                        ${escapeHtml(data.created_at)}
                    </div>
                </div>
            `;

            chatBox.appendChild(wrapper);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function escapeHtml(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }
    });
</script>
@endif

@if (isset($selectedGroup))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const groupId = Number("{{ $selectedGroup->getKey() }}");
        const authUserId = Number("{{ Auth::id() }}");
        const chatBox = document.getElementById('chat-messages');

        if (!window.Echo || !groupId || !chatBox) {
            return;
        }

        window.Echo.private('chat-group.' + groupId)
            .listen('.group.message.sent', function (event) {
                const data = event.message;

                if (Number(data.sender_id) === authUserId) {
                    return;
                }

                appendGroupMessage(data);
            });

        function appendGroupMessage(data) {
            const wrapper = document.createElement('div');
            wrapper.className = 'd-flex mb-3 justify-content-start';

            wrapper.innerHTML = `
                <div class="p-3 rounded shadow-sm bg-light" style="max-width: 75%;">
                    <div class="small fw-bold mb-1">
                        ${escapeHtml(data.sender_name)}
                    </div>

                    <div>
                        ${escapeHtml(data.message)}
                    </div>

                    <div class="small mt-2 text-muted">
                        ${escapeHtml(data.created_at)}
                    </div>
                </div>
            `;

            chatBox.appendChild(wrapper);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function escapeHtml(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const onlineUsers = new Map();
        const onlineUsersList = document.getElementById('online-users-list');
        const onlineUsersCount = document.getElementById('online-users-count');

        if (!window.Echo) {
            return;
        }

        window.Echo.join('online-users')
            .here(function (users) {
                onlineUsers.clear();

                users.forEach(function (user) {
                    onlineUsers.set(Number(user.id), user);
                });

                updatePresenceView();
            })
            .joining(function (user) {
                onlineUsers.set(Number(user.id), user);
                updatePresenceView();
            })
            .leaving(function (user) {
                onlineUsers.delete(Number(user.id));
                updatePresenceView();
            })
            .error(function (error) {
                console.error('Presence channel error:', error);
            });

        function updatePresenceView() {
            updateUserBadges();
            updateOnlineUserList();
        }

        function updateUserBadges() {
            const badges = document.querySelectorAll('[data-presence-user-id]');

            badges.forEach(function (badge) {
                const userId = Number(badge.getAttribute('data-presence-user-id'));
                const isOnline = onlineUsers.has(userId);

                badge.textContent = isOnline ? 'Online' : 'Offline';

                badge.classList.remove('bg-success', 'bg-secondary');
                badge.classList.add(isOnline ? 'bg-success' : 'bg-secondary');
            });
        }

        function updateOnlineUserList() {
            const users = Array.from(onlineUsers.values())
                .sort(function (firstUser, secondUser) {
                    return firstUser.name.localeCompare(secondUser.name);
                });

            if (onlineUsersCount) {
                onlineUsersCount.textContent = users.length + ' online';
            }

            if (!onlineUsersList) {
                return;
            }

            if (users.length === 0) {
                onlineUsersList.innerHTML = '<div class="text-muted">Belum ada user online.</div>';
                return;
            }

            onlineUsersList.innerHTML = users.map(function (user) {
                return `
                    <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                        <div>
                            <div class="fw-semibold">${escapeHtml(user.name)}</div>
                            <div class="text-muted">${escapeHtml(user.email)}</div>
                        </div>
                        <span class="badge bg-success">Online</span>
                    </div>
                `;
            }).join('');
        }

        function escapeHtml(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }
    });
</script>

@endsection

