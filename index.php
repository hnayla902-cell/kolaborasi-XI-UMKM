<?php
$judul = 'Beranda'; $halaman = 'beranda';
include 'includes/header.php';
$unggulan = $db->query("SELECT * FROM produk WHERE stok > 0 ORDER BY RAND() LIMIT 8");
?>
<!-- HERO SECTION -->
<section class="hero" style="background-image:url('<?= e(foto('hero', 1800)) ?>')">
  <div class="container">
    <div class="row"><div class="col-lg-7">
      <span class="hero-tag">Dipanggang fresh setiap hari</span>
      <h1 class="mt-3">Roti Rumahan <em>Lembut &amp; Wangi</em> untuk Setiap Momen</h1>
      <p class="lead my-4">Aneka roti, hampers, snack box, dan kripik singkong buatan UMKM Ohara Bakery. Bahan pilihan, tanpa pengawet, langsung diantar ke rumahmu.</p>
      <a href="katalog.php" class="btn btn-ohara btn-lg me-2">Pesan Sekarang <i class="bi bi-arrow-right"></i></a>
      <a href="https://wa.me/<?= WA_NOMOR ?>" target="_blank" class="btn btn-wa btn-lg"><i class="bi bi-whatsapp"></i> Chat WhatsApp</a>
      <div class="hero-info">
        <div><strong>500+</strong>Pelanggan puas</div>
        <div><strong>20+</strong>Varian produk</div>
        <div><strong>Setiap hari</strong>Dipanggang fresh</div>
      </div>
    </div></div>
  </div>
</section>

<!-- Keunggulan -->
<div class="container">
  <div class="strip">
    <div class="row g-3">
      <div class="col-6 col-lg-3"><div class="item"><i class="bi bi-patch-check"></i><div><strong>Bahan Pilihan</strong><small>Tanpa pengawet</small></div></div></div>
      <div class="col-6 col-lg-3"><div class="item"><i class="bi bi-fire"></i><div><strong>Selalu Fresh</strong><small>Dipanggang harian</small></div></div></div>
      <div class="col-6 col-lg-3"><div class="item"><i class="bi bi-truck"></i><div><strong>Bisa Diantar</strong><small>Area Salatiga</small></div></div></div>
      <div class="col-6 col-lg-3"><div class="item"><i class="bi bi-wallet2"></i><div><strong>Harga Bersahabat</strong><small>Mulai Rp 4.000</small></div></div></div>
    </div>
  </div>
</div>

<!-- BERANDA: kategori + produk pilihan -->
<section class="section" id="beranda">
  <div class="container">
    <div class="text-center mb-5">
      <div class="eyebrow">Kategori</div>
      <h2 class="section-title">Pilih Favoritmu</h2><div class="title-line"></div>
    </div>
    <div class="row g-4 mb-5">
      <?php
      $kat = [
        'Aneka Roti'      => ['roti2',   'Roti tawar, sobek, croissant, donat'],
        'Hampers'         => ['hampers', 'Hadiah istimewa & parcel'],
        'Snack Box'       => ['kue',     'Praktis untuk rapat & acara'],
        'Kripik singkong' => ['roti4',   'Renyah, gurih, aneka rasa'],
      ];
      foreach ($kat as $nama => [$f, $ket]): ?>
      <div class="col-6 col-lg-3">
        <a class="kat-photo" href="katalog.php?kategori=<?= urlencode($nama) ?>">
          <img src="<?= e(foto_kategori($nama, $f, 700)) ?>" alt="<?= e($nama) ?>" loading="lazy">
          <div class="teks"><h5><?= $nama ?></h5><small><?= $ket ?></small></div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mb-4">
      <div class="eyebrow">Terlaris</div>
      <h2 class="section-title">Produk Pilihan</h2><div class="title-line"></div>
    </div>
    <div class="row g-4">
      <?php while ($p = $unggulan->fetch_assoc()) echo kartu_produk($p); ?>
    </div>
    <div class="text-center mt-5"><a href="katalog.php" class="btn btn-ohara btn-lg">Lihat Semua Produk</a></div>
  </div>
</section>

