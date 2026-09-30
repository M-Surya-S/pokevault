# Product Requirements Document (PRD)
## PokéVault

| | |
|---|---|
| **Versi** | 1.0 |
| **Status** | Draft |
| **Penulis** | Muhammad Surya Saniansyah |
| **Tanggal** | 29 September 2026 |

---

## 1. Ringkasan

**PokéVault** adalah aplikasi web untuk menjelajahi data Pokémon dari PokéAPI dan mengelola koleksi pribadi pengguna. Pengguna dapat mencari Pokémon, melihat detailnya, menambahkannya ke koleksi, mengubah data koleksi (nickname, level, catatan), dan menghapusnya.

## 2. Latar Belakang dan Tujuan

PokéAPI (https://pokeapi.co) adalah REST API publik yang menyediakan data Pokémon. API ini bersifat **read-only** (hanya mendukung `GET`), sehingga operasi Create, Update, dan Delete tidak dapat dilakukan langsung ke PokéAPI.

**Solusi:** aplikasi memiliki backend dan database sendiri untuk menyimpan koleksi pengguna. Data master Pokémon diambil dari PokéAPI, sedangkan data koleksi dikelola sepenuhnya oleh aplikasi.

**Tujuan:**
1. Mengimplementasikan fungsionalitas CRUD lengkap yang berjalan di atas data dari PokéAPI.
2. Mendemonstrasikan integrasi API pihak ketiga, desain REST API, dan penanganan error yang baik.
3. Menghasilkan aplikasi yang mudah dijalankan, terdokumentasi, dan mudah dikembangkan.

**Bukan tujuan (out of scope):**
- Autentikasi dan multi-user
- Fitur battle, trading, atau fitur sosial
- Mengubah atau menulis data ke PokéAPI
- Deployment ke lingkungan produksi (lingkup tugas: lingkungan lokal berbasis Docker)

## 3. Asumsi

- Aplikasi dipakai oleh satu pengguna (tanpa login).
- PokéAPI tersedia secara publik dan tanpa API key.
- Docker dan Docker Compose sudah terpasang di mesin yang menjalankan aplikasi.
- "CRUD" dimaknai sebagai: **Read** dari PokéAPI, **Create/Update/Delete** pada koleksi lokal.

## 4. Pengguna Target

Penggemar Pokémon atau pengguna umum yang ingin mencatat dan mengelola Pokémon favorit atau tim miliknya.

## 5. Kebutuhan Fungsional

### 5.1 Read: Jelajah Pokémon (sumber: PokéAPI)

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-01 | Menampilkan daftar Pokémon dengan pagination (default 20 per halaman) | Must |
| FR-02 | Mencari Pokémon berdasarkan nama | Must |
| FR-03 | Menampilkan halaman detail: gambar, id, nama, tinggi, berat, tipe, abilities, dan base stats | Must |
| FR-04 | Menampilkan status "sudah di koleksi" pada Pokémon yang telah disimpan | Should |
| FR-05 | Filter berdasarkan tipe | Could |

### 5.2 Create: Tambah ke Koleksi

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-06 | Pengguna dapat menambahkan Pokémon ke koleksi dari daftar atau halaman detail | Must |
| FR-07 | Sistem menyimpan snapshot data yang diperlukan (id, nama, gambar, tipe) beserta field milik pengguna (nickname, level, catatan) | Must |
| FR-08 | Pokémon yang sama tidak boleh ditambahkan dua kali; sistem menampilkan pesan yang jelas | Must |

### 5.3 Read: Koleksi Saya

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-09 | Menampilkan seluruh Pokémon dalam koleksi | Must |
| FR-10 | Melihat detail satu entri koleksi | Must |
| FR-11 | Mencari dan mengurutkan koleksi (nama, level) | Could |

### 5.4 Update: Ubah Koleksi

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-12 | Pengguna dapat mengubah nickname, level, dan catatan | Must |
| FR-13 | Validasi input: level bilangan bulat 1–100, nickname maksimal 30 karakter, catatan maksimal 200 karakter | Must |

### 5.5 Delete: Hapus dari Koleksi

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-14 | Pengguna dapat menghapus Pokémon dari koleksi | Must |
| FR-15 | Sistem menampilkan dialog konfirmasi sebelum menghapus | Should |

## 6. Kebutuhan Non-Fungsional

| ID | Kebutuhan |
|---|---|
| NFR-01 | **Error handling:** kegagalan PokéAPI, Pokémon tidak ditemukan, dan error validasi ditampilkan dengan pesan yang jelas |
| NFR-02 | **UX:** menampilkan loading state dan empty state (misalnya koleksi kosong) |
| NFR-03 | **Performa:** daftar Pokémon tampil dalam waktu wajar; respons PokéAPI di-cache di backend untuk mengurangi pemanggilan berulang |
| NFR-04 | **Responsif:** tampilan berfungsi baik di desktop dan mobile |
| NFR-05 | **Kode:** mengikuti konvensi Laravel, struktur folder rapi, penamaan konsisten, logika bisnis dipisah dari komponen Livewire (service/action class), konfigurasi via `.env` |
| NFR-06 | **Dokumentasi:** README lengkap dan dokumentasi endpoint API |
| NFR-07 | **Environment:** seluruh aplikasi dapat dijalankan dengan Docker Compose tanpa instalasi PHP, Node, atau MySQL secara lokal |

## 7. Arsitektur

```
Browser
   │  (Blade + Livewire + Alpine.js + Tailwind CSS + daisyUI)
   ▼
Laravel (Livewire components ──► Service/Action layer)
   │                                   │
   ▼                                   └──► PokéAPI (GET saja, dengan cache)
MySQL (koleksi)
```

Seluruh komponen dijalankan sebagai container Docker melalui `docker compose`.

**Tech stack: TALL Stack**

| Lapisan | Teknologi | Peran |
|---|---|---|
| **T**ailwind CSS | Styling utility-first | Antarmuka responsif |
| daisyUI | Plugin komponen UI untuk Tailwind | Card, tombol, modal konfirmasi, form input, pagination, alert, badge, loading, dan tema |
| **A**lpine.js | Interaksi ringan di sisi klien | Dialog konfirmasi, dropdown, state UI kecil |
| **L**ivewire | Komponen UI dinamis | Daftar, pencarian, form tambah/ubah, hapus tanpa reload halaman |
| **L**aravel | Backend framework | Routing, validasi, Eloquent ORM, HTTP Client, Cache |
| Database | MySQL 8 | Penyimpanan koleksi |
| Environment | Docker + Docker Compose | Menjalankan app (PHP-FPM), web server (Nginx), dan MySQL secara konsisten |
| Lainnya | Git, file `.env`, Vite (build aset), Pest/PHPUnit (test) | |

**Layanan Docker Compose**

| Service | Image / Fungsi | Keterangan |
|---|---|---|
| `app` | PHP-FPM 8.x + Composer + Node | Menjalankan Laravel dan build aset |
| `web` | Nginx | Melayani aplikasi pada `http://localhost:8000` |
| `db` | MySQL 8 | Data tersimpan pada named volume agar persisten |

Konfigurasi (kredensial database, `APP_KEY`, dan lainnya) dikelola melalui `.env`; file `.env.example` disertakan di repositori.

## 8. Model Data

**Tabel `collection`**

| Field | Tipe | Keterangan |
|---|---|---|
| `id` | bigint unsigned, PK | Auto increment |
| `pokemon_id` | unsigned int, unique | ID Pokémon dari PokéAPI |
| `name` | varchar(100) | Nama Pokémon |
| `image_url` | varchar(255) | URL sprite |
| `types` | json | Daftar tipe |
| `nickname` | varchar(30), nullable | Maks. 30 karakter |
| `level` | unsigned tinyint | 1–100, default 1 |
| `notes` | varchar(200), nullable | Maks. 200 karakter |
| `created_at` | timestamp | Dikelola Eloquent |
| `updated_at` | timestamp | Dikelola Eloquent |

Didefinisikan melalui Laravel Migration dan diakses melalui Eloquent Model `CollectionItem`.

## 9. Spesifikasi API

### 9.1 Endpoint Backend Sendiri

Endpoint didefinisikan pada `routes/api.php` (Base URL: `/api`) sebagai kontrak REST backend. Antarmuka Livewire memanggil service/action yang sama dengan controller API, sehingga logika dan validasi tidak terduplikasi.

| Method | Endpoint | Deskripsi | Status sukses |
|---|---|---|---|
| GET | `/pokemon` | Daftar Pokémon (proxy PokéAPI) (Query params: `limit`, `offset`, `search`) | 200 |
| GET | `/pokemon/:nameOrId` | Detail Pokémon (proxy PokéAPI) | 200 |
| GET | `/collection` | Daftar koleksi (Query params: `search`, `sort_by`, `sort_dir`) | 200 |
| GET | `/collection/:id` | Detail satu entri koleksi | 200 |
| POST | `/collection` | Tambah Pokémon ke koleksi | 201 |
| PUT | `/collection/:id` | Ubah nickname, level, catatan | 200 |
| DELETE | `/collection/:id` | Hapus dari koleksi | 204 |

**Contoh: `POST /api/collection`**

Request:
```json
{
  "pokemon_id": 25,
  "nickname": "Sparky",
  "level": 12,
  "notes": "Starter"
}
```

Response `201`:
```json
{
  "id": 1,
  "pokemon_id": 25,
  "name": "pikachu",
  "image_url": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/25.png",
  "types": ["electric"],
  "nickname": "Sparky",
  "level": 12,
  "notes": "Starter",
  "created_at": "2026-09-29T10:00:00Z",
  "updated_at": "2026-09-29T10:00:00Z"
}
```

**Format error standar**
```json
{ "error": { "code": "VALIDATION_ERROR", "message": "Level harus antara 1 dan 100" } }
```

| Kode HTTP | Kasus |
|---|---|
| 400 | Input tidak valid |
| 404 | Pokémon atau entri koleksi tidak ditemukan |
| 409 | Pokémon sudah ada di koleksi |
| 502 | PokéAPI tidak dapat dijangkau |

### 9.2 Endpoint PokéAPI yang Dipakai

| Kegunaan | Endpoint |
|---|---|
| Daftar | `GET https://pokeapi.co/api/v2/pokemon?limit=20&offset=0` |
| Detail | `GET https://pokeapi.co/api/v2/pokemon/{id atau nama}` |

Field yang dipakai dari respons detail: `id`, `name`, `height`, `weight`, `types`, `abilities`, `stats`, `sprites.front_default`.

## 10. Alur Pengguna Utama

1. Pengguna membuka aplikasi dan melihat daftar Pokémon.
2. Pengguna mencari "pikachu" dan membuka halaman detailnya.
3. Pengguna menekan **Tambah ke Koleksi** dan mengisi nickname serta level.
4. Pokémon muncul di halaman **Koleksi Saya**.
5. Pengguna mengedit level atau catatan, lalu menyimpan.
6. Pengguna menghapus Pokémon yang tidak diinginkan setelah konfirmasi.

## 11. Kriteria Penerimaan

- [ ] Daftar dan detail Pokémon tampil dari PokéAPI dengan pagination dan pencarian.
- [ ] Pokémon dapat ditambahkan ke koleksi; duplikat ditolak dengan pesan jelas.
- [ ] Nickname, level, dan catatan dapat diubah dengan validasi yang benar.
- [ ] Entri koleksi dapat dihapus dengan konfirmasi.
- [ ] Data koleksi tetap ada setelah aplikasi di-restart (persisten).
- [ ] Error dari PokéAPI atau input tidak valid ditangani tanpa membuat aplikasi crash.
- [ ] Aplikasi dapat dijalankan dengan `docker compose up` (beserta perintah build aset frontend yang terdokumentasi) mengikuti README. Langkah migrasi dieksekusi otomatis oleh Docker, tanpa langkah manual tersembunyi.
- [ ] Data MySQL tetap ada setelah container di-restart (menggunakan volume).

## 12. Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| PokéAPI lambat atau tidak tersedia | Cache di backend, pesan error yang jelas, tombol coba lagi |
| Rate limiting / terlalu banyak request | Cache respons dan batasi pemanggilan berulang |
| Interpretasi "CRUD" berbeda dari harapan pemberi tugas | Asumsi ditulis eksplisit (bagian 3) dan dikonfirmasi bila memungkinkan |
| Konfigurasi Docker (port bentrok, izin file, MySQL belum siap saat app start) | Gunakan healthcheck pada service `db`, port dapat diatur via `.env`, dokumentasikan troubleshooting di README |
| Waktu pengerjaan terbatas | Prioritaskan kebutuhan **Must**; fitur **Could** dikerjakan jika waktu tersisa |

## 13. Rencana Pengerjaan

| Tahap | Cakupan |
|---|---|
| 1 | Setup Docker Compose (app, web, db), instalasi Laravel, Livewire, Tailwind, daisyUI, Alpine, migrasi database |
| 2 | Backend: service PokéAPI (HTTP Client + Cache), model dan migration, CRUD koleksi, validasi, error handling |
| 3 | UI: komponen Livewire untuk daftar, detail, koleksi, form tambah dan ubah, dialog hapus (Alpine.js), styling Tailwind dengan komponen daisyUI |
| 4 | Polishing: loading/empty state (wire:loading), responsif, feature test dasar dengan Pest/PHPUnit |
| 5 | Dokumentasi: README (termasuk panduan Docker), contoh endpoint, screenshot |

## 14. Pengembangan Selanjutnya

- Autentikasi dan koleksi per pengguna
- Filter berdasarkan tipe dan generasi
- Ekspor koleksi (CSV/JSON)
- Unit dan integration test yang lebih lengkap
- Deployment ke hosting dan CI/CD
