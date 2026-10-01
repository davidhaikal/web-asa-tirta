# Workflow Summary

> **Sumber:** `Skripsi.docx` — *Perancangan Sistem Informasi Manajemen Produksi dan Penjualan Air Minum Dalam Kemasan (AMDK) Berbasis Web* pada Perum Jasa Tirta I (sistem **ASA TIRTA**).
> Dokumen ini merangkum workflow setiap role/user **hanya berdasarkan isi skripsi**, tanpa menambah informasi baru.
>
> ⚠️ **Koreksi scope (2026-09-30, dikonfirmasi user dari tabel use case 4.4.2–4.8 + activity diagram skripsi):** sistem cukup **5 role** — **Admin Gudang, Kasir, Admin Keuangan, QC, Driver**. Daftar 8 role di bawah adalah hasil gabungan penamaan Bab I–V saat ekstraksi; implementasi kini **diselaraskan ke 5 role** (role admin-pemisah, produksi-sebagai-role, manajemen, marketing, akun `test@` dihapus dari sistem). Rincian: `ASA_TIRTA_ROLE_FEATURE_AUDIT.md` (addendum 2026-09-30) + `AGENTS.md` (Work progress item 7) + `ASA_TIRTA_ROLE.md` (akun & checklist per role).

## Daftar Role dalam Sistem

| No | Role | Fokus Utama |
|----|------|-------------|
| 1 | Admin (Administrator) | Data master & pengguna, data pelanggan, data penjualan, tagihan |
| 2 | Bagian Produksi | Pelaksanaan dan pencatatan produksi AMDK |
| 3 | QC (Quality Control) | Pemeriksaan kualitas produk, penetapan status layak/tidak layak |
| 4 | Bagian Gudang (Admin Gudang) | Inventory barang jadi: produk, barang masuk/keluar, barang rusak, permintaan stok |
| 5 | Kasir | Purchase Order (PO), transaksi penjualan, invoice, nota |
| 6 | Admin Keuangan | Data pelanggan, piutang, penagihan, laporan keuangan |
| 7 | Driver | Pengiriman barang dan pengunggahan bukti pengiriman |
| 8 | Manajemen | Monitoring dashboard, pengawasan, evaluasi, pengambilan keputusan |

> **Catatan penamaan:** Bab I–III menggunakan istilah *admin, bagian produksi, bagian gudang, kasir, dan manajemen*, sedangkan Bab IV–V (rancangan & implementasi akhir) menggunakan *Admin Gudang, Kasir, Admin Keuangan, QC, Driver*, ditambah *Manajemen* sebagai pengguna dashboard. Kedua penamaan tersebut digabungkan dalam daftar di atas.

## Alur Bisnis Keseluruhan

1. **Manajemen** menetapkan target produksi.
2. **Bagian Produksi** memproduksi AMDK berdasarkan rencana/target; hasil produksi dicatat sebagai barang jadi.
3. **QC** memeriksa hasil produksi; produk yang **lolos QC** menambah stok inventory barang jadi, produk tidak layak dicatat sebagai **reject**.
4. **Bagian Gudang** menyimpan barang jadi, mencatat barang masuk/keluar, memantau stok.
5. **Kasir** membuat **PO** (dasar kebutuhan produksi) dan melakukan transaksi penjualan; sistem membuat invoice, mengurangi stok, dan mencetak nota.
6. **Driver** menerima invoice, mengirim barang, mengunggah bukti pengiriman.
7. **Admin Keuangan** mengelola piutang dan penagihan dari transaksi pelanggan.
8. **Manajemen** memantau laporan produksi, persediaan, dan penjualan untuk evaluasi dan pengambilan keputusan.

## Workflow Bersama: Login (semua pengguna)

1. Membuka halaman login.
2. Memasukkan email dan password.
3. Sistem memvalidasi data sesuai hak akses.
4. Dashboard ditampilkan sesuai hak access pengguna.

> **Alur alternatif:** jika data login salah, sistem menampilkan pesan kesalahan dan pengguna mengulangi proses login.

---

## 1. Admin (Administrator)

### Tanggung Jawab

- Mengelola data master dan pengguna sistem.
- Mengelola data pelanggan: input, edit, atau hapus data informasi pelanggan.
- Mengelola data penjualan: menambah, mengubah, dan menghapus data transaksi penjualan.
- Mengelola laporan penjualan: menyusun dan merapikan data transaksi menjadi laporan yang siap dibaca.
- Memverifikasi piutang & pendapatan: mengecek kesesuaian antara uang yang masuk dengan catatan piutang yang belum dibayar.
- Membuat tagihan: menghasilkan dokumen penagihan untuk pelanggan yang memiliki tunggakan.

