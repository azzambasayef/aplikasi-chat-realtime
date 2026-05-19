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
- [x] Dashboard chat
- [x] Struktur database chat
- [x] Model dan relasi chat
- [x] Private chat
- [x] Group chat
- [x] WebSocket integration
- [x] Private chat realtime
- [x] Group chat realtime
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

## Progress 4 - Chat Dashboard Layout

Pada tahap ini, dashboard aplikasi mulai dibuat menyerupai tampilan aplikasi chat. Dashboard menampilkan daftar user untuk kebutuhan private chat, area ruang chat, bagian group chat, dan bagian user presence.

Fitur pengiriman pesan, group chat, dan status online/offline belum diaktifkan pada tahap ini karena akan dikembangkan secara bertahap pada progress berikutnya.

## Progress 5 - Struktur Database Chat

Pada tahap ini, struktur database untuk fitur chat mulai dibuat. Tabel yang ditambahkan adalah conversations, chat_groups, chat_group_user, dan messages.

Tabel conversations digunakan untuk menyimpan ruang private chat antar dua user. Tabel chat_groups digunakan untuk menyimpan data group chat. Tabel chat_group_user digunakan untuk menyimpan anggota group, sedangkan tabel messages digunakan untuk menyimpan pesan private chat maupun group chat.

## Progress 6 - Model dan Relasi Chat

Pada tahap ini, model untuk Conversation, ChatGroup, dan Message dibuat. Relasi antar model juga ditambahkan agar data user, percakapan private, group chat, anggota group, dan pesan dapat saling terhubung.

Relasi ini menjadi dasar untuk pengembangan fitur private chat dan group chat pada tahap berikutnya.

## Progress 7 - Private Chat Dasar

Pada tahap ini, fitur private chat dasar mulai dibuat. User dapat memilih user lain dari daftar private chat, membuka ruang percakapan, mengirim pesan, dan melihat riwayat pesan.

Pesan yang dikirim sudah tersimpan ke dalam tabel messages dan terhubung dengan tabel conversations. Pada tahap ini, pengiriman pesan masih menggunakan proses request biasa dan belum menggunakan WebSocket atau real-time.

## Progress 8 - Group Chat Dasar

Pada tahap ini, fitur group chat dasar mulai dibuat. User dapat membuat group, memilih anggota group, membuka ruang group chat, mengirim pesan, dan melihat riwayat pesan group.

Data group tersimpan pada tabel chat_groups, data anggota group tersimpan pada tabel chat_group_user, sedangkan pesan group tersimpan pada tabel messages dengan kolom chat_group_id. Pada tahap ini, pengiriman pesan group masih menggunakan request biasa dan belum menggunakan WebSocket atau real-time.

## Progress 9 - WebSocket Integration

Pada tahap ini, Laravel Reverb dan Laravel Echo mulai dikonfigurasi untuk mendukung fitur real-time pada aplikasi chat.

Laravel Reverb digunakan sebagai WebSocket server, sedangkan Laravel Echo digunakan pada sisi frontend untuk menerima event yang dikirim melalui broadcasting. Pada tahap ini, konfigurasi WebSocket sudah berhasil disiapkan dan aplikasi tetap dapat berjalan menggunakan Laravel server, Reverb server, dan Vite secara bersamaan.

## Progress 10 - Private Chat Realtime

Pada tahap ini, fitur private chat mulai menggunakan Laravel Reverb dan Laravel Echo. Pesan private yang dikirim oleh user akan dibroadcast melalui private channel berdasarkan conversation_id.

Dengan fitur ini, penerima pesan dapat melihat pesan baru secara otomatis tanpa melakukan refresh halaman, selama penerima sedang membuka ruang percakapan yang sama. Pengujian dilakukan menggunakan dua akun berbeda melalui browser biasa dan incognito maupun browser lainnya.

## Progress 11 - Group Chat Realtime

Pada tahap ini, fitur group chat mulai menggunakan Laravel Reverb dan Laravel Echo. Pesan group yang dikirim oleh user akan dibroadcast melalui private channel berdasarkan chat_group_id.

Dengan fitur ini, anggota group yang sedang membuka room group yang sama dapat menerima pesan baru secara otomatis tanpa melakukan refresh halaman.