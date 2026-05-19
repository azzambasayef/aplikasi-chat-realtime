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

                            <span class="badge bg-secondary">
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
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span>{{ Auth::user()->getAttribute('name') }}</span>
                    <span class="badge bg-success">Online</span>
                </div>

                <p class="text-muted mb-0">
                    Status online/offline user lain akan dibuat menggunakan Laravel Reverb pada tahap presence tracking.
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
@endsection

