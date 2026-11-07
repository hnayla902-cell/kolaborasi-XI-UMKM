<?php require_once 'config.php';
$_SESSION['cart'] = $_SESSION['cart'] ?? [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $a=$_POST['action'] ?? ''; $id=(int)($_POST['id'] ?? 0);
    if($a==='add' && $id){
        $s=$db->prepare('SELECT stok FROM products WHERE id=?'); $s->execute([$id]);
        if($r=$s->fetch()) $_SESSION['cart'][$id]=min($r['stok'],($_SESSION['cart'][$id] ?? 0)+1);
        $back=$_POST['back'] ?? 'produk.php';
        if(!preg_match('/^[\w\-\.\/\?=&%]+$/',$back) || strpos($back,'//')!==false) $back='produk.php';
        header('Location: '.$back.(strpos($back,'?')?'&':'?').'ok=1'); exit;
    }
    if($a==='update' && $id){ $q=max(0,(int)$_POST['qty']); if($q) $_SESSION['cart'][$id]=$q; else unset($_SESSION['cart'][$id]); }
    if($a==='remove' && $id) unset($_SESSION['cart'][$id]);
    header('Location: cart.php'); exit;
}
$title='Keranjang'; $page='cart'; require 'includes/header.php';
$items=[];
foreach($_SESSION['cart'] as $id=>$q){ $s=$db->prepare('SELECT * FROM products WHERE id=?'); $s->execute([$id]);
  if($p=$s->fetch()){ $p['qty']=$q; $items[]=$p; } else unset($_SESSION['cart'][$id]); }
?>
<div class="container page-top pb-5">
  <h1>Keranjang Belanja</h1>
  <?php if(!$items): ?>
    <div class="panel text-center py-5 mt-3"><p class="mb-3">Keranjang masih kosong. Pilih produk favoritmu dulu.</p><a class="btn btn-green px-4" href="produk.php">Lihat katalog</a></div>
  <?php else: ?>
  <form method="post" action="checkout.php" id="selForm">
  <div class="row g-4 mt-1">
    <div class="col-lg-8"><div class="panel">
      <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="all" checked><label class="form-check-label fw-semibold" for="all">Pilih semua produk</label></div>
      <?php foreach($items as $p): ?>
      <div class="d-flex align-items-center gap-3 py-3 border-top flex-wrap">
        <input class="form-check-input sel" type="checkbox" name="pilih[]" value="<?= $p['id'] ?>" data-sub="<?= $p['harga']*$p['qty'] ?>" checked>
        <img class="cart-img" src="<?= foto($p,200,160) ?>" alt="<?= e($p['nama']) ?>">
        <div class="flex-grow-1"><b><?= e($p['nama']) ?></b><div class="small text-muted"><?= e($p['kategori']) ?> &middot; <?= rp($p['harga']) ?></div></div>
        <div class="d-flex gap-2 align-items-center">
          <input form="u<?= $p['id'] ?>" type="number" name="qty" min="0" max="<?= $p['stok'] ?>" value="<?= $p['qty'] ?>" class="form-control form-control-sm" style="width:70px" onchange="this.form.submit()">
          <button form="r<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger">Hapus</button>
        </div>
        <div class="fw-bold text-success" style="min-width:100px;text-align:right"><?= rp($p['harga']*$p['qty']) ?></div>
      </div>
      <?php endforeach; ?>
    </div></div>
    <div class="col-lg-4"><div class="panel">
      <h5 class="fw-bold">Ringkasan</h5>
      <div class="d-flex justify-content-between"><span>Produk dipilih</span><span id="cnt">0</span></div>
      <div class="d-flex justify-content-between fs-5 fw-bold my-2"><span>Total</span><span class="text-success" id="tot">Rp 0</span></div>
      <button class="btn btn-green w-100 py-2" id="goPay">Lanjut ke pembayaran</button>
      <a href="produk.php" class="btn btn-link w-100 mt-1">Tambah produk lain</a>
    </div></div>
  </div>
  </form>
  <?php foreach($items as $p): ?>
    <form id="u<?= $p['id'] ?>" method="post"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= $p['id'] ?>"></form>
    <form id="r<?= $p['id'] ?>" method="post"><input type="hidden" name="action" value="remove"><input type="hidden" name="id" value="<?= $p['id'] ?>"></form>
  <?php endforeach; ?>
  <script>
  const sels=[...document.querySelectorAll('.sel')],all=document.getElementById('all');
  function calc(){let t=0,c=0;sels.forEach(s=>{if(s.checked){t+=+s.dataset.sub;c++}});
    document.getElementById('tot').textContent='Rp '+t.toLocaleString('id-ID');
    document.getElementById('cnt').textContent=c;document.getElementById('goPay').disabled=c===0;}
  sels.forEach(s=>s.addEventListener('change',()=>{all.checked=sels.every(x=>x.checked);calc()}));
  all.addEventListener('change',()=>{sels.forEach(s=>s.checked=all.checked);calc()});calc();
  </script>
  <?php endif; ?>
</div>
<?php require 'includes/footer.php'; ?>
