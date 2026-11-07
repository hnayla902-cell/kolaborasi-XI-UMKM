<?php
session_start();

// ====== PENGATURAN DATABASE (phpMyAdmin / MySQL) ======
define('DB_HOST', 'localhost');
define('DB_NAME', 'ohara_bakery');
define('DB_USER', 'root');
define('DB_PASS', '');

// ====== LOGIN ADMIN (ganti sebelum dipakai online) ======
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'ohara123');

// Nomor WhatsApp & rekening
define('WA_NUMBER', '6281234567890');
define('REKENING', 'BCA 1234567890 a.n. Ohara Bakery');

try {
    $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Koneksi database gagal. Import database.sql lewat phpMyAdmin dan cek config.php.');
}

const KATEGORI = ['Roti','Hampers','Snack Box','Kripik Singkong'];

function rp($n){ return 'Rp '.number_format($n,0,',','.'); }
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Foto produk: pakai file upload admin; jika kosong, pakai foto stok sesuai kategori
function foto($p, $w=600, $h=450){
    if (!empty($p['gambar']) && file_exists(__DIR__.'/uploads/'.$p['gambar'])) return 'uploads/'.e($p['gambar']);
    $kw = ['Roti'=>'bread,bakery','Hampers'=>'gift,basket,cookies','Snack Box'=>'pastry,snack','Kripik Singkong'=>'chips,snack'];
    return "https://loremflickr.com/$w/$h/".($kw[$p['kategori']] ?? 'bread')."?lock=".(int)$p['id'];
}
function stock($kw,$w,$h,$lock=1){ return "https://loremflickr.com/$w/$h/$kw?lock=$lock"; }

function cart_count(){ return array_sum($_SESSION['cart'] ?? []); }
function cart_total($db){
    $t=0; foreach(($_SESSION['cart'] ?? []) as $id=>$q){
        $s=$db->prepare('SELECT harga FROM products WHERE id=?'); $s->execute([$id]);
        if($r=$s->fetch()) $t+=$r['harga']*$q;
    } return $t;
}
