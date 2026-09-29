# PokéVault

PokéVault adalah aplikasi web untuk menjelajahi data Pokémon dari [PokéAPI](https://pokeapi.co) dan mengelola koleksi pribadi pengguna.

## 🚀 Fitur Utama

- **Jelajah Pokémon**: Daftar Pokémon lengkap dengan pagination dan pencarian (proxy via PokéAPI dengan *caching*).
- **Detail Pokémon**: Menampilkan gambar, stats, abilities, tinggi, dan berat.
- **Koleksi Pribadi**: Simpan Pokémon favorit ke dalam koleksi Anda.
- **Ubah Koleksi**: Beri nickname (maks 30 karakter), set level (1-100), dan tambahkan catatan.
- **Manajemen Koleksi**: Cari, urutkan, dan hapus Pokémon dari koleksi.

## 🛠️ Tech Stack (TALL)

- **T**ailwind CSS (v4) + daisyUI untuk styling
- **A**lpine.js untuk interaksi UI ringan
- **L**aravel 11 (PHP 8.3)
- **L**ivewire 3 untuk reaktivitas
- MySQL 8

## 🐳 Panduan Instalasi (Docker)

Aplikasi ini dirancang agar dapat dijalankan sepenuhnya melalui **Docker Compose**, tanpa perlu instalasi PHP atau Node.js secara lokal.

### Prasyarat
- Docker
- Docker Compose

### Langkah-langkah

1. **Clone repository ini** (jika belum).
2. **Copy file `.env`**
   ```bash
   cp .env.example .env
   ```
3. **Jalankan Docker Compose**
   ```bash
   docker compose up -d --build
   ```
   *Proses ini akan men-download image dan menginstall dependency PHP & Node. Tunggu beberapa saat hingga selesai.*

4. **Build Frontend Assets**
   Karena *local volume* menimpa file build di dalam container, Anda perlu mem-build aset UI-nya sekali setelah container jalan:
   ```bash
   docker compose exec app npm run build
   ```

5. **Jalankan Migration Database**
   Karena database MySQL butuh beberapa detik untuk siap, tunggu sejenak sebelum menjalankan perintah ini:
   ```bash
   docker compose exec app php artisan migrate --force
   ```

6. **Akses Aplikasi**
   Buka browser dan akses: [http://localhost:8000](http://localhost:8000)

## 📡 Dokumentasi Endpoint API

Selain antarmuka web, PokéVault menyediakan REST API yang bisa diakses dengan prefix `/api`.

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/pokemon` | Daftar Pokémon dari PokéAPI (Query params: `limit`, `offset`, `search`) |
| `GET` | `/api/pokemon/{nameOrId}` | Detail Pokémon dari PokéAPI |
| `GET` | `/api/collection` | Daftar koleksi (Query params: `search`, `sort_by`, `sort_dir`) |
| `GET` | `/api/collection/{id}` | Detail satu entri koleksi |
| `POST` | `/api/collection` | Tambah ke koleksi. *Body: `pokemon_id`, `nickname`, `level`, `notes`* |
| `PUT` | `/api/collection/{id}` | Ubah koleksi. *Body: `nickname`, `level`, `notes`* |
| `DELETE` | `/api/collection/{id}` | Hapus dari koleksi |

### Contoh Request & Response (POST /api/collection)

**Request:**
```json
{
  "pokemon_id": 25,
  "nickname": "Sparky",
  "level": 12,
  "notes": "Starter pertama saya"
}
```

**Response (201 Created):**
```json
{
  "pokemon_id": 25,
  "name": "pikachu",
  "image_url": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/25.png",
  "types": ["electric"],
  "nickname": "Sparky",
  "level": 12,
  "notes": "Starter pertama saya",
  "updated_at": "2026-09-29T10:00:00.000000Z",
  "created_at": "2026-09-29T10:00:00.000000Z",
  "id": 1
}
```

### Format Error Standar

Semua error dari API akan dikembalikan dengan format standar seperti berikut:

**Response (400 Bad Request):**
```json
{
  "error": {
    "code": "ERROR",
    "message": "Level harus bernilai antara 1 dan 100."
  }
}
```

## 🔧 Troubleshooting

- **Aset CSS/JS tidak termuat**: Pastikan perintah `npm run build` berjalan dengan baik pada saat image di-build (dilakukan otomatis di Dockerfile). Jika butuh mem-build ulang: `docker compose exec app npm run build`.

## 📜 Lisensi
Open source di bawah [MIT License](https://opensource.org/licenses/MIT).
