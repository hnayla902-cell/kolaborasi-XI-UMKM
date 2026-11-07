<?php
$judul = 'Keranjang Belanja';
include 'includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$items = [];
if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $res = $db->query("SELECT * FROM produk WHERE id IN ($ids)");
    while ($p = $res->fetch_assoc()) { $p['qty'] = $cart[$p['id']]; $items[] = $p; }
    foreach (array_keys($cart) as $cid) if (!in_array($cid, array_column($items, 'id'))) unset($_SESSION['cart'][$cid]);
}
?>
<div class="page-head" style="background-image:linear-gradient(rgba(35,18,8,.72),rgba(35,18,8,.72)),url('<?= e(foto('hero', 1600)) ?>')">
  <div class="container"><h1>Keranjang Belanja</h1><p class="mb-0 opacity-75">Pilih produk yang ingin dibeli, lalu lanjut ke pembayaran</p></div>
</div>

<section class="section pt-5">
  <div class="container-xl px-lg-5">
    <?php if (!$items): ?>
      <div class="cart-card text-center p-5 mx-auto" style="max-width:640px">
        <i class="bi bi-bag-x" style="font-size:4.5rem;color:var(--oranye)"></i>
        <h3 class="mt-3">Keranjangmu masih kosong</h3>
        <p class="text-muted fs-5">Yuk pilih roti dan camilan favoritmu dulu.</p>
        <a href="katalog.php" class="btn btn-ohara btn-lg">Lihat Katalog</a>
      </div>
    <?php else: ?>
    <form action="checkout.php" method="post" id="formPilih">
      <input type="hidden" name="id" id="hid" value="0">
      <input type="hidden" name="qty" id="hqty" value="1">
      <div class="row g-4">
        <div class="col-lg-8">
          <div class="cart-card p-4 p-lg-5">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
              <div class="form-check d-flex align-items-center gap-2 ps-0">
                <input class="form-check-input ms-0 item-all" style="width:1.6rem;height:1.6rem" type="checkbox" id="pilihSemua" checked>
                <label class="form-check-label fs-5 fw-semibold" for="pilihSemua">Pilih semua (<?= count($items) ?> produk)</label>
              </div>
              <button type="submit" formaction="cart_action.php" formnovalidate name="aksi" value="kosongkan" class="btn btn-outline-danger" onclick="return confirm('Kosongkan seluruh keranjang?')"><i class="bi bi-trash"></i> Kosongkan</button>
            </div>

            <?php foreach ($items as $p): ?>
            <div class="cart-item">
              <input class="form-check-input item-check" type="checkbox" name="pilih[]" value="<?= $p['id'] ?>" data-subtotal="<?= $p['harga'] * $p['qty'] ?>" checked>
              <img src="<?= e(foto_produk($p, 400)) ?>" class="cart-foto" alt="<?= e($p['nama']) ?>">
              <div>
                <div class="cart-nama"><?= e($p['nama']) ?></div>
                <span class="badge bg-warning text-dark mb-1"><?= e($p['kategori']) ?></span>
                <div class="cart-harga"><?= rupiah($p['harga']) ?> / item</div>
                <small class="text-muted">Stok tersedia: <?= (int)$p['stok'] ?></small>
              </div>
              <div class="qty-box">
                <button type="submit" formaction="cart_action.php" formnovalidate name="aksi" value="ubah" onclick="setQty(<?= $p['id'] ?>,<?= $p['qty'] - 1 ?>)" aria-label="Kurangi">−</button>
                <span><?= $p['qty'] ?></span>
                <button type="submit" formaction="cart_action.php" formnovalidate name="aksi" value="ubah" onclick="setQty(<?= $p['id'] ?>,<?= $p['qty'] + 1 ?>)" aria-label="Tambah">+</button>
              </div>
              <div class="cart-sub"><?= rupiah($p['harga'] * $p['qty']) ?></div>
              <button type="submit" formaction="cart_action.php" formnovalidate name="aksi" value="hapus" class="btn-hapus" onclick="setQty(<?= $p['id'] ?>,1)" title="Hapus"><i class="bi bi-trash"></i></button>
            </div>
            <?php endforeach; ?>
          </div>
          <a href="katalog.php" class="btn btn-link mt-3 fs-6"><i class="bi bi-arrow-left"></i> Lanjut belanja</a>
        </div>

        <div class="col-lg-4">
          <div class="cart-card p-4 p-lg-5 ringkasan">
            <h4 class="mb-4">Ringkasan Belanja</h4>
            <div class="d-flex justify-content-between fs-5 mb-2"><span>Produk dipilih</span><span id="jml" class="fw-semibold">0</span></div>
            <div class="d-flex justify-content-between fs-5 mb-2"><span>Ongkir</span><span class="text-muted">Dikonfirmasi admin</span></div>
            <hr class="my-3">
            <div class="d-flex justify-content-between align-items-center"><span class="fs-5">Total</span><span id="total" class="total">Rp 0</span></div>
            <button class="btn btn-ohara btn-lg w-100 mt-4 py-3 fs-5" id="btnBayar">Lanjut ke Pembayaran <i class="bi bi-arrow-right"></i></button>
            <p class="small text-muted text-center mt-3 mb-0"><i class="bi bi-shield-check"></i> Pembayaran aman. Bisa transfer, QRIS, atau COD.</p>
          </div>
        </div>
      </div>
    </form>
    <script>
    const checks = [...document.querySelectorAll('.item-check')];
    const rp = n => 'Rp ' + n.toLocaleString('id-ID');
    function hitung(){
      let t = 0, c = 0;
      checks.forEach(x => { if (x.checked) { t += +x.dataset.subtotal; c++; } });
      document.getElementById('total').textContent = rp(t);
      document.getElementById('jml').textContent = c + ' item';
      document.getElementById('btnBayar').disabled = c === 0;
      document.getElementById('pilihSemua').checked = c === checks.length;
    }
    checks.forEach(x => x.addEventListener('change', hitung));
    document.getElementById('pilihSemua').addEventListener('change', e => { checks.forEach(x => x.checked = e.target.checked); hitung(); });
    function setQty(id, q){ document.getElementById('hid').value = id; document.getElementById('hqty').value = Math.max(1, q); }
    hitung();
    </script>
    <?php endif; ?>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
