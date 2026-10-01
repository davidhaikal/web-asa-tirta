# ASA Tirta — Role & Feature Audit

> **Baseline:** `WORKFLOW_SUMMARY.md` (workflow 8 role dari `Skripsi.docx`).
> **Objek audit:** source code sistem web ASA Tirta di repository ini (stand: commit lokal, 2026-09-29).
> **Fokus:** *apa yang tertulis di skripsi* → *apa yang benar-benar ada di source code*.

## Metode & Batasan

1. **Analisis statis** menyeluruh: `routes/web.php` (281 baris, dibaca seluruhnya), semua controller (26 file), semua model (18), semua migration (22), view per modul, `layouts/app.blade.php` + `layouts/kasir.blade.php`, seeder, middleware, `composer.json`.
2. **Verifikasi HTTP** (2026-09-29, `127.0.0.1:8000`, `curl` + cookie jar, login `{role}@example.com`/`password`): status 200/302/500 per halaman untuk guest + 8 role, plus `laravel.log` untuk pesan error persis.
3. **Inspeksi database** (MySQL `web_asa_tirta`): tabel `migrations`, `show tables`, tabel `users`.
4. **Cek API/integrasi & komponen:** tidak ada `routes/api.php` (hanya `web.php` + `console.php`), tidak ada Blade components (`resources/views/components` tidak ada), dan tidak ada panggilan HTTP eksternal / mailer / gateway pembayaran di `app/` — sistem tidak punya integrasi API apa pun.

### ⚠️ Kondisi lingkungan — sudah disinkronkan 2026-09-29 (dipersetujui pengguna)

Saat audit awal (2026-09-29 pagi), dua kondisi lingkungan menyebabkan 500 yang bukan bug logika:

1. **6 migration belum di-apply di DB lokal** (DB berhenti di 2026-07-02; 16/22): `create_pembelians`, `create_pembelian_details`, `create_pembayaran_utangs`, `create_pelanggans`, `add_qty_to_gudang_tables` (kolom `qty`), `add_bukti_foto_to_pengiriman` (kolom `bukti_foto`).
2. **Package export belum terinstall di `vendor/`**: `barryvdh/laravel-dompdf` dan `maatwebsite/excel` **ter-deklarasikan di `composer.json`** (baris 10 & 13) tetapi tidak ada di vendor → semua endpoint export 500 (`Class ... not found`).

**Tindak lanjut (2026-09-29, setelah persetujuan):** `composer install` (74 paket, package discover OK) + `php artisan migrate --force` (6 migration, semuanya aditif) → DB sekarang **22/22 applied**.

**Verifikasi ulang (sweep HTTP kedua, 2026-09-29) — 12 halaman berubah 500 → 200:**
`/qc/export/excel`, `/qc/export/pdf`, `/gudang/export/pdf`, `/gudang/export/excel`, `/keuangan/dashboard`, `/keuangan/pelanggan`, `/pembelian`, `/pembelian/create`, `/keuangan/export/pdf`, `/keuangan/export/excel`, `/manajemen/laporan/export/pdf`, `/manajemen/laporan/export/excel`.

**Masih 500 (level kode, tidak terpengaruh sinkronisasi — sesuai prediksi audit):** semua halaman admin ber-layout (`stok.index`), `/stok`, `/invoice`, `/permintaan-uang`, `/laporan`, `/penjualan`, `/dashboard` (guest), `/keuangan/pembelian/tambah`, `/manajemen/laporan/filter`, `/manajemen/dashboard/export/{pdf,excel}`. Login `manajemen@example.com` masih gagal (user memang tidak ada di DB — seeder tidak dijalankan ulang, disengaja agar tidak mengubah data).

**Uji POST pasca-sinkronisasi:** `POST /gudang/barang-masuk/store` → **302 sukses** (stok 198→199, baris terisi `qty=1`; test data dibersihkan & stok dikembalikan). `POST /gudang/produk/store` → **masih 500** — `Unknown column 'kode_produk'`: **tidak ada migration `kode_produk` di seluruh codebase** (form & `Produk::create` memakainya) → ini gap level kode, bukan lingkungan.

### Legenda Status

- ✅ **SESUAI** — workflow didukung fitur yang benar-benar ada (diverifikasi).
- ⚠️ **SEBAGIAN** — hanya sebagian workflow yang tersedia, atau ada deviasi signifikan.
- ❌ **TIDAK ADA** — workflow disebutkan di skripsi tetapi implementasinya tidak ditemukan.
- 🔵 **TAMBAHAN** — fitur ada di sistem tetapi tidak tercantum dalam workflow skripsi.

### Ringkasan Status per Role

| # | Role | Status Keseluruhan |
|---|------|--------------------|
| 1 | Admin | ⚠️ (efektif rusak — semua halaman ber-layout 500) |
| 2 | Bagian Produksi | ❌ (role read-only; pencatatan produksi tidak bisa dilakukan role ini) |
| 3 | QC | ✅ (core workflow lengkap; export ⚠️) |
| 4 | Bagian Gudang | ✅ (core CRUD + stok lengkap; laporan & riwayat ⚠️/❌) |
| 5 | Kasir | ✅ (core POS + PO + laporan lengkap; invoice/SPJ deviasi) |
| 6 | Admin Keuangan | ⚠️ (dashboard/pelanggan 500 di DB lokal; laporan statis; aksi penagihan dummy) |
| 7 | Driver | ⚠️ (UI + alur status lengkap, tetapi tidak ada sumber data pengiriman) |
| 8 | Manajemen | ⚠️ (dashboard + laporan nyata; target produksi & laporan produksi ❌; user lokal tidak ada) |

---

## 1. Admin

### Workflow yang Diharapkan

1. Login (divalidasi hak akses).
2. Mengelola **data master dan pengguna** sistem.
3. Mengelola **data pelanggan** (input/edit/hapus).
4. Mengelola **data penjualan** (tambah/ubah/hapus: tanggal, pelanggan, produk, jumlah, total).
5. Menyusun **laporan penjualan**.
6. **Memverifikasi piutang & pendapatan** (kesesuaian uang masuk vs piutang).
7. **Membuat tagihan** untuk pelanggan ber-tunggakan.

### Fitur yang Ditemukan

- Role `admin` ada di seeder (`database/seeders/UserSeeder.php`) dan DB (`#2 admin@example.com`). Middleware `role:admin` aktif (`app/Http/Middleware/RoleMiddleware.php`, alias di `bootstrap/app.php`).
- Login admin → `redirect()->route('admin.dashboard')` (`app/Http/Controllers/Auth/LoginController.php:50`) → `GET /admin` adalah **closure yang mengembalikan string "Dashboard Admin"** (`routes/web.php:219-222`). Tidak ada dashboard admin sesungguhnya.
- **Kerusakan sistemik:** semua halaman yang `@extends('layouts.app')` dan dirender untuk role admin **500** — `resources/views/layouts/app.blade.php:129` memanggil `route('stok.index')` (blok Marketing, baris 125-132, dirender untuk `admin`/`marketing`). Nama `stok.index` **hilang** karena `GET /stok` didaftarkan dua kali: `routes/web.php:69` (bernama) dan `:120` (`StokController@riwayat`, menang). Konfirmasi log: `Route [stok.index] not defined. (View: .../layouts/app.blade.php)`. Hasil HTTP 2026-09-29 sebagai admin: `/dashboard`, `/gudang`, `/gudang/dashboard`, `/gudang/produk`, `/produk`, `/qc/dashboard`, `/keuangan/dashboard`, `/manajemen/dashboard`, `/driver/dashboard` → **semua 500**. Yang bisa dibuka admin hanya `/admin` (string), `/kasir/*` (layout kasir, 200), `/po` (kosong), `/`.
- **Pengelolaan pengguna: tidak ada.** Tidak ada controller CRUD user; hanya `register` publik (`routes/web.php:46-47`).
- **AdminController** (`app/Http/Controllers/AdminController.php`) = legacy: login session hardcoded `admin`/`admin123`, method `login()` merujuk view `admin.login` yang **tidak ada**; **tidak tertaut rute** (rute `/admin` tertimpa closure).
- Data master produk: ada (CRUD `ProdukController`) tetapi bukan milik admin khusus, dan halamannya 500 untuk admin.
- Data pelanggan: ada CRUD lengkap di `KeuanganController@pelanggan/store/update/destroy` (`routes/web.php:156-159`) — tetapi **500 di DB lokal** (tabel `pelanggans` belum di-migrate) dan 500 untuk admin (layout).
- Data penjualan: transaksi nyata hanya di modul kasir (buat/bayar/batal; **tidak ada edit**). Modul legacy `Route::resource('penjualan', ...)` (`routes/web.php:64`) rusak: `PenjualanController@index()` berisi logika *create* (bukan tampilkan), method `store/show/edit/update/destroy` **tidak ada**; `GET /penjualan` 500 (`SQLSTATE[23000]: Column 'total' cannot be null`).
- Laporan penjualan: ada di `/kasir/laporan-penjualan` (data nyata, 200 — bisa dibuka admin karena layout kasir).
- Piutang & pendapatan: `/keuangan/piutang` (data nyata, 200 untuk role keuangan) — tidak terjangkau admin (layout 500).
- Tagihan: modul penagihan (`PenagihanController`) — daftar data nyata, tetapi aksi "kirim/tagih" dummy.