### Workflow

1. Login ke sistem (divalidasi sesuai hak akses).
2. Mengelola data master dan pengguna.
3. Mengelola data pelanggan (input/edit/hapus).
4. Mengelola data penjualan: menambah, mengubah, atau menghapus transaksi (tanggal transaksi, nama pelanggan, jenis produk, jumlah terjual, total transaksi).
5. Menyusun data transaksi menjadi laporan penjualan.
6. Memverifikasi kesesuaian uang masuk dengan catatan piutang.
7. Membuat dokumen tagihan untuk pelanggan ber-tunggakan.

### Input

- Data pelanggan (baru/perubahan).
- Data transaksi penjualan.
- Uang yang masuk dan catatan piutang yang belum dibayar.

### Output

- Data master dan pengguna tersimpan.
- Data pelanggan tersimpan/terperbarui di database.
- Data penjualan (tambah/ubah/hapus) tersimpan.
- Laporan penjualan yang siap dibaca.
- Dokumen tagihan pelanggan ber-tunggakan.

### Interaksi dengan Role Lain

- **Kasir** — sumber data transaksi penjualan yang dikelola Admin.
- **Manajemen** — laporan penjualan digunakan manajemen sebagai bahan evaluasi.
- **Admin Keuangan** — aspek piutang & penagihan di Bab IV–V dirinci pada role Admin Keuangan.

---

## 2. Bagian Produksi

### Tanggung Jawab

- Melakukan pengolahan dan pengemasan air minum (AMDK) sesuai standar yang telah ditetapkan.
- Mencatat pelaksanaan produksi dan hasil produksi.
- Menyerahkan hasil produksi sebagai barang jadi ke bagian gudang.

### Workflow

1. Perencanaan produksi berdasarkan target yang ditetapkan manajemen / rencana produksi.
2. Melakukan proses pengolahan dan pengemasan AMDK sesuai standar.
3. Mencatat pelaksanaan produksi dan hasil produksi ke sistem.
4. Hasil produksi dicatat sebagai barang jadi.
5. Barang jadi diserahkan ke bagian gudang untuk disimpan sebagai persediaan.
6. Hasil produksi diperiksa oleh QC; yang lolos QC menambah stok inventory barang jadi.

### Input

- Target/rencana produksi dari manajemen.
- Bahan baku (data bahan baku dan persediaan dicatat oleh bagian gudang).

### Output

- Data produksi yang tercatat di sistem.
- Hasil produksi berupa barang jadi yang diserahkan ke gudang.

### Interaksi dengan Role Lain

- **Manajemen** — menerima target produksi; data produksi dipakai untuk pengawasan/evaluasi.
- **Bagian Gudang** — menyerahkan hasil produksi untuk disimpan sebagai persediaan.
- **QC** — hasil produksi diperiksa kualitasnya sebelum masuk stok.

---

## 3. QC (Quality Control)

### Tanggung Jawab

- Melakukan pemeriksaan kualitas produk (hasil produksi).
- Menentukan status produk layak/tidak layak.

### Workflow

1. Login ke sistem.
2. Memilih data barang/produk yang akan diperiksa.
3. Melakukan pemeriksaan kualitas.
4. Menentukan status layak atau tidak layak berdasarkan hasil pemeriksaan.
5. Produk yang memenuhi standar dicatat sebagai **lolos QC** (menambah stok inventory barang jadi); yang tidak memenuhi standar dicatat sebagai **barang reject**.
6. Seluruh hasil pemeriksaan disimpan ke database (dapat digunakan sebagai laporan QC).

> **Alur alternatif:** jika pemeriksaan belum selesai, data tidak disimpan.

### Input

- Hasil produksi/produk yang tersedia untuk diperiksa.

### Output

- Hasil pemeriksaan QC tersimpan (status lolos/reject).
- Laporan Quality Control dari data tersimpan.

### Interaksi dengan Role Lain

- **Bagian Produksi** — memeriksa hasil produksi.
- **Bagian Gudang** — produk lolos QC menambah stok inventory barang jadi.

---

## 4. Bagian Gudang (Admin Gudang)

### Tanggung Jawab

- Mengelola inventory barang jadi (stok fisik dan pencatatan barang).
- Mengelola data produk: menambah, mengubah, menghapus, dan melihat data produk.
- Mencatat barang masuk, barang keluar, dan barang rusak.
- Mengelola permintaan stok.
- Memperbarui data stok secara real-time setelah pengecekan.
- Memantau barang rusak dan melihat laporan gudang.

### Workflow

1. Login ke sistem → masuk Dashboard Gudang (kartu ringkasan: total produk, barang masuk, barang keluar, barang rusak; grafik aktivitas gudang; panel informasi stok menipis).
2. Memilih menu yang akan digunakan.
3. Mengelola data produk (nama produk, kode produk, jumlah per kardus, stok, harga, status produk).
4. Mencatat barang masuk (termasuk dari hasil produksi) → stok bertambah.
5. Mencatat barang keluar untuk memenuhi kebutuhan penjualan → stok berkurang.
6. Mencatat barang rusak (produk tidak layak jual agar bisa diretur atau dihapus dari stok).
7. Mengajukan permintaan stok (penambahan barang ke pabrik atau pusat).
8. Melihat laporan gudang.
9. Perubahan disimpan ke database; sistem memperbarui informasi stok jika terjadi perubahan jumlah barang; kembali ke halaman dashboard.

> **Alur alternatif:** jika data tidak lengkap, sistem menolak penyimpanan.

### Input

- Data produk.
- Hasil produksi yang diserahkan bagian produksi.
- Kebutuhan penjualan (barang keluar).
- Identifikasi produk rusak.

### Output

- Data produk dan persediaan ter-update.
- Catatan barang masuk, barang keluar, dan barang rusak.
- Permintaan stok tersimpan.
- Stok yang diperbarui secara real-time (sesuai fisik).
- Laporan gudang.

### Interaksi dengan Role Lain

- **Bagian Produksi** — menerima hasil produksi untuk disimpan.
- **Kasir** — mencatat barang keluar untuk penjualan; ketersediaan stok menentukan kelancaran transaksi.
- **Manajemen** — data stok dan laporan gudang dipantau manajemen.
- **Pabrik/Pusat** — tujuan permintaan penambahan stok.

---

## 5. Kasir

### Tanggung Jawab

- Berhubungan langsung dengan transaksi harian dan operasional penjualan (melakukan aktivitas penjualan bagian penjualan).
- Membuat dan mengelola Purchase Order (PO) sebagai dasar kebutuhan produksi.
- Melakukan transaksi penjualan kepada pelanggan.
- Membuat invoice dan mencetak nota penjualan.
- Membuat laporan SPJ bulanan (Surat Pertanggungjawaban penggunaan dana/operasional).
- Monitoring laporan penjualan harian.

### Workflow

1. Login ke sistem → masuk Dashboard Kasir.
2. Membuat Purchase Order (PO): produk, jumlah kebutuhan, tanggal kebutuhan, catatan; memantau status tiap PO pada daftar kebutuhan produksi.
3. Memilih menu transaksi penjualan.
4. Memilih pelanggan dan produk, input jumlah pembelian, memilih metode pembayaran.
5. Sistem menghitung total transaksi secara otomatis dan menampilkan stok tersedia sebagai acuan.
6. Sistem melakukan validasi data dan pengecekan stok:
   - **Stok tersedia** → transaksi disimpan, invoice dibuat, stok berkurang, nota dicetak.
   - **Stok tidak mencukupi** → transaksi dibatalkan.
7. Melihat laporan penjualan dan stok (memastikan semua transaksi terinput dengan benar).
8. Menyusun laporan SPJ bulanan.

### Input

- Data pelanggan.
- Data produk dan stok tersedia (dari gudang).
- Jumlah pembelian dan metode pembayaran.
- Kebutuhan produksi (untuk PO).

### Output

- Purchase Order (PO) tersimpan.
- Transaksi penjualan tersimpan di database.
- Invoice.
- Nota penjualan tercetak.
- Stok berkurang secara otomatis.
- Laporan SPJ bulanan.

### Interaksi dengan Role Lain

- **Bagian Gudang** — ketersediaan stok menentukan transaksi berhasil/dibatalkan; stok berkurang saat transaksi tersimpan.
- **Bagian Produksi** — PO menjadi dasar kebutuhan produksi.
- **Driver** — invoice diserahkan kepada driver untuk pengiriman.
- **Admin/Manajemen** — data penjualan dipakai untuk laporan, verifikasi piutang, dan evaluasi.

---

## 6. Admin Keuangan

### Tanggung Jawab

- Mengelola data pelanggan.
- Mengelola piutang pelanggan dan proses penagihan.
- Mengelola tagihan pelanggan.
- Melihat laporan keuangan dan penjualan.

### Workflow

