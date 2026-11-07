<?php
require_once 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: katalog.php'); exit; }

$aksi = $_POST['aksi'] ?? '';
$id = (int)($_POST['id'] ?? 0);
$kembali = $_POST['kembali'] ?? 'katalog.php';
// Cegah open redirect: ambil hanya nama file + query, selalu relatif ke folder ini
$u = parse_url($kembali);
$file = basename($u['path'] ?? '');
$kembali = preg_match('/^[a-z_]+\.php$/i', $file) ? $file . (isset($u['query']) ? '?' . $u['query'] : '') : 'katalog.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($aksi === 'tambah' && $id > 0) {
    $stmt = $db->prepare("SELECT nama, stok FROM produk WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    if ($p && $p['stok'] > 0) {
        $_SESSION['cart'][$id] = min(($_SESSION['cart'][$id] ?? 0) + 1, $p['stok']);
        $_SESSION['flash'] = '"' . $p['nama'] . '" ditambahkan ke keranjang.';
    }
} elseif ($aksi === 'ubah' && $id > 0) {
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    $stmt = $db->prepare("SELECT stok FROM produk WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    if ($p && isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] = min($qty, max(1, $p['stok']));
    $kembali = 'keranjang.php';
} elseif ($aksi === 'hapus' && $id > 0) {
    unset($_SESSION['cart'][$id]);
    $kembali = 'keranjang.php';
} elseif ($aksi === 'kosongkan') {
    $_SESSION['cart'] = [];
    $kembali = 'keranjang.php';
}
header('Location: ' . $kembali);
exit;