### Mapping

| Workflow dari Skripsi | Fitur yang Ditemukan di Source Code | Status | Evidence |
|---|---|---|---|
| Kelola data master & pengguna | Tidak ada CRUD user/pengguna (hanya register publik); `AdminController` legacy & tidak tertaut rute | ❌ | `database/seeders/UserSeeder.php`; `app/Http/Controllers/AdminController.php`; `routes/web.php:46-47,219-222` |
| Kelola data pelanggan (input/edit/hapus) | CRUD lengkap di KeuanganController, tetapi 500 di DB lokal (tabel `pelanggans` belum migrate) & 500 untuk admin (layout) | ⚠️ | `app/Http/Controllers/KeuanganController.php:42-89`; `routes/web.php:156-159`; migration `2026_07_16_132215_create_pelanggans_table.php` |
| Kelola data penjualan (tambah/ubah/hapus) | Transaksi nyata hanya via kasir (buat→bayar/batal, tanpa edit); resource `/penjualan` legacy rusak total | ⚠️ | `app/Http/Controllers/KasirController.php:84-206`; `app/Http/Controllers/PenjualanController.php` |
| Laporan penjualan | Ada (data nyata, printable) — milik menu kasir, bukan halaman admin | ⚠️ | `app/Http/Controllers/KasirController.php:294-362`; `resources/views/kasir/laporan-penjualan.blade.php` |
| Verifikasi piutang & pendapatan | Data piutang nyata + ubah status; tidak ada fitur "verifikasi kesesuaian uang masuk" | ⚠️ | `app/Http/Controllers/KeuanganController.php:96-139` |
| Membuat tagihan pelanggan tunggakan | Daftar tagihan nyata (`penjualan` belum lunas); "kirim tagihan/tagih" hanya flash, tanpa efek (tanpa email/status) | ⚠️ | `app/Http/Controllers/PenagihanController.php:10-83` |
| Login → dashboard sesuai hak akses | Login OK, tetapi landing admin = string "Dashboard Admin"; semua halaman ber-layout 500 | ⚠️ | `routes/web.php:219-222`; `resources/views/layouts/app.blade.php:129` |

### Gap

1. **Role admin praktis tidak bisa dipakai** — satu akar (`stok.index` di `app.blade.php:129`) membuat ±10 halaman ber-layout 500.
2. Tidak ada fitur kelola pengguna/administrasi (inti tanggung jawab admin di skripsi).
3. Tidak ada halaman "data penjualan" milik admin (tidak ada edit/hapus transaksi di mana pun).
4. `AdminController` + view `admin/dashboard` adalah kode mati (legacy login hardcoded).

### Evidence Source Code

- `routes/web.php:50-52,64,69,70,71,120,156-159,219-222`
- `resources/views/layouts/app.blade.php:94,125-132`
- `app/Http/Controllers/AdminController.php` (48 baris, legacy)
- `app/Http/Controllers/Auth/LoginController.php:48-69`
- `resources/views/admin/dashboard.blade.php` (kode mati)
- `laravel.log` 2026-09-29: `Route [stok.index] not defined` (userId:2 = admin, 6×), `View [stok.index] not found`, `Target class [InvoiceController] does not exist`, `Target class [PermintaanUangController] does not exist`

---

## 2. Bagian Produksi

### Workflow yang Diharapkan

1. Perencanaan produksi berdasarkan **target manajemen**.
2. Proses pengolahan & pengemasan AMDK (aktivitas fisik, di luar sistem).
3. **Mencatat pelaksanaan produksi & hasil produksi ke sistem.**
4. Hasil produksi dicatat sebagai barang jadi.
5. Serah terima barang jadi ke gudang.
6. Diperiksa QC → yang lolos menambah stok.

### Fitur yang Ditemukan

- Role `produksi` ada di seeder & DB (`#5`); login → `redirect('/produksi')` (`LoginController.php:54`) → **bisa** (200).
- Satu-satunya halaman role ini: `modules.produksi` — dashboard ringkasan (total produksi, produksi hari ini, 8 terbaru) dari tabel `produksis` (`routes/web.php:234-242`; `resources/views/modules/produksi.blade.php`). **Data nyata.**
- Skema mendukung: tabel `produksis` (`produk_id, jumlah_produksi, tanggal_produksi, status enum[proses,qc,selesai]` — `2026_04_15_050000_create_produksis_table.php`) + `app/Models/Produksi.php`.
- **Tidak ada form/halaman pencatatan produksi untuk role produksi.** `ProduksiController@index()` rusak (variable mismatch `compact('produksi')` vs `$produk`, view `produk.index`) dan **tidak tertaut rute**; `update()` merujuk variabel tak terdefinisi (`$produk`) — kode mati.
- `POST /produksi` (`routes/web.php:210`) → `ProduksiController@store()` — tetapi method ini **membuat `Produk`** (katalog, stok=0), **bukan** record produksi (bug legacy).
- Satu-satunya `Produksi::create` di seluruh aplikasi ada di **`QcController@store`** (`app/Http/Controllers/QcController.php:101-106`, status `'selesai'`) — yaitu saat QC memeriksa produk baru. Status `proses` dan `qc` **tidak pernah diset** oleh kode mana pun.
- Tidak ada fitur "target produksi dari manajemen" (cek silang: tidak ada tabel/kolom/rute terkait target).

### Mapping

| Workflow dari Skripsi | Fitur yang Ditemukan di Source Code | Status | Evidence |
|---|---|---|---|
| Perencanaan berdasarkan target manajemen | Tidak ada fitur target produksi di mana pun | ❌ | (tidak ditemukan di routes/controllers/models) |
| Mencatat pelaksanaan & hasil produksi ke sistem | Tidak ada form pencatatan untuk role produksi; satu-satunya pencipta record `produksis` adalah QC; `POST /produksi` malah membuat produk katalog | ❌ | `app/Http/Controllers/ProduksiController.php:10-33,46-62`; `routes/web.php:210`; `app/Http/Controllers/QcController.php:100-106` |
| Hasil produksi → barang jadi → gudang | Tersirat lewat QC: `Layak` → `Produk.stok +=` + record `stoks` "Hasil produksi"; tidak ada alur serah-terima eksplisit | ⚠️ | `app/Http/Controllers/QcController.php:118-136` |
| Dipantau manajemen | Tabel `produksis` ada, tetapi laporan manajemen tidak menyertakan jenis "produksi" | ⚠️ | `app/Http/Controllers/ManajemenController.php:16-60` |

### Gap

1. Role produksi **read-only**: tidak bisa mencatat batch produksi (tanggung jawab utama di skripsi).
2. Pencatatan produksi hanya terjadi sebagai efek samping pemeriksaan QC — deviasi alur dari skripsi (produksi mencatat → QC memeriksa).
3. Kolom `status` (`proses`/`qc`/`selesai`) tidak pernah berjalan (hanya `selesai` diset, dan oleh QC).
4. Tidak ada input target produksi.

### Evidence Source Code

- `routes/web.php:210,234-242`
- `app/Http/Controllers/ProduksiController.php` (73 baris — index/update rusak, store salah tabel)
- `app/Http/Controllers/QcController.php:97-140`
- `database/migrations/2026_04_15_050000_create_produksis_table.php`
- `resources/views/modules/produksi.blade.php`
- HTTP 2026-09-29: `produksi /produksi => 200` (satu-satunya halaman role ini)

---

## 3. QC (Quality Control)

### Workflow yang Diharapkan

1. Login.
2. Memilih barang/produk yang akan diperiksa.
3. Melakukan pemeriksaan kualitas.
4. Menentukan status **layak / tidak layak**.
5. Lolos → **menambah stok inventory barang jadi**; tidak layak → dicatat **reject**.
6. Hasil tersimpan → **laporan QC**.

### Fitur yang Ditemukan

- Role `qc` ada di seeder & DB (`#4`); login → `/qc/dashboard` (200).
- `QcController` **lengkap dan berfungsi** (semua halaman 200, data nyata):
  - `index()` → dashboard: total QC, lolos, reject, hari ini, 6 terakhir (`qc.dashboard`).
  - `pemeriksaan()` → daftar `produksis` yang **belum punya QC** + daftar semua produk (`qc.pemeriksaan`).
  - `lolos()` / `reject()` → daftar per hasil (`qc.lolos`, `qc.reject`).
  - `laporan()` → filter rentang tanggal + rekap (`qc.laporan`).
  - `store()` → jika `produk_id` diberikan: buat `produksis` (status `selesai`) + `qcs`; jika `produksi_id`: pakai record yang ada. **`Layak` → `Produk.stok += jumlah_produksi` + record `stoks` (jenis `masuk`, keterangan "Hasil produksi")** — persis alur skripsi.
  - `cetak()` / `exportPdf()` (view `qc.cetak`, A4 landscape) / `exportExcel()` (`app/Exports/QcExport.php`).
- Tabel `qcs`: `hasil enum[Layak,'Tidak Layak']`, `jumlah_dicek` (default 0 — **tidak pernah diisi controller**), `tanggal_qc` (nullable — **tidak diisi**), `foto_reject` (kolom ada, **tidak ada fitur upload**).
- **Catatan keamanan:** seluruh rute `/qc/*` **tanpa middleware auth** (terbukti 200 tanpa login) — data QC & form pemeriksaan terbuka publik.
- Export 500 di lingkungan ini (package belum di vendor) — logika kode sudah benar.

