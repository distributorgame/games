<div align="center">

# 🛒 Web PPOB

**Platform PPOB terintegrasi Digiflazz & Midtrans untuk pembelian produk digital**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-2-9553E9?logo=inertia&logoColor=white)](https://inertiajs.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind-4-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker&logoColor=white)](https://docker.com)

[**Live Demo →**](https://topup.karuhundeveloper.com)

![Preview](image.png)

</div>

---

## ✨ Fitur

| Kategori      | Fitur                                                  |
| ------------- | ------------------------------------------------------ |
| **Produk**    | Manajemen Category, Brand & Product PPOB               |
| **Transaksi** | Checkout flow, Payment flow, Riwayat & Tracking status |
| **Pengguna**  | Role-Based Access Control, Manajemen profil            |
| **Konten**    | Manajemen Slider & FAQ                                 |
| **Lainnya**   | Reporting & Analytics, Notifikasi, Sistem Gift         |

---

## 🚀 Quick Start

### Opsi 1 — Docker (Direkomendasikan)

Cara paling mudah untuk menjalankan project secara lokal tanpa perlu setup PHP, MySQL, atau Redis secara manual.

**Prasyarat:** Docker & Docker Compose terinstall.

```bash
# 1. Clone repository
git clone git@github.com:karuhun-developer/webtopup.git
cd webtopup

# 2. Salin file environment
cp .env.docker .env

# 3. Setup otomatis (build image, migrate, generate key)
./docker-run.sh setup
```

Aplikasi berjalan di **http://localhost**

#### Perintah Docker yang Tersedia

```bash
./docker-run.sh setup       # Setup otomatis (build image, migrate, generate key)
./docker-run.sh up          # Mulai semua service
./docker-run.sh down        # Hentikan semua service
./docker-run.sh shell       # Masuk ke bash container app
./docker-run.sh build       # Build ulang image Docker
./docker-run.sh artisan ... # Jalankan perintah Artisan
./docker-run.sh logs        # Tail log semua container
./docker-run.sh test        # Jalankan Pest tests
./docker-run.sh reset       # Reset semua data (hapus volumes)
```

#### Service Docker

| Service     | Image             | Port              |
| ----------- | ----------------- | ----------------- |
| `app`       | PHP 8.4-FPM       | —                 |
| `nginx`     | nginx:1.27-alpine | `80`              |
| `mysql`     | mysql:8.4         | `3306`            |
| `redis`     | redis:7.4-alpine  | `6379`            |
| `queue`     | PHP 8.4-FPM       | —                 |
| `scheduler` | PHP 8.4-FPM       | —                 |
| `vite`      | node:22-alpine    | `5173` (dev only) |

---

### Opsi 2 — Manual

**Prasyarat:** PHP 8.4, MySQL 8, Redis, Node.js 22, Composer.

```bash
# 1. Clone repository
git clone git@github.com:karuhun-developer/webtopup.git
cd webtopup

# 2. Install dependencies
composer install && npm install

# 3. Salin file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Sesuaikan konfigurasi database di .env, lalu jalankan migrasi
php artisan migrate --seed

# 6. Buat symlink storage
php artisan storage:link

# 7. Jalankan development server
composer run dev
```

---

## 🔑 Konfigurasi API

Isi kredensial berikut di file `.env`:

```env
# Midtrans — https://midtrans.com
MIDTRANS_MERCHANT_ID=
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=

# Digiflazz — https://member.digiflazz.com/buyer-area
DIGIFLAZZ_USERNAME=
DIGIFLAZZ_API_KEY=
DIGIFLAZZ_WEBHOOK_SECRET=

# API Games — https://member.apigames.id/pengaturan/secret-key
APIAGAME_MERCHANT_ID=
APIAGAME_SECRET_KEY=
```

---

## 🛠 Tech Stack

- **Backend:** Laravel 12, PHP 8.4
- **Frontend:** Vue 3, Inertia.js v2, TailwindCSS v4
- **Database:** MySQL 8
- **Cache / Queue:** Redis
- **Web Server:** Nginx
- **Auth:** Laravel Fortify + Sanctum
- **Media:** Spatie Media Library
- **Payment:** Midtrans
- **Supplier:** Digiflazz, API Games

---

## ❤️ Dukung Project Ini

Jika project ini bermanfaat, dukung pengembangan lebih lanjut:

**Saweria:** https://saweria.co/reincity
