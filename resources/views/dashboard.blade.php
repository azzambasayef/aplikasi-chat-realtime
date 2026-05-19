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
                    <div class="d-flex align-items-center justify-content-between border-bottom p-3">
                        <div>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>

                        <span class="badge bg-secondary">
                            Offline
                        </span>
                    </div>
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
                <div class="fw-bold">Ruang Chat</div>
                <small class="text-muted">
                    Pilih user atau group untuk mulai chat.
                </small>
            </div>

            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 420px;">
                <div class="text-center text-muted">
                    <h5>Belum ada percakapan dipilih</h5>
                    <p class="mb-0">
                        Fitur pengiriman pesan akan dibuat pada tahap private chat dan group chat.
                    </p>
                </div>
            </div>

            <div class="card-footer bg-white">
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
                    Form pesan masih dinonaktifkan sampai fitur chat dibuat.
                </small>
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