### Mapping

| Workflow dari Skripsi | Fitur yang Ditemukan di Source Code | Status | Evidence |
|---|---|---|---|
| Memilih produk untuk diperiksa | `/qc/pemeriksaan` (produksi tanpa QC + semua produk) | ✅ | `app/Http/Controllers/QcController.php:37-43`; `resources/views/qc/pemeriksaan.blade.php` |
| Menentukan status layak/tidak layak | `POST /qc/store` dengan `hasil` Layak/Tidak Layak + keterangan | ✅ | `app/Http/Controllers/QcController.php:98-115`; `routes/web.php:148` |
| Lolos → menambah stok barang jadi | `Produk.stok +=` + `stoks` "Hasil produksi" | ✅ | `app/Http/Controllers/QcController.php:118-136` |
| Tidak layak → dicatat reject | Record `qcs` hasil `Tidak Layak` + halaman `/qc/reject`; foto reject tidak terimplementasi | ⚠️ | `app/Http/Controllers/QcController.php:55-61`; `database/migrations/2026_04_15_060000_create_qc_table.php:21` |
| Laporan QC | `/qc/laporan` (filter tanggal), `/qc/cetak`; export PDF/Excel ada tapi 500 (package) | ✅ (⚠️ export) | `app/Http/Controllers/QcController.php:64-87,143-171`; `app/Exports/QcExport.php` |
| Login | OK, role benar | ✅ | `LoginController.php:52`; HTTP: 302 → `/qc/dashboard` |

### Gap

1. Kolom `jumlah_dicek`, `tanggal_qc`, `foto_reject` tidak pernah diisi oleh kode (skema ada, fitur tidak).
2. Rute QC tanpa proteksi auth (deviasi dari "divalidasi sesuai hak akses").
3. Export PDF/Excel gagal di lingkungan ini (vendor) — bukan bug kode.

### Evidence Source Code

- `routes/web.php:139-149,211,224-232`
- `app/Http/Controllers/QcController.php` (173 baris, dibaca utuh)
- `app/Models/Qc.php`, `app/Models/Produksi.php`
- `resources/views/qc/{dashboard,pemeriksaan,lolos,reject,laporan,cetak}.blade.php`
- `laravel.log` 2026-09-29 06:38:42-43: `Class "Maatwebsite\Excel\Facades\Excel" not found` / `Class "Barryvdh\DomPDF\Facade\Pdf" not found` (QcController:146/159)
- HTTP 2026-09-29: semua halaman QC 200 (juga sebagai guest)

---

## 4. Bagian Gudang (Admin Gudang)

### Workflow yang Diharapkan

1. Login → Dashboard Gudang (kartu ringkasan, grafik aktivitas, panel stok menipis).
2. Kelola **data produk** (nama, kode, jumlah per kardus, stok, harga, status).
3. Catat **barang masuk** (termasuk dari produksi) → stok bertambah.
4. Catat **barang keluar** (kebutuhan penjualan) → stok berkurang.
5. Catat **barang rusak**.
6. Ajukan **permintaan stok**.
7. Lihat **laporan gudang**.
8. Stok ter-update real-time; data tidak lengkap → ditolak.

### Fitur yang Ditemukan

- Role `gudang` ada di seeder & DB (`#6`); login → `/gudang/dashboard` (200).
- **Dashboard** (`DashboardController@gudang`, `routes/web.php:79`): total produk, barang masuk/keluar/rusak, total stok, stok menipis (ambang **hardcode ≤ 10**), 5 aktivitas terbaru, grafik — **data nyata, 200**.
- **Data produk** (`ProdukController`): index (search + pagination), detail, store, edit, update, destroy — semua halaman 200. Skema `produks` punya `nama_produk, harga, stok` (+ `kode_produk`? **tidak** — tidak ada migration `kode_produk`; kolom hanya ditambah `qty` via `2026_07_16_114950`). `store()` menyimpan `nama_produk, kode_produk, qty, harga, stok` → **di DB lokal, POST gagal** (kolom `kode_produk`/`qty` belum ada karena migration belum di-apply). Tidak ada field "status produk" di skema.
- **Barang masuk** (`BarangMasukController`): CRUD + validasi + **stok `+=` otomatis**; tidak membuat record `stoks` (inkonsistensi kecil vs QC/kasir). Halaman 200; POST 500 di DB lokal (kolom `qty`).
- **Barang keluar** (`BarangKeluarController`): CRUD + **cek stok cukup** + stok `-=` otomatis; `update()`/`destroy()` tidak mengoreksi stok (bug). Halaman 200.
- **Barang rusak** (`BarangRusakController`): CRUD + cek stok + stok `-=` (store/update/destroy). Halaman 200.
- **Permintaan stok** (`PermintaanStokController`): CRUD, status awal `Menunggu` — **tidak ada workflow persetujuan** (siapa pun bisa edit status via form).
- **Laporan gudang**: menu sidebar menunjuk `/gudang/laporan` (`layouts/app.blade.php:143`) tetapi **rute tidak ada** (404). Export PDF/Excel ada (`GudangController@exportPdf` view `gudang.dashboard_pdf` + `GudangExport`) → **500 (package)**.
- **Riwayat stok**: `/stok` 500 — `StokController@riwayat()` merender `view('stok.index')` yang **tidak ada**, dan `StokController` **tidak punya `index()`**; rute `POST /stok/keluar` + `GET /stok/{jenis}` menunjuk method `keluar()`/`filter()` yang **tidak ada**.
- Update stok real-time: **ya** — berubah dari barang masuk/keluar/rusak, QC lolos, dan pembayaran kasir.
- `GudangController@dashboard()` (method lain, memakai kolom `stok_minimum` yang **tidak ada di skema**) **tidak tertaut rute** — kode mati.

### Mapping

| Workflow dari Skripsi | Fitur yang Ditemukan di Source Code | Status | Evidence |
|---|---|---|---|
| Dashboard Gudang (ringkasan + grafik + stok menipis) | Lengkap, data nyata, 200 | ✅ | `app/Http/Controllers/DashboardController.php:30-83`; `resources/views/gudang/dashboard.blade.php` |
| Kelola data produk (nama, kode, qty/kardus, stok, harga) | CRUD lengkap + search/pagination; "status produk" tidak ada; POST gagal di DB lokal (kolom `kode_produk`/`qty` belum migrate) | ⚠️ | `app/Http/Controllers/ProdukController.php`; `routes/web.php:80-87`; `2026_07_16_114950_add_qty_to_gudang_tables.php` |
| Barang masuk → stok bertambah | CRUD + stok `+=` (validasi menolak data tidak lengkap) | ✅ | `app/Http/Controllers/BarangMasukController.php:22-49` |
| Barang keluar → stok berkurang | CRUD + cek stok + stok `-=`; koreksi stok saat edit/hapus tidak ada (bug) | ✅ | `app/Http/Controllers/BarangKeluarController.php:27-63` |
| Barang rusak | CRUD + stok `-=` | ✅ | `app/Http/Controllers/BarangRusakController.php` |
| Permintaan stok | CRUD + status `Menunggu`; tanpa alur persetujuan/pemrosesan | ⚠️ | `app/Http/Controllers/PermintaanStokController.php` |
| Laporan gudang | Menu sidebar → `/gudang/laporan` **tidak ada rutenya** (404); export PDF/Excel ada tapi 500 (package) | ⚠️ | `layouts/app.blade.php:143`; `app/Http/Controllers/GudangController.php:94-109`; `routes/web.php:81-82` |
| Riwayat/kartu stok | `/stok` 500 (view + method `index()` tidak ada) | ❌ | `app/Http/Controllers/StokController.php:28-33`; `routes/web.php:69,120,119,121` |
| Update stok real-time | Berjalan (masuk/keluar/rusak/QC/kasir) | ✅ | berbagai controller di atas + `app/Models/Stok.php` |

### Gap

1. `/gudang/laporan` (menu ada, rute tidak) — laporan gudang tidak dapat diakses.
2. Riwayat stok (`/stok`) rusak total (view + method hilang).
3. Form "tambah" produk/barang gagal di DB lokal (migration qty belum di-apply) — di skema kode sudah benar.
4. Barang keluar/rusak: edit/hapus tidak mengoreksi stok.
5. Ambang stok menipis hardcode `<= 10` di `DashboardController` (bukan `stok_minimum`).
6. Rute gudang tanpa middleware auth (halaman terbuka tanpa login — diverifikasi 200 sebagai guest).

### Evidence Source Code

- `routes/web.php:74-121`
- `app/Http/Controllers/{DashboardController,ProdukController,BarangMasukController,BarangKeluarController,BarangRusakController,PermintaanStokController,GudangController,StokController}.php`
- `resources/views/gudang/*.blade.php` (14 view)
- `laravel.log` 2026-09-29 06:38:52-53: `Class "Barryvdh\DomPDF\Facade\Pdf" not found` / `Class "Maatwebsite\Excel\Facades\Excel" not found` (GudangController:101/108)
- HTTP 2026-09-29: semua halaman CRUD gudang 200 (role gudang & guest); export 500

---

## 5. Kasir