<!-- TENTANG UMKM -->
<section class="section section-alt" id="tentang">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 position-relative">
        <img class="about-img" src="<?= e(foto('tentang', 1000)) ?>" alt="Roti buatan Ohara Bakery" loading="lazy">
        <div class="about-badge"><strong>Sejak 2026</strong>Dibuat dengan hati</div>
      </div>
      <div class="col-lg-6">
        <div class="eyebrow">Tentang UMKM</div>
        <h2 class="section-title">Dari Dapur Rumahan untuk Keluarga Indonesia</h2>
        <p class="mt-3">Ohara Bakery adalah usaha mikro yang berawal dari dapur rumahan dengan satu tujuan sederhana: menghadirkan roti dan camilan yang enak, higienis, dan terjangkau untuk semua orang.</p>
        <p>Kami memakai bahan pilihan tanpa pengawet berbahaya, diolah dengan resep keluarga yang terus disempurnakan. Dari roti sarapan, hampers hadiah, snack box acara, sampai kripik singkong untuk teman santai, semuanya dibuat dengan penuh perhatian.</p>
        <ul class="list-unstyled">
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Bahan berkualitas, tanpa pengawet berbahaya</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Dapur bersih dan higienis</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Menerima pesanan custom untuk acara</li>
        </ul>
        <div class="row text-center mt-4">
          <div class="col-4"><div class="stat">500+</div><small class="text-muted">Pelanggan</small></div>
          <div class="col-4"><div class="stat">20+</div><small class="text-muted">Varian</small></div>
          <div class="col-4"><div class="stat">4,9</div><small class="text-muted">Rating</small></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- INFORMASI PRODUK -->
<section class="section" id="informasi">
  <div class="container">
    <div class="text-center mb-5">
      <div class="eyebrow">Informasi Produk</div>
      <h2 class="section-title">Kenali Produk Kami</h2><div class="title-line"></div>
    </div>
    <div class="row g-4">
      <?php
      $info = [
        ['Aneka Roti',      'roti3',   'Mulai Rp 5.000',   ['Roti tawar, roti sobek, croissant, donat', 'Dipanggang setiap pagi', 'Tahan 2–3 hari di suhu ruang']],
        ['Hampers',         'hampers', 'Mulai Rp 165.000', ['Cocok untuk lebaran, ulang tahun, hadiah', 'Dikemas rapi dalam box cantik', 'Isi bisa custom sesuai budget']],
        ['Snack Box',       'kue',     'Mulai Rp 25.000',  ['Untuk rapat, arisan, pengajian', 'Minimal pemesanan 10 box', 'Pesan H-1 untuk jumlah besar']],
        ['Kripik Singkong', 'roti2',   'Mulai Rp 20.000',  ['Varian original, balado, keju', 'Kemasan 250 gram', 'Tahan hingga 1 bulan']],
      ];
      foreach ($info as [$nama, $f, $harga, $poin]): ?>
      <div class="col-md-6 col-lg-3">
        <div class="info-card">
          <img src="<?= e(foto_kategori($nama, foto_kategori_kunci($nama, $f), 600)) ?>" alt="<?= e($nama) ?>" loading="lazy">
          <div class="isi">
            <h5 class="fw-bold"><?= $nama ?></h5>
            <ul><?php foreach ($poin as $x) echo '<li>' . $x . '</li>'; ?></ul>
            <span class="badge bg-warning text-dark"><?= $harga ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CARA PESAN -->
<section class="section section-alt">
  <div class="container">
    <div class="text-center mb-5">
      <div class="eyebrow">Mudah</div>
      <h2 class="section-title">Cara Memesan</h2><div class="title-line"></div>
    </div>
    <div class="row g-4">
      <div class="col-6 col-lg-3"><div class="langkah"><div class="no">1</div><h6>Pilih Produk</h6><small class="text-muted">Lihat katalog dan klik tambah ke keranjang.</small></div></div>
      <div class="col-6 col-lg-3"><div class="langkah"><div class="no">2</div><h6>Cek Keranjang</h6><small class="text-muted">Pilih produk dan atur jumlahnya.</small></div></div>
      <div class="col-6 col-lg-3"><div class="langkah"><div class="no">3</div><h6>Isi Data &amp; Bayar</h6><small class="text-muted">Transfer, QRIS, atau bayar di tempat.</small></div></div>
      <div class="col-6 col-lg-3"><div class="langkah"><div class="no">4</div><h6>Pesanan Diantar</h6><small class="text-muted">Kami proses dan antar ke alamatmu.</small></div></div>
    </div>
  </div>
</section>

<!-- TESTIMONI -->
<section class="section">
  <div class="container">
    <div class="text-center mb-5">
      <div class="eyebrow">Testimoni</div>
      <h2 class="section-title">Kata Pelanggan</h2><div class="title-line"></div>
    </div>
    <div class="row g-4">
      <?php foreach ([
        ['Rina S.', 'Roti sobeknya empuk banget, anak-anak suka. Sudah langganan untuk sarapan.'],
        ['Pak Budi', 'Pesan snack box untuk rapat kantor, rapi dan tepat waktu. Rasanya enak.'],
        ['Dewi A.', 'Hampers lebaran dari Ohara cantik dan isinya lengkap. Keluarga senang.'],
      ] as [$n, $t]): ?>
      <div class="col-md-4"><div class="testi">
        <div class="bintang mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
        <p>“<?= $t ?>”</p><strong>– <?= $n ?></strong>
      </div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
