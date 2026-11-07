<?php
$judul = 'Pesanan Berhasil';
include 'includes/header.php';
$kode = $_GET['kode'] ?? '';
$stmt = $db->prepare("SELECT * FROM pesanan WHERE kode = ?");
$stmt->bind_param('s', $kode);
$stmt->execute();
$o = $stmt->get_result()->fetch_assoc();
if (!$o) { echo '<div class="container py-5 text-center">Pesanan tidak ditemukan. <a href="index.php">Kembali</a></div>'; include 'includes/footer.php'; exit; }
$it = $db->query("SELECT * FROM pesanan_item WHERE pesanan_id = " . (int)$o['id']);
$pesanWa = rawurlencode('Halo Ohara Bakery, saya sudah membuat pesanan dengan kode ' . $o['kode'] . ' atas nama ' . $o['nama'] . '.');
?>
<section class="section pt-5">
  <div class="container" style="max-width:760px">
    <div class="cart-card p-4 p-md-5 text-center">
      <i class="bi bi-check-circle-fill" style="font-size:4.5rem;color:var(--hijau)"></i>
      <h2 class="mt-2">Terima kasih, <?= e($o['nama']) ?>!</h2>
      <p class="text-muted fs-5">Pesananmu sudah kami terima. Kode pesanan:</p>
      <div class="display-6 fw-bold" style="color:var(--oranye)"><?= e($o['kode']) ?></div>
      <hr class="my-4">
      <div class="text-start fs-5">
        <?php while ($r = $it->fetch_assoc()): ?>
          <div class="d-flex justify-content-between mb-1"><span><?= e($r['nama']) ?> × <?= $r['qty'] ?></span><span><?= rupiah($r['harga'] * $r['qty']) ?></span></div>
        <?php endwhile; ?>
        <div class="d-flex justify-content-between fw-bold fs-4 mt-3"><span>Total</span><span class="price fs-4"><?= rupiah($o['total']) ?></span></div>
        <div class="mt-3 small">Metode: <strong><?= e($o['metode']) ?></strong> &bull; Status: <span class="badge bg-warning text-dark"><?= e($o['status']) ?></span></div>
      </div>
      <div class="alert alert-warning text-start mt-4 mb-0">
        <?php if (strpos($o['metode'], 'BCA') !== false): ?>Silakan transfer <strong><?= rupiah($o['total']) ?></strong> ke <strong>BCA 1234567890 a.n. Ohara Bakery</strong>, lalu kirim bukti transfer lewat WhatsApp dengan menyebut kode pesanan.
        <?php elseif ($o['metode'] === 'QRIS'): ?>Admin akan mengirim kode QRIS ke WhatsApp kamu. Kirim bukti pembayaran dengan menyebut kode pesanan.
        <?php else: ?>Siapkan uang tunai <strong><?= rupiah($o['total']) ?></strong> saat pesanan tiba. Admin akan menghubungi untuk konfirmasi.<?php endif; ?>
      </div>
      <div class="d-flex gap-2 justify-content-center flex-wrap mt-4">
        <a href="https://wa.me/<?= WA_NOMOR ?>?text=<?= $pesanWa ?>" target="_blank" class="btn btn-wa btn-lg"><i class="bi bi-whatsapp"></i> Konfirmasi via WhatsApp</a>
        <a href="katalog.php" class="btn btn-ohara btn-lg">Belanja Lagi</a>
      </div>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