### Workflow yang Diharapkan

1. Login → Dashboard Kasir.
2. Membuat **PO** (produk, jumlah, tanggal butuh, catatan) + memantau status PO (kebutuhan produksi).
3. Transaksi penjualan: pilih pelanggan, produk, jumlah, metode pembayaran.
4. Total otomatis + tampil stok tersedia.
5. Validasi stok: **stok cukup → simpan, invoice dibuat, stok berkurang, nota dicetak**; stok kurang → batal.
6. Laporan penjualan & stok.
7. **Laporan SPJ bulanan**.

### Fitur yang Ditemukan

- Role `kasir` ada di seeder & DB (`#8`); login → `/kasir/dashboard` (200). Layout khusus `layouts/kasir.blade.php`. **Semua 5 halaman kasir 200** (diverifikasi ulang 2026-09-29; alur bisnis sudah diverifikasi end-to-end di sesi sebelumnya: buat→bayar→batal, PO→bayar, perubahan stok & `stoks` benar).
- **Dashboard** (`KasirController@dashboard`): 4 statistik harian, strip "perlu ditangani" (transaksi pending + PO menunggu, query read-only), grafik 7 hari (Chart.js 4.4.3), stok, form PO, tabel kebutuhan produksi, transaksi terbaru + aksi cepat.
- **Transaksi POS** (`/kasir/transaksi`): keranjang JS (tambah/hapus item, subtotal + total live, badge peringatan qty > stok), panel stok dengan pencarian, 10 transaksi terbaru + Konfirmasi Bayar/Batal (two-step confirm), seksi PO belum dibayar.
  - `storeTransaksi()` (`KasirController.php:84-144`): validasi `items` JSON, hitung total dari `Produk.harga`, buat `Penjualan` **status `pending`** + `DetailPenjualan`, kode `TRX{YmdHis}`. **Stok TIDAK dicek/dikurangi saat pembuat-an.**
  - `bayarTransaksi()` (`:146-191`): **cek stok saat pembayaran** (throw jika kurang), kurangi stok + record `stoks` "keluar", status → `lunas`, set `metode`.
  - `batalkanTransaksi()` (`:193-206`): status → `batal` (transaksi lunas tidak bisa dibatalkan).
- **Nota** (`/kasir/nota` + `/kasir/nota/{id}/cetak`): daftar nota + cetak (`resources/views/kasir/cetak-nota.blade.php`, auto-print).
- **PO** (`storePO` `:244+`, `bayarPO` `:208-242`): kode `PO{YmdHis}`, `bulan_produksi`, status awal `menunggu`; "dibayar" → stok `+=` + record `stoks` "PO dibayar" + status `selesai`. Dipantau di tabel "Kebutuhan Produksi" dashboard. **Status `disetujui`/`ditolak` (ada di skema `purchase_orders`) TIDAK PERNAH diset** oleh kode mana pun — tidak ada fitur approval.
- **Laporan penjualan** (`:294-362`): filter periode (hari/minggu/bulan), 4 kartu ringkasan, tabel + TOTAL, cetak. Data nyata, 200.
- **Laporan stok** (`:364+`): 3 statistik, tabel stok + badge AMAN >50 / MENIPIS >10 / KRITIS ≤10, kartu stok per produk. Data nyata, 200.
- **Invoice: TIDAK ADA.** Tidak ada `Invoice::create` di seluruh aplikasi; tabel `invoices` kosong hanya `id + timestamps` (`2026_04_15_070000`). Dokumen yang dihasilkan sistem = **nota** (penjualan + `cetak-nota`).
- **SPJ: tidak ditemukan** (tidak ada controller/rute/view terkait SPJ).
- Pelanggan di form kasir = **field teks bebas** (default "Walk-in Customer") — tidak terhubung ke tabel `pelanggans`.
- Catatan keamanan: rute `/kasir/*` tanpa middleware auth (200 sebagai guest — data transaksi terbuka).

### Mapping

| Workflow dari Skripsi | Fitur yang Ditemukan di Source Code | Status | Evidence |
|---|---|---|---|
| Dashboard Kasir | Lengkap (statistik, grafik, stok, PO, transaksi terbaru) | ✅ | `app/Http/Controllers/KasirController.php:20-66`; `resources/views/kasir/dashboard.blade.php` |
| Buat PO + pantau status | Buat PO OK; pantau = tabel kebutuhan produksi; **tanpa approval (disetujui/ditolak tak terpakai)**; "bayar PO" = tambah stok | ⚠️ | `KasirController.php:208-268`; `2026_07_02_200000_add_penjualan_detail_and_po_tables.php:31-40` |
| Transaksi penjualan (pelanggan, produk, jumlah, metode) | POS lengkap; pelanggan = teks bebas (bukan master pelanggan) | ✅ | `KasirController.php:84-144`; `resources/views/kasir/transaksi.blade.php` |
| Total otomatis + stok tersedia | Keranjang JS live + panel stok + warning | ✅ | `resources/views/kasir/transaksi.blade.php` |
| Validasi stok → simpan / stok kurang → batal | Validasi terjadi **saat konfirmasi bayar** (bukan saat simpan): stok cukup → lunas + stok berkurang; kurang → error, transaksi tetap `pending` | ⚠️ | `KasirController.php:156-161` (deviasi dari urutan skripsi) |
| Invoice dibuat | Tidak ada record invoice; dokumen = nota penjualan | ⚠️ | (grep `Invoice::create` → 0 hasil); `resources/views/kasir/cetak-nota.blade.php` |
| Nota dicetak | Cetak nota per transaksi (auto-print) | ✅ | `KasirController.php:287-292`; `routes/web.php:133` |
| Laporan penjualan & stok | Kedua laporan data nyata + cetak | ✅ | `KasirController.php:294-420`; `resources/views/kasir/laporan-*.blade.php` |
| Laporan SPJ bulanan | Tidak ditemukan di mana pun | ❌ | (tidak ada rute/controller/view "SPJ") |

### Gap

1. **SPJ bulanan tidak diimplementasi.**
2. **Invoice tidak diimplementasi** (tabel ada, kosong, tak terpakai) — skripsi menyebut "sistem membuat invoice".
3. PO tidak punya alur persetujuan (hanya `menunggu → selesai`).
4. Urutan validasi stok berbeda dari skripsi (dicek saat bayar, bukan saat simpan); transaksi `pending` bisa menumpuk tanpa batas waktu.
5. Rute kasir tanpa auth (terbuka publik — diverifikasi).

### Evidence Source Code

- `routes/web.php:124-135`
- `app/Http/Controllers/KasirController.php` (388 baris, dibaca utuh)
- `resources/views/kasir/*.blade.php` (7 view; `index.blade.php` mati — `KasirController@index` hanya redirect)
- `resources/views/layouts/kasir.blade.php`
- `database/migrations/2026_04_15_070000_create_invoice_table.php` (kosong)
- HTTP 2026-09-29: 5 halaman kasir 200 (role kasir & guest)

---

## 6. Admin Keuangan

### Workflow yang Diharapkan

1. Login → Dashboard Keuangan (total pendapatan, transaksi lunas, total piutang, tagihan menunggu; grafik arus kas).
2. Kelola **data pelanggan**.
3. Kelola **piutang**: cari, saring per status & jatuh tempo, detail tagihan, **kirim tagihan**, ubah status pembayaran.
4. Kelola **tagihan** customer.
5. Lihat **laporan keuangan/penjualan**.

### Fitur yang Ditemukan

- Role `keuangan` ada di seeder & DB (`#7`); login → `/keuangan/dashboard`. Satu-satunya grup rute dengan middleware `auth` (`routes/web.php:152`) — tetapi **tanpa cek role** (siapa pun yang login boleh masuk).
- **Dashboard** (`KeuanganController@index`): pendapatan hari ini, jumlah lunas, total piutang (`penjualan` pending), tagihan menunggu (dari `Pembelian`), grafik 7 hari pendapatan & pengeluaran — **kode data nyata**, tetapi **500 di DB lokal** karena kueri `Pembelian` (tabel `pembelians` belum migrate).
- **Data pelanggan** (`pelanggan/store/update/destroy` `:42-89`): CRUD lengkap + statistik piutang per status — **500 di DB lokal** (tabel `pelanggans` belum migrate). Di kode/skema sudah benar.
- **Piutang** (`:96-139`): total piutang, jumlah pelanggan belum bayar/lunas, daftar `penjualan` pending, grafik 6 bulan, **edit** (pelanggan/total/tanggal/status) dan **hapus** — data nyata, **200**. **Tidak ada pencarian/penyaringan per status & jatuh tempo** (hanya daftar pending).
- **Tagihan/penagihan** (`PenagihanController`, `routes/web.php:172-180`):
  - `index()` → daftar `penjualan` belum lunas — data nyata, 200.
  - `show($id)` → detail (200).
  - `formKirim()` → form "kirim tagihan" berisi **data dummy hardcoded** (PT Maju Jaya, PT Sumber Rejeki) — bukan data riil.
  - `prosesKirim()` / `kirimSemua()` / `kirim()` / `tagih()` → **hanya redirect + flash "berhasil"**, tanpa efek apa pun (tanpa email, tanpa ubah status, tanpa log).