1. Login ke sistem → masuk Dashboard Keuangan (ringkasan: total pendapatan, jumlah transaksi lunas, total piutang, tagihan yang masih menunggu pembayaran; grafik arus kas).
2. Mengelola data pelanggan.
3. Mengelola piutang: mencari data, menyaring berdasarkan status dan jatuh tempo, melihat detail tagihan, mengirim tagihan, mengubah status pembayaran.
4. Mengelola tagihan customer.
5. Melihat laporan penjualan/keuangan.

> **Alur alternatif:** jika data tidak valid, sistem menolak penyimpanan.

### Input

- Data pelanggan.
- Data piutang dan tagihan dari transaksi penjualan.
- Pembayaran yang diterima.

### Output

- Data pelanggan ter-update.
- Piutang terkelola (tagihan terkirim, status pembayaran berubah).
- Laporan keuangan.

### Interaksi dengan Role Lain

- **Kasir** — piutang, pendapatan, dan tagihan berasal dari transaksi penjualan yang dilakukan kasir.
- **Admin** — terkait verifikasi piutang & pendapatan (kegiatan Admin pada Bab III).

---

## 7. Driver

### Tanggung Jawab

- Menerima invoice.
- Mengelola/melakukan pengiriman barang.
- Mengunggah bukti pengiriman.

### Workflow

1. Login ke sistem.
2. Menerima invoice (prasyarat: invoice telah diterima) — halaman Detail Pengiriman menampilkan nomor invoice, data pelanggan, daftar produk yang dikirim, dan status pengiriman.
3. Melakukan pengiriman barang kepada pelanggan.
4. Mengunggah bukti pengiriman setelah produk diterima oleh pelanggan.
5. Sistem memperbarui status pengiriman.

> **Alur alternatif:** jika bukti belum diunggah, status pengiriman belum selesai.

### Input

- Invoice dari kasir.
- Data pelanggan dan daftar produk yang dikirim (dalam invoice).

### Output

- Barang terkirim dan diterima pelanggan.
- Bukti pengiriman tersimpan.
- Status pengiriman ter-update (selesai setelah bukti diunggah).

### Interaksi dengan Role Lain

- **Kasir** — menerima invoice untuk dikirimkan.
- **Pelanggan (di luar sistem)** — penerima produk; bukti diunggah setelah produk diterima.

---

## 8. Manajemen

### Tanggung Jawab

- Melakukan pengawasan dan evaluasi terhadap keseluruhan proses bisnis produksi dan penjualan.
- Menetapkan target produksi.
- Memantau laporan produksi, persediaan, dan penjualan sebagai dasar pengambilan keputusan.
- Menggunakan dashboard sebagai pusat informasi utama untuk memantau performa secara keseluruhan melalui visualisasi data.

### Workflow

1. Menampilkan dashboard (pusat informasi utama untuk memantau performa secara keseluruhan; digunakan manajemen dan aktor lainnya).
2. Menetapkan target produksi untuk bagian produksi.
3. Memantau laporan produksi, persediaan, dan penjualan (termasuk melihat laporan penjualan).
4. Melakukan pengawasan dan evaluasi proses bisnis produksi dan penjualan.
5. Menggunakan informasi yang akurat dan tepat waktu sebagai dasar pengambilan keputusan strategis dan operasional.

### Input

- Data produksi, persediaan, dan penjualan dari seluruh bagian.
- Laporan produksi, persediaan, dan penjualan.

### Output

- Target produksi.
- Hasil pengawasan dan evaluasi.
- Keputusan strategis dan operasional.

### Interaksi dengan Role Lain

- **Bagian Produksi** — menetapkan target produksi; memantau hasil produksi.
- **Bagian Gudang** — memantau persediaan/stok barang jadi.
- **Kasir** — memantau transaksi dan laporan penjualan (pemantauan pencapaian target penjualan).
- **Admin** — melihat laporan penjualan bersama admin.
- **Seluruh bagian** — menerima informasi operasional untuk mendukung pengawasan dan pengambilan keputusan.

---

## Matriks Interaksi Antar Role

| Dari \ Ke | Produksi | QC | Gudang | Kasir | Keuangan | Driver | Manajemen |
|-----------|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| **Produksi** | — | hasil produksi | barang jadi | | | | data produksi |
| **QC** | | — | stok (lolos QC) | | | | |
| **Gudang** | | | — | stok & barang keluar | | | data stok & laporan |
| **Kasir** | PO (kebutuhan) | | transaksi/stok | — | piutang & tagihan | invoice | data penjualan |
| **Keuangan** | | | | sumber transaksi | — | | |
| **Driver** | | | | terima invoice | | — | status pengiriman |
| **Manajemen** | target produksi | | | | | | — |
