@extends('layouts.app')

@section('content')
<div class="row g-3">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold">
                Private Chat
            </div>

            <div class="card-body p-0">
                @forelse ($users as $user)
                    <a 
                        href="{{ route('private.chat', $user->id) }}" 
                        class="text-decoration-none text-dark"
                    >
                        <div class="d-flex align-items-center justify-content-between border-bottom p-3 
                            {{ isset($selectedUser) && $selectedUser->id === $user->id ? 'bg-light' : '' }}">
                            <div>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <small class="text-muted">{{ $user->email }}</small>
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
                    <div class="fw-bold">{{ $selectedUser->name }}</div>
                    <small class="text-muted">{{ $selectedUser->email }}</small>
                @else
                    <div class="fw-bold">Ruang Chat</div>
                    <small class="text-muted">
                        Pilih user untuk mulai private chat.
                    </small>
                @endisset
            </div>

            <div class="card-body" style="min-height: 420px; max-height: 420px; overflow-y: auto;">
                @isset($selectedUser)
                    @forelse ($messages as $message)
                        @php
                            $isOwnMessage = $message->sender_id === Auth::id();
                        @endphp

                        <div class="d-flex mb-3 {{ $isOwnMessage ? 'justify-content-end' : 'justify-content-start' }}">
                            <div 
                                class="p-3 rounded shadow-sm {{ $isOwnMessage ? 'bg-primary text-white' : 'bg-light' }}"
                                style="max-width: 75%;"
                            >
                                <div class="small fw-bold mb-1">
                                    {{ $isOwnMessage ? 'Saya' : $message->sender->name }}
                                </div>

                                <div>
                                    {{ $message->message }}
                                </div>

                                <div class="small mt-2 {{ $isOwnMessage ? 'text-white-50' : 'text-muted' }}">
                                    {{ $message->created_at->format('H:i') }}
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
                                Pilih salah satu user di sebelah kiri untuk membuka private chat.
                            </p>
                        </div>
                    </div>
                @endisset
            </div>

            <div class="card-footer bg-white">
                @isset($selectedUser)
                    <form action="{{ route('private.chat.send', $selectedUser->id) }}" method="POST">
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
                        Pilih user terlebih dahulu untuk mengirim pesan.
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

            <div class="card-body">
                <p class="text-muted mb-3">
                    Belum ada group chat.
                </p>

                <button class="btn btn-outline-primary w-100" disabled>
                    Buat Group
                </button>

                <small class="text-muted d-block mt-2">
                    Fitur group akan dibuat pada tahap berikutnya.
                </small>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                User Presence
            </div>

            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span>{{ Auth::user()->name }}</span>
                    <span class="badge bg-success">Online</span>
                </div>

                <p class="text-muted mb-0">
                    Status online/offline user lain akan dibuat menggunakan Laravel Reverb pada tahap presence tracking.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection