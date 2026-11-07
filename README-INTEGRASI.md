# Ohara Bakery + Dashboard Admin (terintegrasi)

Website Ohara Bakery dan dashboard admin kini satu proyek. Folder `admin/` yang lama
sudah diganti dashboard; produk yang dikelola di dashboard langsung tampil di website.

## Cara pasang (XAMPP / Laragon)
1. Salin folder `ohara-bakery` ke `htdocs` / `www`. Nyalakan Apache + MySQL.
2. phpMyAdmin → **Import** → `database.sql` (seperti biasa).
3. phpMyAdmin → **Import** → `database_integrasi.sql` (**baru**, wajib, setelah langkah 2).
4. Website: `http://localhost/ohara-bakery/`
5. Admin: `http://localhost/ohara-bakery/admin/login.php` — `admin` / `admin123`
   (akun dibuat otomatis saat login pertama; segera ganti password).

## Apa yang disambungkan
- **Database**: dashboard memakai view `ohara_dashboard.produk` yang menunjuk ke
  `ohara_bakery.produk`. Kode produk dashboard (nama_produk, dst.) tidak diubah.
  `database_integrasi.sql` juga mengubah kolom `kategori` dari ENUM ke VARCHAR agar
  kategori bebas dari form dashboard tidak error.
- **Login**: `admin/login.php` & `logout.php` bawaan Ohara dipakai. Semua halaman dan
  proses dashboard dijaga `admin/auth.php`; tanpa login diarahkan ke halaman login.
- **Foto**: upload dari dashboard tersimpan di `admin/uploads/`; website sudah
  membacanya (`config/foto.php`). Produk tanpa foto memakai foto bawaan website.
- **Menu**: ditambah Foto & Logo, Lihat Website, dan Keluar.
- **Foto & Logo** (menu admin): ganti logo (header, footer, login) dan foto kategori. Foto kategori dipakai di bagian *Pilih Favoritmu* dan *Kenali Produk Kami* (sama).

## Halaman yang diisi (data dari pesanan website)
- **Dashboard**: ringkasan pendapatan/pesanan/produk/pelanggan, grafik 7 hari, pesanan terbaru, stok menipis.
- **Orders**: daftar pesanan, cari & filter status, ubah status (`prosesstatus.php`).
- **Customers**: pelanggan dari riwayat pesanan (per nomor telepon), tautan WhatsApp.

## Perubahan pada kode awal (seminimal mungkin)
Dashboard: `pages/product.php` (kolom Gambar jadi foto), `pages/koneksi.php` (nama database),
`index.php` (judul + perbaikan tanda kutip `src` bootstrap yang hilang),
`content.php` (rute foto), `menu.php`/`header.php` (menu & nama), dan satu baris
`require auth.php` di `index.php`, `prosesaddproduk.php`, `prosesedit.php`, `proseshapus.php`.
Ohara Bakery: `config/foto.php` (cek `admin/uploads/`) dan `includes/functions.php`
(deskripsi kosong tidak memicu peringatan).
