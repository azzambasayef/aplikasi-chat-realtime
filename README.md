# Aplikasi Chat Realtime

Project ini merupakan aplikasi chat real-time berbasis Laravel yang dibuat untuk memenuhi tugas 2 dari mata kuliah Pemrograman Web 2.

Aplikasi ini dirancang agar user dapat melakukan autentikasi, mengirim pesan pribadi, bergabung dalam group chat, serta melihat status online/offline user secara real-time menggunakan Laravel Reverb.

## Fitur yang Direncanakan

- User Authentication
- Private Chat
- Group Chat
- WebSocket Integration
- User Presence Tracking (Online/Offline)

## Teknologi yang Digunakan

- Laravel 12
- PHP
- MySQL
- Bootstrap
- Laravel Reverb
- Laravel Echo
- XAMPP

## Progress Pengembangan

- [x] Inisialisasi project Laravel
- [x] Pembuatan dokumentasi awal
- [x] Konfigurasi database MySQL
- [x] Menjalankan migration awal Laravel
- [x] User authentication
- [ ] Dashboard chat
- [ ] Private chat
- [ ] Group chat
- [ ] WebSocket integration
- [ ] User presence tracking

## Catatan Pengembangan

Project ini dikembangkan secara bertahap. Setiap fitur akan dibuat dan diuji terlebih dahulu sebelum dilanjutkan ke fitur berikutnya.

## Progress 1 - Initial Setup

Pada tahap awal, project Laravel berhasil dibuat dan dihubungkan ke repository GitHub. Project juga sudah dapat dijalankan secara lokal melalui Laravel development server.

## Progress 2 - Database Configuration

Pada tahap ini, aplikasi dikonfigurasi agar terhubung dengan database MySQL menggunakan XAMPP. Migration bawaan Laravel sudah dijalankan untuk membuat tabel awal seperti users, cache, jobs, dan migrations.

## Progress 3 - User Authentication

Pada tahap ini, aplikasi sudah memiliki fitur autentikasi dasar. User dapat melakukan register, login, logout, dan mengakses dashboard setelah berhasil login.

Halaman dashboard juga sudah dilindungi menggunakan middleware auth, sehingga hanya user yang sudah login yang dapat mengakses halaman tersebut. Selain itu, data user yang melakukan registrasi berhasil tersimpan ke dalam tabel users pada database MySQL.