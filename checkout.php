<?php
$judul = 'Pembayaran';
require_once 'includes/functions.php';

// Produk terpilih dari keranjang (disimpan di session agar tetap ada saat submit form)
if (isset($_POST['pilih']) && !isset($_POST['proses'])) {
    $_SESSION['checkout'] = array_values(array_filter(array_map('intval', (array)$_POST['pilih'])));
}
$pilih = $_SESSION['checkout'] ?? [];
$cart = $_SESSION['cart'] ?? [];
$pilih = array_values(array_filter($pilih, fn($id) => isset($cart[$id])));
if (!$pilih) { header('Location: keranjang.php'); exit; }

$ids = implode(',', array_map('intval', $pilih));
$items = []; $total = 0;
$res = $db->query("SELECT * FROM produk WHERE id IN ($ids)");
while ($p = $res->fetch_assoc()) {
    $p['qty'] = min($cart[$p['id']], max(0, $p['stok']));
    if ($p['qty'] < 1) continue;
    $p['sub'] = $p['qty'] * $p['harga'];
    $total += $p['sub'];
    $items[] = $p;
}
if (!$items) { header('Location: keranjang.php'); exit; }

$ongkir = 0;
$metodeList = [
    'Transfer Bank BCA'     => ['bi-bank',   'Rekening BCA 1234567890 a.n. Ohara Bakery'],
    'QRIS'                  => ['bi-qr-code','Scan QRIS, bisa semua e-wallet dan m-banking'],
    'COD (Bayar di Tempat)' => ['bi-cash-coin','Bayar tunai saat pesanan tiba'],
];
$error = '';

if (isset($_POST['proses'])) {
    $nama = trim($_POST['nama'] ?? ''); $telp = trim($_POST['telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? ''); $catatan = trim($_POST['catatan'] ?? '');
    $metode = $_POST['metode'] ?? '';
    if ($nama === '' || $telp === '' || $alamat === '' || !isset($metodeList[$metode])) {
        $error = 'Mohon lengkapi nama, telepon, alamat, dan pilih metode pembayaran.';
    } elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', $telp)) {
        $error = 'Nomor telepon tidak valid.';
    } else {
        $db->begin_transaction();
        try {
            $kode = 'OB' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
            $grand = $total + $ongkir;
            $stmt = $db->prepare("INSERT INTO pesanan (kode,nama,telepon,alamat,catatan,metode,total) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param('ssssssi', $kode, $nama, $telp, $alamat, $catatan, $metode, $grand);
            $stmt->execute();
            $pid = $db->insert_id;
            $ins = $db->prepare("INSERT INTO pesanan_item (pesanan_id,produk_id,nama,harga,qty) VALUES (?,?,?,?,?)");
            $upd = $db->prepare("UPDATE produk SET stok = stok - ? WHERE id = ? AND stok >= ?");
            foreach ($items as $it) {
                $ins->bind_param('iisii', $pid, $it['id'], $it['nama'], $it['harga'], $it['qty']);
                $ins->execute();
                $upd->bind_param('iii', $it['qty'], $it['id'], $it['qty']);
                $upd->execute();
                if ($upd->affected_rows < 1) throw new Exception('Stok ' . $it['nama'] . ' tidak mencukupi.');
                unset($_SESSION['cart'][$it['id']]);
            }
            $db->commit();
            unset($_SESSION['checkout']);
            header('Location: sukses.php?kode=' . $kode);
            exit;
        } catch (Exception $ex) {
            $db->rollback();
            $error = 'Pesanan gagal: ' . $ex->getMessage();
        }
    }
}
$halaman = '';
include 'includes/header.php';
?>
<div class="page-head" style="background-image:linear-gradient(rgba(35,18,8,.72),rgba(35,18,8,.72)),url('<?= e(foto('hero', 1600)) ?>')">
  <div class="container"><h1>Pembayaran</h1><p class="mb-0 opacity-75">Isi data pengiriman dan pilih metode pembayaran</p></div>
</div>
<section class="section pt-5">
  <div class="container-xl px-lg-5">
    <?php if ($error): ?><div class="alert alert-danger fs-6"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="row g-4">
      <input type="hidden" name="proses" value="1">
      <div class="col-lg-7">
        <div class="cart-card p-4 p-lg-5 mb-4">
          <h4 class="mb-4">Data Pemesan</h4>
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input name="nama" class="form-control form-control-lg" required value="<?= e($_POST['nama'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label">No. WhatsApp / Telepon</label><input name="telepon" class="form-control form-control-lg" required value="<?= e($_POST['telepon'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label">Alamat Pengiriman</label><textarea name="alamat" rows="3" class="form-control form-control-lg" required><?= e($_POST['alamat'] ?? '') ?></textarea></div>
            <div class="col-12"><label class="form-label">Catatan (opsional)</label><input name="catatan" class="form-control form-control-lg" placeholder="Contoh: tulisan di hampers, jam kirim" value="<?= e($_POST['catatan'] ?? '') ?>"></div>
          </div>
        </div>
        <div class="cart-card p-4 p-lg-5">
          <h4 class="mb-4">Metode Pembayaran</h4>
          <?php $i = 0; foreach ($metodeList as $m => [$ic, $ket]): $i++; ?>
            <input type="radio" class="btn-check" name="metode" id="m<?= $i ?>" value="<?= e($m) ?>" <?= (($_POST['metode'] ?? '') === $m) ? 'checked' : '' ?> required>
            <label class="pay-option mb-3" for="m<?= $i ?>">
              <div class="d-flex align-items-center gap-3">
                <i class="bi <?= $ic ?> fs-2" style="color:var(--oranye)"></i>
                <div><strong class="fs-5"><?= e($m) ?></strong><br><small class="text-muted"><?= e($ket) ?></small></div>
              </div>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="cart-card p-4 p-lg-5 ringkasan">
          <h4 class="mb-4">Ringkasan Pesanan</h4>
          <?php foreach ($items as $it): ?>
            <div class="d-flex align-items-center gap-3 mb-3">
              <img src="<?= e(foto_produk($it, 200)) ?>" class="sum-foto" alt="">
              <div class="flex-grow-1"><div class="fw-semibold"><?= e($it['nama']) ?></div><small class="text-muted"><?= $it['qty'] ?> × <?= rupiah($it['harga']) ?></small></div>
              <div class="fw-semibold"><?= rupiah($it['sub']) ?></div>
            </div>
          <?php endforeach; ?>
          <hr>
          <div class="d-flex justify-content-between"><span>Subtotal</span><span><?= rupiah($total) ?></span></div>
          <div class="d-flex justify-content-between"><span>Ongkir</span><span class="text-muted">Dikonfirmasi admin</span></div>
          <div class="d-flex justify-content-between align-items-center mt-3"><span class="fs-5">Total</span><span class="total"><?= rupiah($total + $ongkir) ?></span></div>
          <button class="btn btn-ohara btn-lg w-100 mt-4 py-3 fs-5"><i class="bi bi-bag-check"></i> Buat Pesanan</button>
          <a href="keranjang.php" class="btn btn-link w-100 mt-1"><i class="bi bi-arrow-left"></i> Kembali ke keranjang</a>
        </div>
      </div>
    </form>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
