@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="mb-3">Dashboard Chat</h4>

                <p class="mb-1">
                    Selamat datang, <strong>{{ Auth::user()->name }}</strong>.
                </p>

                <p class="text-muted">
                    Halaman ini nantinya akan digunakan sebagai halaman utama untuk fitur private chat, group chat, dan status online/offline user.
                </p>

                <hr>

                <div class="row">
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light h-100">
                            <h6>Daftar User</h6>
                            <p class="text-muted mb-0">
                                Area ini nantinya menampilkan daftar user untuk private chat.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light h-100">
                            <h6>Group Chat</h6>
                            <p class="text-muted mb-0">
                                Area ini nantinya menampilkan daftar group chat.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light h-100">
                            <h6>Status User</h6>
                            <p class="text-muted mb-0">
                                Area ini nantinya menampilkan status online dan offline.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection