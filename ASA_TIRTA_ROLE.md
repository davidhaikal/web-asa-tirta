# ASA TIRTA — Akun Login & Panduan Cek Fitur per Role

> **Tujuan:** dokumen ini berisi **username + password semua role** plus **checklist verifikasi fitur** per role sesuai use case & activity diagram skripsi (`Skripsi.docx` — 5 role: Admin Gudang, Kasir, Admin Keuangan, QC, Driver). Gunakan untuk login manual dan cek apakah setiap fitur sudah sesuai skripsi.
>
> **Diperbarui:** 2026-09-30 (setelah penyelarasan scope 5 role — role Admin/Marketing/Produksi/Manajemen dihapus; sistem kini persis 5 role skripsi).

## 0. Persiapan Login

| Item | Nilai |
|---|---|
| URL aplikasi | `http://127.0.0.1:8000` (atau `http://localhost/web-asa-tirta-main` via Laragon) |
| Halaman login | `http://127.0.0.1:8000/login` |
| Server dev | `php artisan serve --host=127.0.0.1 --port=8000` (jika belum jalan) |
| Halaman landing | `http://127.0.0.1:8000/` — klik **Login** |

> Setelah login, sistem otomatis redirect ke dashboard sesuai role (use case **Login**). Semua 5 akun di bawah **sudah diverifikasi bisa login** (uji HTTP 2026-09-30). Data login salah → sistem menampilkan pesan kesalahan (alur alternatif use case Login).

## 1. Daftar Akun (5 role skripsi)

| Role skripsi | Username (email) | Password | Redirect setelah login |
|---|---|---|---|
| Admin Gudang | `gudang@example.com` | `password` | `/gudang/dashboard` |
| Kasir | `kasir@example.com` | `password` | `/kasir/dashboard` |
| Admin Keuangan | `keuangan@example.com` | `password` | `/keuangan/dashboard` |
| QC | `qc@example.com` | `password` | `/qc/dashboard` |
| Driver | `driver@example.com` | `password` | `/driver/dashboard` |

> Role lama (admin, marketing, produksi, manajemen) + akun `test@example.com` **sudah dihapus** dari sistem dan database (2026-09-30). Halaman `/admin`, `/manajemen/*`, `/produksi*`, `/po`, `/register`, `/dashboard` kini 404.

---

## 2. Checklist Verifikasi per Role

Legenda: **Halaman** = tempat klik; **Cek** = yang harus terlihat/bekerja; **Skripsi** = use case yang dipenuhi.

### 2.1 Admin Gudang — `gudang@example.com` / `password`

> **Use case Pengelolaan Gudang:** mengelola data produk, barang masuk, barang keluar, barang rusak, permintaan stok, dan laporan gudang. Setiap perubahan data memperbarui informasi stok.

| Halaman | Cek |
|---|---|
| Dashboard Gudang (`/gudang/dashboard`) | Ringkasan produk, barang masuk/keluar/rusak, total stok, aktivitas terkini |
| **Data Produk** (`/gudang/produk`) | CRUD produk (nama, harga, kode, qty, stok) — "mengelola data produk" |
| **Barang Masuk** (`/gudang/barang-masuk`) | CRUD; simpan → stok produk bertambah + kartu stok tercatat |
| **Barang Keluar** (`/gudang/barang-keluar`) | CRUD; simpan → stok berkurang (ada guard stok tidak boleh minus) |
| **Barang Rusak** (`/gudang/barang-rusak`) | CRUD barang rusak |
| **Permintaan Stok** (`/gudang/permintaan-stok`) | CRUD permintaan stok |
| Stok Produk (`/stok`) | Riwayat mutasi stok + tab Semua/Masuk/Keluar |
| **Laporan Gudang** (`/gudang/laporan`) | 4 kartu ringkasan + tabel per produk (akumulatif masuk/keluar/rusak, stok + badge AMAN/MENIPIS/KRITIS, nilai stok, TOTAL) + **cetak** + export PDF/Excel |
| Laporan Sistem (`/laporan`) | Laporan penjualan + stok terkini (use case **Laporan Sistem**) |

**Cek integrasi stok:** stok gudang bertambah otomatis dari (a) hasil QC **Layak** dan (b) pembayaran PO kasir; berkurang dari penjualan kasir yang dibayar + barang keluar.

---

### 2.2 Kasir — `kasir@example.com` / `password`

> **Use case Transaksi Penjualan:** membuat Purchase Order, memilih pelanggan & produk, **sistem membuat invoice**, mencetak nota, melihat laporan penjualan dan stok. Alur alternatif: jika stok tidak mencukupi, transaksi tidak bisa dibayar. Layout khusus kasir (topbar, bukan sidebar).

| Halaman | Cek |
|---|---|
| Dashboard Kasir (`/kasir/dashboard`) | 4 stat harian, chart 7 hari, stock, form PO, kebutuhan produksi, transaksi terbaru |
| **Transaksi Penjualan** (`/kasir/transaksi`) | Keranjang POS (pilih produk, qty, total live) → Simpan (status `BELUM LUNAS`) → **Konfirmasi Bayar** → stok berkurang + status LUNAS + **sistem membuat invoice** + tugas pengiriman |
| **Uji alur inti skripsi** | Bayar transaksi → flash "…Invoice INV… dibuat & tugas pengiriman dibuat" → login **Driver** → tugas baru muncul → kembali ke kasir → invoice terlihat di menu **Invoice** |
| PO Belum Bayar (section di `/kasir/transaksi`) | **Membuat Purchase Order** (form di dashboard/transaksi) → PO `menunggu` punya tombol **Konfirmasi Bayar** → stok bertambah (PO = kebutuhan produksi, bukan penjualan) |
| **Invoice** (menu topbar, `/invoice`) | Daftar invoice yang dibuat otomatis sistem + search (no. invoice / pelanggan) |
| **Cetak Nota** (`/kasir/nota`) | Search + per baris **Cetak Nota** (nota tercetak di halaman baru) |
| Laporan Penjualan (`/kasir/laporan-penjualan`) | Filter hari/minggu/bulan + 4 kartu + tabel + TOTAL + cetak |
| Laporan Stok (`/kasir/laporan-stok`) | 3 stat + badge AMAN/MENIPIS/KRITIS + kartu stok per produk |
| Laporan SPJ (`/kasir/spj`) | Pilih bulan → total transaksi/pendapatan/lunas/pending, rekap metode (tunai/transfer/qris), rekap produk, rincian + **Cetak SPJ** |
| Laporan Sistem (`/laporan`) | Laporan penjualan + stok terkini (use case **Laporan Sistem**) |

**Catatan:** transaksi baru = `pending` (stok belum berkurang); stok divalidasi & dikurangi saat **konfirmasi bayar** — jika stok kurang, sistem menolak pembayaran ("Stok … tidak cukup") sesuai alur alternatif use case. Dua tahap ini disengaja: transaksi pending menjadi sumber data **piutang & penagihan** role Admin Keuangan.

---

### 2.3 Admin Keuangan — `keuangan@example.com` / `password`

> **Use case Pengelolaan Keuangan:** mengelola data pelanggan, piutang, tagihan customer, dan laporan.

| Halaman | Cek |
|---|---|
| Dashboard Keuangan (`/keuangan/dashboard`) | Ringkasan + grafik 6 bulan |
| **Data Pelanggan** (`/keuangan/pelanggan`) | CRUD pelanggan |
| **Piutang** (`/keuangan/piutang`) | List piutang + update/hapus |
| Pembelian Barang (`/pembelian`) | CRUD pembelian + detail + edit (fitur pendukung) |
| **Penagihan** (`/keuangan/penagihan`) | Transaksi belum lunas (tagihan customer); kolom **Kec. Kirim**; tombol **Tagih** (follow-up), **Kirim Tagihan**, **Kirim Semua** |
| Kirim Tagihan (`/keuangan/penagihan/kirim-tagihan`) | Search customer, filter status Pending/Menunggak (jatuh tempo = tanggal+30 hari), centang multi + metode → **Kirim Tagihan** → waktu kirim tercatat |
| Detail tagihan (`/keuangan/penagihan/{id}`) | Data lengkap dari transaksi (customer, no. transaksi, total, jatuh tempo) |
| **Laporan Keuangan** (`/keuangan/laporan`) | Data riil (total penjualan, transaksi, bulan ini) + export PDF/Excel |
| Laporan Sistem (`/laporan`) | Laporan penjualan + stok terkini (use case **Laporan Sistem**) |

**Deviasi:** "kirim/tagih" = pencatatan timestamp (`tagihan_dikirim_at`/`tagihan_ditagih_at`), **bukan kirim email** (sistem tak punya infrastruktur SMTP) — skripsi hanya mensyaratkan follow-up penagihan tercatat.

---

### 2.4 QC — `qc@example.com` / `password`

> **Use case Pemeriksaan Barang:** memeriksa produk, menentukan status **layak/tidak layak**, menyimpan hasil pemeriksaan. Prasyarat: produk tersedia. Barang lolos QC dicatat, yang tidak lolos = reject; hasil tersimpan jadi laporan QC.

| Halaman | Cek |
|---|---|
| Dashboard QC (`/qc/dashboard`) | Total QC, Lolos, Reject, hari ini + 6 QC terbaru |
| **Pemeriksaan Produk** (`/qc/pemeriksaan`) | Dua form: (a) daftar produksi **belum diperiksa** + form per baris, (b) form **catat produksi baru** (produk + jumlah + hasil) — satu form menghasilkan pencatatan produksi + hasil QC sekaligus |
| Uji **Layak** | Hasil `Layak` → simpan → cek role **Gudang**: stok produk bertambah sesuai jumlah produksi + kartu stok "Hasil produksi" |
| Uji **Tidak Layak** | Hasil `Tidak Layak` → simpan → stok **tidak bertambah**, masuk daftar Reject |
| Produk Lolos / Reject (`/qc/lolos`, `/qc/reject`) | Data terpisahkan sesuai hasil |
| **Laporan QC** (`/qc/laporan`) | Filter tanggal + export Excel/PDF + cetak |

