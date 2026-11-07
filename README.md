# Ohara Bakery – Website UMKM (PHP + Bootstrap 5 + MySQL)

## Cara menjalankan (XAMPP / Laragon)
1. Salin folder `ohara-bakery` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Nyalakan **Apache** dan **MySQL**.
3. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`) → tab **Import** → pilih `database.sql` → **Go**.
   Database `ohara_bakery` beserta 14 produk contoh akan terbuat.
4. Cek `config/koneksi.php` (default: user `root`, password kosong). Ubah jika berbeda.
5. Buka website: `http://localhost/ohara-bakery/`

## Login Admin
- URL: `http://localhost/ohara-bakery/admin/login.php`
- Username: `admin`  |  Password: `admin123`
  (akun dibuat otomatis saat pertama kali membuka halaman login — **segera ganti password** untuk penggunaan nyata)

## Fitur
- Halaman publik: Header, Hero, Beranda, Tentang UMKM, Informasi Produk, Katalog (filter + cari), Keranjang, Footer.
- Klik "Tambah ke Keranjang" → di keranjang pilih produk (checkbox) dan atur jumlah → lanjut ke Pembayaran.
- Pembayaran: isi data, pilih metode (Transfer BCA / QRIS / COD) → pesanan tersimpan di database, stok berkurang.
- Admin: tambah, edit, hapus produk (termasuk upload foto) dan kelola status pesanan.

## Struktur
```
ohara-bakery/
├── index.php, katalog.php, keranjang.php, checkout.php, sukses.php, cart_action.php
├── config/koneksi.php
├── includes/ (header, footer, functions)
├── admin/ (login, index, produk_form, hapus, pesanan, logout)
├── assets/css/style.css
├── uploads/            ← foto produk (otomatis terisi dari admin)
└── database.sql
```

## Foto produk
- Foto bawaan diambil dari Unsplash (lisensi bebas pakai) lewat internet, jadi komputer harus online agar foto tampil. Daftarnya ada di `config/foto.php`.
- Untuk foto produk asli: Admin → Edit Produk → Foto Produk. Foto yang diupload otomatis menggantikan foto bawaan.
- Foto bawaan untuk Kripik Singkong dan Snack Box hanya pengganti (foto kue/roti), sebaiknya diganti foto produk sendiri.

## Kustomisasi cepat
- Nomor WhatsApp: ubah konstanta `WA_NOMOR` dan `WA_TAMPIL` di `includes/functions.php`.
- Alamat dan rekening: ubah di `includes/footer.php`, `checkout.php`, `sukses.php`.
- Warna tema: variabel di bagian atas `assets/css/style.css`.