- **Laporan** (`/keuangan/laporan`): view **statis hardcode** ("Rp 250 JT", "520 transaksi", dst.) — `KeuanganController@laporan()` (`:91-94`) tidak mengirim data sama sekali.
- **Export** (`exportPdf` `:173-178`, `exportExcel` `:180-183`): memakai **`getDummyData()` hardcoded** + package belum di vendor → 500.
- **Pembelian (supplier)** — 🔵 tidak ada di skripsi: `PembelianController` CRUD lengkap + view `keuangan.pembelian` & `keuangan.pembelian_tambah`; tetapi view `pembelian_show`/`pembelian_edit` **tidak ada** (show/edit 500), tabel belum di DB lokal (500), dan `Pembelian` model punya relasi `pembayaranUtang()` → `PembayaranUtang::class` yang **modelnya tidak ada** (ledakan jika relasi dipanggil; tidak ada view yang memanggilnya saat ini).
- Rute `/keuangan/pembelian/tambah` → `KeuanganController@tambahPembelian` — **method tidak ada** (500).
- Duplicasi nama rute: `keuangan.penagihan` dipakai 2× (`web.php:167` & `:172`); resource `pembelian` didaftarkan 2× (`:170` & `:171`); `/keuangan/export/*` didaftarkan 2× (`:153-154` & `:164-165`).

### Mapping

| Workflow dari Skripsi | Fitur yang Ditemukan di Source Code | Status | Evidence |
|---|---|---|---|
| Dashboard Keuangan (ringkasan + grafik arus kas) | Kode data nyata; **500 di DB lokal** (kueri `pembelians`) | ⚠️ | `app/Http/Controllers/KeuanganController.php:9-40`; `laravel.log` 06:38:29 (`Table 'web_asa_tirta.pembelians' doesn't exist`) |
| Kelola data pelanggan | CRUD lengkap; **500 di DB lokal** (`pelanggans` belum migrate) | ⚠️ | `KeuanganController.php:42-89`; `routes/web.php:156-159` |
| Piutang: cari & saring per status/jatuh tempo | Daftar piutang nyata + grafik 6 bulan; **tanpa pencarian/filter status & jatuh tempo** | ⚠️ | `KeuanganController.php:96-119` |
| Piutang: detail, kirim tagihan, ubah status | Detail & ubah status OK; **"kirim tagihan" = dummy flash** (formKirim berisi data hardcoded) | ⚠️ | `KeuanganController.php:121-131`; `app/Http/Controllers/PenagihanController.php:25-83` |
| Kelola tagihan customer | Daftar tagihan nyata (belum lunas); aksi kirim/tagih tanpa efek | ⚠️ | `PenagihanController.php:10-22,60-83`; `routes/web.php:172-180` |
| Laporan keuangan/penjualan | Halaman ada tapi **statis hardcode**; export **dummy data** + 500 (package) | ⚠️ | `KeuanganController.php:91-94,165-183`; `resources/views/keuangan/laporan.blade.php:1-40` |
| Login + proteksi hak akses | Grup keuangan = satu-satunya grup ber-`auth` — tetapi **tanpa cek role** | ⚠️ | `routes/web.php:152` |

### Gap

1. Laporan keuangan tidak berisi data riil (statis) — export juga dummy.
2. "Kirim tagihan" tidak benar-benar mengirim (dummy); tidak ada ubah status dari penagihan.
3. Filter/saring piutang per status & jatuh tempo tidak ada.
4. Dashboard & pelanggan bergantung tabel yang belum ada di DB lokal (migrasi tertinggal) — 500.
5. Modul pembelian setengah jadi (view show/edit hilang, model relasi `PembayaranUtang` tidak ada) — meskipun ini fitur tambahan di luar skripsi.

### Evidence Source Code