**Catatan:** karena role Produksi tidak ada di skripsi, pencatatan pelaksanaan produksi dilakukan **oleh QC pada saat pemeriksaan** (form "produk baru" di halaman Pemeriksaan) — ini satu-satunya pintu masuk data produksi.

---

### 2.5 Driver — `driver@example.com` / `password`

> **Use case Pengiriman Barang:** **menerima invoice**, mengirim barang, **mengunggah bukti pengiriman**. Prasyarat: invoice telah diterima. Alur alternatif: jika bukti belum diunggah, status belum selesai.

| Halaman | Cek |
|---|---|
| Dashboard Driver (`/driver/dashboard`) | Pengiriman hari ini, sedang dikirim, selesai, menunggu konfirmasi + 5 terbaru |
| **Pengiriman** (`/driver/pengiriman`) | List tugas (kode TRX + **no. invoice riil**, customer, total, status) + tab filter + search |
| **Alur 5 langkah** (di halaman detail `/driver/pengiriman/{id}`) | 1) status `baru` → **✅ Terima Invoice** → 2) `siap` → **🚚 Mulai Pengiriman** → 3) `berangkat` → **Upload Bukti** (foto, max 5 MB) → 4) `sampai` → **🏁 Konfirmasi Selesai** → 5) 🎉 selesai + bukti foto tampil + no. invoice |
| Data dari mana? | Tugas muncul **otomatis** saat kasir memungut pembayaran transaksi penjualan (sistem membuat invoice + tugas pengiriman sekaligus) |

---

## 3. Skenario E2E Lintas-Role (alur bisnis skripsi utuh)

Jalankan berurutan dengan beberapa browser/tab (setiap tab login role berbeda):

1. **QC** → `/qc/pemeriksaan` → form **produk baru**: pilih Air Mineral, isi jumlah (mis. 50), hasil **Layak** → simpan.
2. **Admin Gudang** → `/gudang/produk` (atau `/stok`) → stok bertambah 50 + kartu stok "Hasil produksi".
3. **Kasir** → `/kasir/transaksi` → transaksi baru (mis. 2 unit) → **Konfirmasi Bayar** → flash menyebut Invoice + tugas pengiriman dibuat.
4. **Kasir** → menu topbar **Invoice** → invoice baru muncul (dibuat otomatis sistem).
5. **Kasir** → `/kasir/nota` → **Cetak Nota** transaksi yang baru dibayar.
6. **Driver** → `/driver/pengiriman` → tugas baru (no. invoice tertera) → Terima → Mulai → Upload Bukti → Selesai.
7. **Kasir** → buat transaksi lain yang **tidak dibayar** (biarkan pending).
8. **Admin Keuangan** → `/keuangan/penagihan` → transaksi pending muncul sebagai tagihan → **Kirim Tagihan** / **Tagih** → waktu tercatat.
9. **Laporan:** Admin Gudang `/gudang/laporan`, Kasir `/kasir/laporan-penjualan` + `/kasir/spj`, Keuangan `/keuangan/laporan`, dan ketiganya bisa buka `/laporan` (Laporan Sistem) — pilih periode, lihat, cetak.

> Langkah 1–8 **sudah dijalankan otomatis** oleh skrip QA (`qa3_e2e.ps1` + 17 asersi DB di `qa3_verify.php`, semuanya lolos 2026-09-30) — cek manual di atas untuk konfirmasi visual.

## 4. Catatan

- **Baseline data** (2026-09-30): 1 produk (Air Mineral, stok 198), data demo (5 penjualan lama — 1 masih pending — 4 PO lama, 1 produksi lama, 2 QC lama, 1 legacy pengiriman, 1 legacy invoice kosong), **5 user** (persis role skripsi). Data test QA sudah dibersihkan.
- **Bukti foto driver** butuh symlink `public/storage` — **sudah dibuat** 2026-09-30 (`php artisan storage:link`).
- **Kode unik per detik:** `TRX{tanggal}` / `PO{tanggal}` / `INV{tanggal}` — jika dua transaksi dibuat di detik yang sama, salah satu bisa gagal; tunggu 1–2 detik.
- **Sejarah scope:** 2026-09-30 role Admin/Marketing/Produksi/Manajemen + akun `test@` dihapus sesuai koreksi skripsi (5 role). Modul yang ikut dihapus: Kelola Pengguna, /register, PO approval (`/po`), dashboard marketing, modul manajemen (target produksi + laporan produksi) — keputusan user, terdokumentasi di `ASA_TIRTA_ROLE_FEATURE_AUDIT.md` (addendum).
- Rincian teknis semua fitur: `AGENTS.md` → "Work progress" item 7; pemetaan skripsi-vs-kode lengkap: `ASA_TIRTA_ROLE_FEATURE_AUDIT.md`.
