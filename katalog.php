<?php
$judul = 'Katalog Produk'; $halaman = 'katalog';
include 'includes/header.php';

$kategoriList = ['Aneka Roti', 'Hampers', 'Snack Box', 'Kripik Singkong'];
$kat = $_GET['kategori'] ?? '';
$q = trim($_GET['q'] ?? '');

$sql = "SELECT * FROM produk WHERE 1=1";
$types = ''; $params = [];
if (in_array($kat, $kategoriList)) { $sql .= " AND kategori = ?"; $types .= 's'; $params[] = $kat; }
if ($q !== '') { $sql .= " AND nama LIKE ?"; $types .= 's'; $params[] = "%$q%"; }
$sql .= " ORDER BY kategori, nama";
$stmt = $db->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$hasil = $stmt->get_result();
?>
<div class="page-head" style="background-image:linear-gradient(rgba(35,18,8,.72),rgba(35,18,8,.72)),url('<?= e(foto('hero', 1600)) ?>')">
  <div class="container"><h1>Katalog Produk</h1><p class="mb-0 opacity-75">Klik "Tambah ke Keranjang" pada produk yang kamu mau</p></div>
</div>
<section class="section pt-5">
  <div class="container">
    <form class="row g-2 justify-content-center mb-3" method="get">
      <?php if ($kat): ?><input type="hidden" name="kategori" value="<?= e($kat) ?>"><?php endif; ?>
      <div class="col-md-5"><input type="search" name="q" value="<?= e($q) ?>" class="form-control form-control-lg" placeholder="Cari produk..."></div>
      <div class="col-auto"><button class="btn btn-ohara btn-lg"><i class="bi bi-search"></i> Cari</button></div>
    </form>

    <div class="filter-pills text-center mb-4">
      <a href="katalog.php" class="btn btn-outline-ohara <?= $kat==''?'active':'' ?>">Semua</a>
      <?php foreach ($kategoriList as $k): ?>
        <a href="katalog.php?kategori=<?= urlencode($k) ?>" class="btn btn-outline-ohara <?= $kat==$k?'active':'' ?>"><?= $k ?></a>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> <?= e($_SESSION['flash']) ?> <a href="keranjang.php" class="alert-link">Lihat keranjang</a><button class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php unset($_SESSION['flash']); endif; ?>

    <div class="row g-4">
      <?php if ($hasil->num_rows === 0): ?>
        <div class="col-12 text-center text-muted py-5">Produk tidak ditemukan.</div>
      <?php else: while ($p = $hasil->fetch_assoc()) echo kartu_produk($p); endif; ?>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