- `routes/web.php:152-180`
- `app/Http/Controllers/{KeuanganController,PenagihanController,PembelianController}.php`
- `app/Models/{Pembelian,PembelianDetail,Pelanggan,Penjualan}.php`
- `resources/views/keuangan/{dashboard,pelanggan,piutang,penagihan,penagihan_detail,penagihan_kirim,laporan,laporan_pdf,pembelian,pembelian_tambah}.blade.php`
- `app/Exports/KeuanganExport.php`
- `laravel.log` 2026-09-29 06:38:58 - 06:39:08 (6× `pembelians`/`pelanggans` doesn't exist; `Call to undefined method ...tambahPembelian()`)

---

## 7. Driver

### Workflow yang Diharapkan

1. Login.
2. **Menerima invoice** (prasyarat: invoice telah diterima) — detail pengiriman menampilkan no invoice, pelanggan, daftar produk, status.
3. Mengirim barang kepada pelanggan.
4. **Mengunggah bukti pengiriman** setelah diterima.
5. Status pengiriman diperbarui (selesai setelah bukti terunggah).

### Fitur yang Ditemukan

- Role `driver` ada di seeder & DB (`#9 driver@example.com`, plus `#1 test@example.com` juga role driver); login → `/driver/dashboard` (200).
- `DriverController` **lengkap secara UI & alur status**:
  - `dashboard()` — statistik (pengiriman hari ini, sedang dikirim, selesai hari ini, menunggu konfirmasi) + 5 terbaru, data nyata dari `Pengiriman`.
  - `pengiriman($id?)` — daftar (tab Semua/Hari Ini/Dikirim/Selesai + search) & mode detail (pelanggan, daftar produk via `penjualan.detailPenjualans.produk`, status).
  - Alur status **`baru → siap` (Terima) `→ berangkat` (Mulai) `→ sampai` (Upload Bukti: foto → disk `public`) `→ selesai`**, dengan validasi urutan ketat (`majukanStatus()`).
  - Konstanta label status `baru/siap/berangkat/sampai/selesai`.
- **MASALAH INTI: tidak ada kode yang membuat record `Pengiriman`.** Grep `Pengiriman::create` di seluruh `app/` → **0 hasil**. Tabel `pengiriman` (FK `penjualan_id`, `tanggal_kirim`, `status`, `bukti_foto`) hanya bisa terisi manual via DB. Akibat: daftar driver **selalu kosong**, "menerima invoice" tidak pernah terjadi (tidak ada mekanisme kasir/admin membuat pengiriman dari penjualan/invoice).
- "Invoice" di skripsi ≠ tabel `invoices` (kosong) — relasi yang dipakai sistem: `Pengiriman → Penjualan` (bukan Invoice).
- Kolom `bukti_foto`: migration `2026_07_16_131058` **belum di-apply di DB lokal** → `uploadBukti()` akan 500 di lingkungan ini (kode upload ke `Storage::disk('public')` sudah benar; butuh `storage:link` agar foto tampil).
- Rute `/driver/*` **tanpa auth** (200 sebagai guest — diverifikasi).
- Grup `/driver/` (`routes/web.php:273-281`) → `modules.driver` (count `Pengiriman`) 200.
- Catatan log lama (2026-09-23): `View [driver.invoice.index] not found` / `driver.pengiriman.index` — sisa controller lama (`DriverDashboardController`/`DriverPengirimanController`) yang masih di-`use` di `web.php:27-28` **namun file-nya tidak ada dan tidak dipakai rute mana pun**.

### Mapping

| Workflow dari Skripsi | Fitur yang Ditemukan di Source Code | Status | Evidence |
|---|---|---|---|
| Menerima invoice | Tidak ada fitur penciptaan pengiriman/invoice→driver; daftar selalu kosong | ❌ | (grep `Pengiriman::create` → 0 hasil); `database/migrations/2026_04_15_040000_create_pengiriman_table.php` |
| Detail pengiriman (no invoice, pelanggan, produk, status) | Halaman detail lengkap (no. transaksi, pelanggan, produk, status) — berfungsi jika ada data | ⚠️ | `app/Http/Controllers/DriverController.php:43-100`; `resources/views/driver/pengiriman.blade.php` |
| Melakukan pengiriman | Status `berangkat` (Mulai) tersedia | ⚠️ | `DriverController.php` (method `mulai`) |
| Unggah bukti pengiriman | `uploadBukti()` (foto → disk public) lengkap; 500 di DB lokal (kolom `bukti_foto` belum migrate) | ⚠️ | `DriverController.php` (method `uploadBukti`); `routes/web.php:199`; `2026_07_16_131058_add_bukti_foto_to_pengiriman_table.php` |
| Status ter-update (selesai setelah bukti) | Alur status lengkap + validasi urutan | ✅ (logika) | `DriverController.php` (konstanta `LABEL_STATUS`, `majukanStatus`) |
| Login | OK (2 user driver di DB) | ✅ | `LoginController.php:59-60`; `users` #1 & #9 |

### Gap

1. **Tidak ada sumber data**: tidak ada fitur apa pun yang membuat `Pengiriman` dari penjualan — seluruh modul driver mati tanpa data.
2. Tidak ada konsep "menerima invoice" (skripsi): tidak ada invoice, tidak ada penugasan ke driver.
3. Upload bukti 500 di DB lokal (migration tertinggal).
4. Rute driver tanpa auth (terbuka publik).
5. Import mati di `web.php:27-28` (`DriverDashboardController`, `DriverPengirimanController` tidak ada).

### Evidence Source Code

- `routes/web.php:195-200,273-281`
- `app/Http/Controllers/DriverController.php` (136 baris, dibaca utuh)
- `app/Models/Pengiriman.php`, `app/Models/Penjualan.php:26-29` (relasi `hasOne(Pengiriman)`)
- `resources/views/driver/{dashboard,pengiriman}.blade.php`, `resources/views/pengiriman/status.blade.php`
- `laravel.log` 2026-09-23: `View [driver.invoice.index] not found`
- HTTP 2026-09-29: `/driver/dashboard`, `/driver/pengiriman` 200 (role driver & guest)

---

## 8. Manajemen

### Workflow yang Diharapkan

1. Dashboard (pusat informasi utama; visualisasi performa keseluruhan).
2. **Menetapkan target produksi** untuk bagian produksi.
3. Memantau **laporan produksi, persediaan, dan penjualan**.
4. Pengawasan & evaluasi proses bisnis.
5. Dasar pengambilan keputusan (informasi akurat & tepat waktu).

### Fitur yang Ditemukan

- Role `manajemen` ada di seeder (`UserSeeder.php`) **tetapi `manajemen@example.com` TIDAK ADA di DB lokal** → login gagal (302 kembali ke `/login` — diverifikasi). Role yang ada di DB justru termasuk `marketing@example.com` (bukan dari skripsi).
- `ManajemenController`:
  - `dashboard()` — ringkasan 4 jenis gerakan (Barang Masuk, Barang Keluar, Barang Rusak, Penjualan) + filter periode (hari ini/bulan ini/tahun ini/semua) + 6 aktivitas terbaru — **data nyata**, 200.
  - `laporan()` — filter jenis + search + produk + satuan + rentang tanggal + pagination — **data nyata**, 200.
  - `exportPdf()` / `exportExcel()` — data nyata (sudah disaring), memakai `LaporanManajemenExport` & view `manajemen.laporan_pdf` — **500 (package belum di vendor)**.
  - Method yang **tidak ada** namun dirute-kan: `filter()` (`web.php:189` → 500), `dashboardPdf()` (`:186` → 500), `dashboardExcel()` (`:187` → 500).
- **Jenis laporan hanya 4: masuk/keluar/rusak/penjualan. TIDAK ADA jenis "Produksi"** — tabel `produksis` tidak dipakai modul manajemen, sehingga "memantau laporan produksi" tidak terdukung.
- **Tidak ada fitur menetapkan target produksi** di seluruh aplikasi (cek silang routes/controllers/models — tidak ditemukan).
- Rute `/manajemen/*` **tanpa auth** (200 sebagai guest — diverifikasi).
- Sidebar manajemen (`layouts/app.blade.php:174-178`) hanya 2 menu: Dashboard + Laporan.

### Mapping

| Workflow dari Skripsi | Fitur yang Ditemukan di Source Code | Status | Evidence |
|---|---|---|---|
| Dashboard monitoring (visualisasi performa) | Dashboard 4 jenis gerakan + filter periode + aktivitas terbaru (data nyata) | ✅ | `app/Http/Controllers/ManajemenController.php`; `resources/views/manajemen/dashboard.blade.php` |
| Menetapkan target produksi | Tidak ditemukan di mana pun | ❌ | (tidak ada tabel/rute/controller "target") |
| Memantau laporan produksi | **Tidak ada jenis laporan "produksi"** (hanya masuk/keluar/rusak/penjualan) | ❌ | `ManajemenController.php:16-60` (fungsi `getLaporanData`) |
| Memantau laporan persediaan | Ada (jenis masuk/keluar/rusak) | ✅ | `ManajemenController.php` |
| Memantau laporan penjualan | Ada (jenis "penjualan" dari `DetailPenjualan`) | ✅ | `ManajemenController.php` |
| Export laporan/dashboard | 2 rute 500 (method tak ada: `dashboardPdf`, `dashboardExcel`), 2 rute 500 (package), `laporan/filter` 500 (method tak ada) | ⚠️ | `routes/web.php:185-191`; `laravel.log` 06:39:13-17 |
| Login sesuai hak akses | Role di seeder & middleware, tetapi **user tidak ada di DB lokal** → login gagal | ⚠️ | `database/seeders/UserSeeder.php:21`; tabel `users` (9 baris, tanpa manajemen) |

### Gap

1. **Target produksi** — tanggung jawab khas manajemen di skripsi — tidak diimplementasi.
2. **Laporan produksi** tidak masuk ke dashboard/laporan manajemen.
3. 3 dari 6 rute manajemen 500 (method controller tidak ada), 2 lagi 500 karena package.
4. User `manajemen@example.com` tidak ada di DB lokal (seeder belum dijalankan ulang).
5. Rute manajemen tanpa auth (terbuka publik).

### Evidence Source Code

- `routes/web.php:185-191`
- `app/Http/Controllers/ManajemenController.php` (290 baris)
- `app/Exports/LaporanManajemenExport.php`
- `resources/views/manajemen/{dashboard,laporan,laporan_pdf}.blade.php`
- `database/seeders/UserSeeder.php:13-22` vs tabel `users` aktual
- `laravel.log` 2026-09-29 06:39:13-17: `Call to undefined method ManajemenController::filter()/dashboardPdf()/dashboardExcel()` + 2× class export not found
- HTTP 2026-09-29: `manajemen login => 302|/login`; `/manajemen/dashboard` & `/manajemen/laporan` 200 (juga guest); 5 rute lain 500

---

# Overall Findings

## Role yang Sudah Sesuai

| Role | Keterangan |
|---|---|
| **QC** ✅ | Satu-satunya role yang alurnya berjalan persis seperti skripsi: pilih produk → tetapkan Layak/Tidak Layak → lolos menambah stok + record `stoks` → laporan QC. Semua halaman 200 dengan data nyata. (Catatan: export 500 karena package di vendor; rute tanpa auth; kolom `jumlah_dicek`/`tanggal_qc`/`foto_reject` tak terpakai.) |
| **Kasir** ✅ (dengan deviasi) | POS lengkap dan terverifikasi end-to-end (buat → bayar → batal, PO → bayar, stok & `stoks` benar), dashboard, nota, laporan penjualan & stok. Deviasi: invoice = nota (bukan tabel `invoices`), stok dicek/dikurangi saat **bayar** (bukan saat simpan), SPJ tidak ada, PO tanpa approval. |
| **Bagian Gudang** ✅ (dengan catatan) | Dashboard + CRUD produk/barang masuk/keluar/rusak/permintaan stok semuanya 200 dengan logika stok real-time yang benar (validasi, cek stok cukup, auto +/-). Catatan: `/gudang/laporan` 404 (menu tanpa rute), riwayat stok `/stok` 500, form "tambah" gagal di DB lokal (migration `qty`/`kode_produk` belum di-apply). |

## Role yang Sebagian Sesuai

| Role | Yang Ada | Yang Hilang/Rusak |
|---|---|---|
| **Admin** ⚠️ | Login OK; bisa melihat data kasir (layout kasir 200) | Semua halaman ber-layout 500 (`stok.index`); **tidak ada kelola pengguna/master**; tidak ada CRUD penjualan milik admin; `AdminController` legacy mati |
| **Admin Keuangan** ⚠️ | Piutang (data nyata + ubah status, 200), daftar tagihan nyata, struktur dashboard lengkap | Dashboard & pelanggan 500 di DB lokal (tabel belum migrate); laporan **statis**; "kirim tagihan" dummy; export dummy + 500; tanpa filter status/jatuh tempo |
| **Driver** ⚠️ | UI lengkap: dashboard, daftar+detail, alur status `baru→siap→berangkat→sampai→selesai`, upload bukti | **Tidak ada penciptaan `Pengiriman`** → modul selalu kosong; tidak ada "menerima invoice"; upload 500 di DB lokal (`bukti_foto`) |
| **Manajemen** ⚠️ | Dashboard + laporan 4 jenis data nyata (200) | **Target produksi ❌, laporan produksi ❌**; 5 rute 500 (3 method tak ada, 2 package); user tidak ada di DB lokal |

> **Bagian Produksi** tidak masuk tabel ini karena status keseluruhannya **❌ TIDAK ADA** (bukan sebagian): satu-satunya yang ada adalah dashboard read-only `modules.produksi` (200); pencatatan produksi — tanggung jawab utamanya — tidak bisa dilakukan oleh role ini (satu-satunya pencatat `produksis` adalah QC; `POST /produksi` malah membuat produk katalog; tidak ada target produksi).

## Workflow yang Belum Terimplementasi

1. **Kelola data master & pengguna** (Admin) — tidak ada CRUD user; hanya register publik.
2. **Pencatatan produksi oleh role Produksi** — tidak ada form; record `produksis` hanya tercipta lewat QC.
3. **Perencanaan/target produksi** (Manajemen → Produksi) — tidak ada di seluruh aplikasi.
4. **Laporan produksi** di dashboard/laporan Manajemen — jenis laporan hanya masuk/keluar/rusak/penjualan.
5. **Laporan SPJ bulanan** (Kasir) — tidak ada.
6. **Pembuatan invoice** (Kasir) — tabel `invoices` kosong & tak terpakai; dokumen nyata = nota.
7. **Penciptaan pengiriman / driver "menerima invoice"** (Driver) — tidak ada `Pengiriman::create` di mana pun.
8. **Approval PO** (`disetujui`/`ditolak` ada di skema) — tidak pernah diset oleh kode.
9. **Laporan keuangan ber-data-riil** (Admin Keuangan) — halaman statis hardcode; export dummy.
10. **Kirim tagihan yang benar-benar mengirim** (Penagihan) — hanya flash sukses tanpa efek (tanpa email/status/log).
11. **Laporan gudang** (`/gudang/laporan`) — menu ada, rute tidak ada.
12. **Riwayat stok** (`/stok`) — view + method controller tidak ada.

## Fitur Tambahan di Sistem (tidak ada di workflow skripsi)

| Fitur | Lokasi | Catatan |
|---|---|---|
| 🔵 Modul **Pembelian (supplier)** | `PembelianController`, `keuangan.pembelian*`, tabel `pembelians`/`pembelian_details` | CRUD kode ada tapi setengah jadi: view show/edit hilang, 500 di DB lokal, relasi `PembayaranUtang` model-nya tidak ada |
| 🔵 **Pembayaran utang** (skema saja) | `2026_07_06_114715_create_pembayaran_utangs_table.php` | Tabel di kode, belum di DB lokal, tidak ada model/controller |
| 🔵 Role **Marketing** + dashboard | `LoginController.php:61-62`, `DashboardController@index`, `resources/views/marketing/dashboard.blade.php`, user `marketing@example.com` di DB | Role ke-9 yang tidak ada di skripsi; halaman 500 untuk owner-nya sendiri (layout `stok.index`); `MarketingController` (data dummy) tidak tertaut rute |
| 🔵 Halaman `/po` (marketing) | `PoController` (stub kosong) | 200 dengan body kosong |
| 🔵 Modul `/penjualan` legacy | `Route::resource('penjualan', ...)` + `PenjualanController` | Rusak (index=store, method lain hilang, tanpa view); `GET /penjualan` 500 |
| 🔵 Halaman `/laporan` | `LaporanController` | 500 (`Penjualan`/`Stok` tidak di-import) |
| 🔵 Halaman `/pengiriman/status` | `routes/web.php:102` | View statis |
| 🔵 **Register publik** | `routes/web.php:46-47` | Siapa pun bisa membuat akun (tanpa verifikasi/role) |
| 🔵 Rute `/test` | `routes/web.php:205-207` | "Sistem berjalan" — sisa pengembangan |
| 🔵 `/permintaan-uang` (menu) | `routes/web.php:71` + menu `app.blade.php:131` | Controller tidak ada → 500; `DashboardController` menaruh placeholder `permintaanUang = 0` ("model belum ada") |

## Gap Utama

**A. Arsitektur & rute**
1. **Role admin lumpuh** — akar tunggal: `route('stok.index')` di `layouts/app.blade.php:129` + duplikasi `GET /stok` (`web.php:69` vs `:120`) yang menghapus nama rute. Menular ke semua halaman ber-layout untuk admin (±10 halaman 500) — ini gap terbesar untuk "kesesuaian skripsi vs sistem".
2. **Rute → method/controller/view yang tidak ada** (semua 500): `/invoice`, `/permintaan-uang` (controller tak ada, tidak di-import), `/laporan` (import model tak ada), `/stok` (view tak ada), `/stok/keluar`, `/stok/{jenis}` (method tak ada), `/keuangan/pembelian/tambah` (method tak ada), `/manajemen/laporan/filter`, `/manajemen/dashboard/export/{pdf,excel}` (method tak ada), resource `penjualan` (5 method hilang), `pembelian` show/edit (view hilang).
3. **Menu sidebar tanpa rute**: `/gudang/laporan` (`app.blade.php:143`) → 404.
4. **Nama rute bentrok**: `keuangan.penagihan` (2×), resource `pembelian` (2×), `/keuangan/export/*` (2×), `GET /admin` (3×), `GET /gudang` (3×), `GET /dashboard` (2×) — pemenang = yang terakhir terdaftar; nama dari yang pertama hilang.
5. **Import mati** di `web.php`: `AuthController` (baris 7), `DriverDashboardController` (baris 27), `DriverPengirimanController` (baris 28) — file tidak ada. Tidak mematahkan runtime (class hanya jadi string saat registrasi rute dan tak pernah dipakai rute), tetapi `php artisan route:list` tetap crash saat me-refleksi `InvoiceController` (baris 70, class tak ada).

**B. Keamanan**
6. **Sebagian besar rute tanpa proteksi auth/role** — diverifikasi: guest dapat membuka `/gudang/dashboard`, `/gudang/produk`, `/qc/dashboard`, `/qc/pemeriksaan`, `/manajemen/dashboard`, `/driver/dashboard`, `/kasir/dashboard`, `/pengiriman/status` (semua 200). Hanya grup `/keuangan/*` (`auth` saja, tanpa cek role) dan 7 grup prefix role (`auth` + `role:`) yang terlindungi.
7. Register publik tanpa batasan role.
8. `/dashboard` (marketing) tanpa auth tetapi memanggil `Auth::user()->name` → 500 untuk guest.

**C. Data & lingkungan**
9. **DB lokal tertinggal 6 migration** (2026-07-06 & 2026-07-16) → modul Pembelian & Pelanggan 500; form tambah Produk/Barang 500; upload bukti driver 500. **RESOLVED 2026-09-29** (migrate dijalankan, DB 22/22; 12 halaman flip 500→200). **Sisa gap level kode:** `POST /gudang/produk/store` tetap 500 karena kolom `kode_produk` tidak punya migration di codebase.
10. **Package export belum di-install di vendor** (`barryvdh/laravel-dompdf`, `maatwebsite/excel` — ter-deklarasikan di `composer.json`) → 10 endpoint export 500. **RESOLVED 2026-09-29** (`composer install`; semua export 200).
11. Seeder belum sinkron dengan DB: user `manajemen@example.com` tidak ada; user `marketing` & `test` ada (tidak sesuai daftar 8 role).
12. **Data dummy/statis** menempel di fitur: `keuangan.laporan` (angka hardcode), `PenagihanController@formKirim` (pelanggan hardcode), `KeuanganController@getDummyData` (export), `MarketingController` (dashboard dummy, tak tertaut rute).
13. `invoices` = tabel kosong tanpa satu pun writer; `pembayaran_utangs` = skema tanpa model/controller; kolom `jumlah_dicek`, `tanggal_qc`, `foto_reject`, `stok_minimum` (yang dirujuk kode mati) tidak terpakai.

**D. Alur bisnis**
14. Rantai **Penjualan → Pengiriman → Driver** terputus di mata rantai pertama (tidak ada penciptaan pengiriman).
15. Rantai **Produksi (role) → QC** terputus: role produksi tidak punya cara mencatat batch.
16. **PO** tidak punya tahap persetujuan (`disetujui`/`ditolak` tak terpakai); "bayar PO" justru menambah stok (semantik: pembelian barang, bukan approval produksi).
17. Edit/hapus barang keluar/rusak tidak mengoreksi stok (risiko stok negatif/salah).

## Rekomendasi Dokumentasi

Bagian skripsi/dokumen yang perlu disesuaikan dengan implementasi aktual:

1. **"Sistem membuat invoice" (kasir)** → tulis bahwa dokumen penjualan yang dihasilkan sistem adalah **nota** (`cetak-nota`); tabel `invoices` didefinisikan namun belum dipakai. Jika skripsi mempertahankan istilah invoice, uraikan bahwa `penjualan` + `detail_penjualans` berperan sebagai invoice.
2. **"Driver menerima invoice"** → sesuaikan: tidak ada fitur penugasan/pembuatan pengiriman; yang sudah diimplementasi adalah **alur status pengiriman** (terima → mulai → sampai + upload bukti → selesai) yang menunggu data dari penjualan.
3. **"Bagian Produksi mencatat pelaksanaan produksi"** → implementasi aktual: pencatatan `produksis` terjadi **saat QC memeriksa produk** (`QcController@store`); role produksi saat ini hanya dashboard read-only. Tulis sebagai batasan/deviasi, atau implementasikan form produksi.
4. **"Kasir memvalidasi stok saat menyimpan transaksi"** → aktual: validasi & pengurangan stok terjadi saat **konfirmasi pembayaran** (`bayarTransaksi`); transaksi baru berstatus `pending`.
5. **"Admin mengelola data master & pengguna"** → tidak ada kelola pengguna; tulis bahwa manajemen pengguna saat ini manual (seeder) + register publik.
6. **"Manajemen menetapkan target produksi"** → belum diimplementasi; sebutkan sebagai rencana/batasan.
7. **"Laporan SPJ bulanan"** → belum diimplementasi.
8. **"Laporan keuangan"** → halaman laporan saat ini menampilkan data statis; export memakai data dummy — jangan klaim sebagai laporan riil sebelum dikoneksikan ke model.
9. **Daftar role** → sistem memiliki 9 role (`admin, marketing, qc, produksi, gudang, keuangan, kasir, manajemen, driver`) vs 8 role skripsi; tentukan apakah `marketing` bagian dari ruang lingkup.
10. **Modul Pembelian (supplier)** → tidak dibahas di skripsi; tambahkan sebagai fitur tambahan atau hapus dari ruang lingkup.
11. **Pembahasan keamanan** → uraikan proteksi aktual: middleware `role:` hanya pada 7 grup prefix; mayoritas rute lain terbuka; grup keuangan hanya `auth`.
12. **Daftar tabel** → tandai tabel skema-tak-pakai: `invoices`, `pembayaran_utangs`, kolom `foto_reject`, `jumlah_dicek`, `tanggal_qc` (sesuai temuan audit ini).

---

*Lampiran verifikasi: hasil HTTP per role (2026-09-29) dan isi `laravel.log` pada sesi audit tersimpan di `C:\Users\Ananda Az Haruddin S\AppData\Local\Temp\opencode\qa_http.ps1` (skrip) dan `.../qa_migrations.php` (inspeksi DB). Tidak ada perubahan source code selama audit ini.*

---

## Addendum — Status Perbaikan (2026-09-29, setelah audit; disetujui pengguna "lanjut semuanya")

Audit ini tetap menjadi **snapshot kondisi saat diaudit**. Perbaikan yang disetujui setelahnya (detail lengkap + evidence di `AGENTS.md` → "Work progress" item 5 & "Bug history"):

- **Semua 500 level kode/environment teratasi** — tidak ada 500 yang tersisa; 404 hanya pada rute yang sengaja dihapus (`/test`, `/penjualan`, `/invoice`, `/permintaan-uang`, `/manajemen/laporan/filter`, `/manajemen/dashboard/export/*`).
- **Admin tidak lagi "bricked"** — halaman `layouts/app` (termasuk `/stok` kini dengan view + data riil) semua 200; role `manajemen` bisa login (user dibuat ulang dari seeder).
- **Keamanan** — semua rute modul kini di balik `auth` + `role:` (guest 302, cross-role 403); temuan "rute publik" di atas sudah tidak berlaku.
- **Laporan keuangan & list pembelian** kini data riil (bukan dummy); view `pembelian_show`/`pembelian_edit` dibuat; migration `kode_produk`+`qty` untuk `produks` ditambahkan; model `PembayaranUtang` dibuat.
- **Gap workflow skripsi yang TIDAK berubah** (tetap butuh desain fitur): tanpa user-management, tanpa pencatatan produksi oleh role produksi, tanpa sumber data `Pengiriman`, tanpa `Invoice::create`, status PO `disetujui/ditolak` tidak pernah di-set, tanpa SPJ, tanpa target produksi, aksi kirim/tagih penagihan masih dummy.

---

## Addendum — Fitur Workflow Skripsi Diimplementasikan (2026-09-30, disetujui pengguna)

Semua gap workflow skripsi pada addendum di atas **kini terimplementasi** dan terverifikasi E2E (29/29 asersi DB + sweep HTTP per-role + `composer test` 2/2; detail + evidence di `AGENTS.md` → "Work progress" item 6):

| Gap skripsi | Status | Implementasi |
|---|---|---|
| Role produksi mencatat pelaksanaan produksi | ✅ | `ProduksiController@create/storeProduksi` + `GET/POST /produksi/{form,store}` (status `proses` → antrean QC) |
| QC menandai produksi selesai | ✅ | `QcController@store` set `produksis.status='selesai'` setelah pemeriksaan |
| Penjualan → Pengiriman (driver dapat tugas) | ✅ | `KasirController@bayarTransaksi`: lunas → `Pengiriman::create` status `baru` (auto, dalam transaksi DB) |
| Driver alur penuh + bukti foto | ✅ | E2E `baru→siap→berangkat→sampai (upload bukti)→selesai`; `storage:link` dibuat; detail tampilkan no. invoice riil |
| "Sistem membuat invoice" | ✅ | `Invoice::create` saat lunas (`INV{YmdHis}`) + migration kolom `invoices` + halaman `GET /invoice` (marketing/admin); `Penjualan::invoice()` hasOne |
| PO approval `disetujui/ditolak` | ✅ | `PoController@index/setujui/tolak` (halaman `/po` kini hidup — sebelumnya scaffold kosong/blank); kasir bisa bayar PO `menunggu`/`disetujui`; PO `ditolak` tidak bisa dibayar |
| Admin kelola pengguna | ✅ | `UserController` CRUD `/admin/users*` (9 role, guard hapus/ubah-role diri sendiri) + 3 view + sidebar "Administrasi" |
| Manajemen tetapkan target produksi | ✅ | Tabel `target_produks` + CRUD `manajemen.target` + progress realisasi (dari `produksis` selesai) |
| Laporan produksi (manajemen) | ✅ | Jenis `produksi` di `ManajemenController` (baris `PRD-xxxx`, nilai = jumlah×harga) + opsi di laporan + card dashboard ke-5 |
| Laporan SPJ bulanan (kasir) | ✅ | `KasirController@spj` + `GET /kasir/spj` (rekap metode + produk + rincian, print) |
| Penagihan kirim/tagih riil | ✅ (tanpa email) | Kolom `tagihan_dikirim_at`/`tagihan_ditagih_at`; `PenagihanController` data riil (formKirim/`show` sebelumnya dummy/undefined var); tracking timestamp sebagai pengganti SMTP |

Catatan deviasi untuk skripsi:
- **Penagihan** hanya tracking timestamp (tidak ada infrastruktur email di sistem) — sesuaikan narasi "kirim tagihan" menjadi "pencatatan pengiriman tagihan".
- **PO kasir** semantiknya *kebutuhan produksi* (bayar PO → stok masuk), bukan penjualan — jadi **tidak** memicu Pengiriman; Pengiriman hanya dari transaksi penjualan lunas (sesuai alur invoice → driver di skripsi).
- Temuan audit lama yang kini **kedaluwarsa** (jangan dikutip lagi): poin D-14 (rantai penjualan→pengiriman terputus), D-15 (produksi→QC terputus), poin rekomendasi 1, 2, 3, 5, 6, 7 (invoice, driver, form produksi, kelola pengguna, target produksi, SPJ), dan baris gap di addendum 2026-09-29. Poin 4, 8, 9, 10, 11, 12 tetap relevan (dengan catatan: laporan keuangan sudah riil sejak round 2026-09-29; proteksi middleware kini menyeluruh).

---

## Addendum — Penyelarasan Scope 5 Role (2026-09-30, disetujui pengguna: "hapus role yang tidak perlu")

User mengonfirmasi scope skripsi sesungguhnya hanya **5 role** (berdasarkan tabel use case 4.4.2–4.8 + activity diagram yang dilampirkan: Login, Pengelolaan Gudang, Transaksi Penjualan, Pengelolaan Keuangan, Pemeriksaan Barang, Pengiriman Barang, Laporan Sistem). Role **admin/marketing/produksi/manajemen + akun `test@` dihapus** dari kode & database (detail + evidence di `AGENTS.md` → "Work progress" item 7):

| Item scope | Status akhir |
|---|---|
| Role Admin (kelola pengguna, akses penuh) | ❌ Dihapus — tak ada use case user management; akun via seeder (5 role) |
| Role Marketing (dashboard, PO, stok) | ❌ Dihapus — list invoice dipindah ke menu Kasir |
| Role Produksi (form catat produksi) | ❌ Dihapus — pencatatan produksi kini via form **"Produk Baru" on-the-fly di QC Pemeriksaan** (satu-satunya pintu masuk data produksi) |
| Role Manajemen (target produksi + laporan produksi + dashboard) | ❌ Dihapus — `target_produks` dropped (migration `2026_09_30_000003` + model + export ikut dihapus); tak ada di use case 5 role |
| PO approval (`/po` setujui/tolak) | ❌ Dihapus — use case Transaksi Penjualan tidak punya langkah approval; PO dibuat kasir → dibayar kasir (status `menunggu`→`selesai`) |
| `/register` publik | ❌ Dihapus — tak ada use case; tadinya bug (redirect `/home` 404, daftar role tak lengkap) |
| List invoice | ✅ Dipertahankan — pindah ke menu **Kasir** (`/invoice`, kini layout kasir) sesuai use case "sistem membuat invoice" |
| Laporan Sistem (`/laporan`) | ✅ Dipertahankan — dibatasi actor use case: **Gudang, Kasir, Keuangan** |
| Transaksi dua tahap (pending→bayar) | ✅ Dipertahankan — alur alternatif "jika stok tidak mencukupi, transaksi dibatalkan" diimplementasi sebagai validasi stok saat bayar (stok kurang → pembayaran ditolak); transaksi pending = sumber data piutang/penagihan role Keuangan |
| Penagihan (kirim/tagih) | ✅ Dipertahankan — tracking timestamp (tanpa SMTP), follow-up tercatat |
| QC 2 form (produksi existing + produk baru) | ✅ Dipertahankan — keduanya; form "produk baru" = pencatatan produksi + hasil QC sekaligus |

**Catatan:** tabel mapping per role di atas (era 8 role) adalah snapshot historis; baris yang menyangkut modul terhapus (user management, manajemen, produksi role, PO approval, marketing) kini tidak berlaku karena fiturnya **dihapus mengikuti scope**, bukan ditinggalkan. Verifikasi round ini: `qa3_sweep.ps1` (5 role 200 semua halaman modul + 403 lintas-role + 404 rute terhapus), `qa3_e2e.ps1` + `qa3_verify.php` (17/17 asersi DB), smoke 5 role, `composer test` 2/2. Panduan cek manual per role: `ASA_TIRTA_ROLE.md` (sudah di-rewrite untuk 5 role).
