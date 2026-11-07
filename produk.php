<?php $title='Katalog Produk'; require 'includes/header.php';
$kat = $_GET['kat'] ?? ''; $q = trim($_GET['q'] ?? '');
$sql='SELECT * FROM products WHERE 1'; $args=[];
if(in_array($kat,KATEGORI)){ $sql.=' AND kategori=?'; $args[]=$kat; }
if($q!==''){ $sql.=' AND (nama LIKE ? OR deskripsi LIKE ?)'; $args[]="%$q%"; $args[]="%$q%"; }
$s=$db->prepare($sql.' ORDER BY id DESC'); $s->execute($args); $rows=$s->fetchAll(); ?>
<div class="container page-top">
  <h1>Katalog Produk</h1>
  <?php if(!empty($_GET['ok'])): ?><div class="alert alert-success py-2">Produk masuk ke keranjang. <a href="cart.php">Lihat keranjang</a></div><?php endif; ?>
  <div class="tabs-row mt-3">
    <a class="<?= $kat===''?'active':'' ?>" href="produk.php">Semua</a>
    <?php foreach(KATEGORI as $k): ?><a class="<?= $kat===$k?'active':'' ?>" href="produk.php?kat=<?= urlencode($k) ?>"><?= e($k) ?></a><?php endforeach; ?>
    <form method="get"><?php if($kat): ?><input type="hidden" name="kat" value="<?= e($kat) ?>"><?php endif; ?><input name="q" value="<?= e($q) ?>" placeholder="Cari produk"></form>
  </div>
  <div class="row g-4">
    <?php foreach($rows as $p) include 'includes/card.php'; ?>
    <?php if(!$rows): ?><p class="text-muted">Produk tidak ditemukan.</p><?php endif; ?>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
