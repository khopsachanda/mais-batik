<?php

declare(strict_types=1);

use MaisBatik\Models\ProductRepository;

$projectRoot = dirname(__DIR__);
require $projectRoot . '/models/ProductRepository.php';

function catalogEscape(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$products = [];
$catalogError = null;

try {
    if (!is_file($projectRoot . '/config/database.php')) {
        throw new RuntimeException('Missing config/database.php.');
    }

    $pdo = require $projectRoot . '/config/connection.php';
    $repository = new ProductRepository($pdo);
    $products = array_values(array_filter(
        $repository->findAll(),
        static fn (array $product): bool => $product['status'] === 'active'
    ));
} catch (PDOException | RuntimeException $exception) {
    error_log('Mais Batik catalog database request failed ('
        . $exception::class . ', code ' . $exception->getCode() . ').');
    http_response_code(503);
    $catalogError = 'Katalog belum dapat dimuat. Pastikan database dan konfigurasi aplikasi sudah disiapkan.';
}

$categoryLabels = ['pria' => 'Batik pria', 'anak' => 'Batik anak', 'wanita' => 'Batik wanita'];
$categories = [];
foreach ($products as $product) {
    $categories[$product['category']] = $categoryLabels[$product['category']] ?? $product['category'];
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Lihat katalog produk batik Mais Batik yang tersedia.">
    <meta name="theme-color" content="#f8f5ed">
    <title>Katalog Produk — Mais Batik</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/theme.js" defer></script>
    <script src="assets/js/main.js" defer></script>
  </head>
  <body>
    <a class="skip-link" href="#konten-utama">Lewati ke konten utama</a>
    <header class="site-header" id="home">
      <div class="header-inner">
        <a class="brand" href="index.html" aria-label="Mais Batik, ke halaman utama">
          <span class="brand-mark" aria-hidden="true">M</span>
          <span class="brand-name">mais<span>batik</span></span>
        </a>
        <nav class="primary-navigation" id="primary-navigation" aria-label="Navigasi utama">
          <a class="nav-link" href="index.html#home">Home</a>
          <a class="nav-link" href="katalog.php" aria-current="page">Katalog</a>
          <a class="nav-link" href="index.html#order">Order</a>
          <a class="nav-link" href="index.html#booking">Booking</a>
          <a class="nav-link nav-system" href="index.html#pos">POS</a>
          <a class="nav-login" href="admin/">Login Admin <span aria-hidden="true">↗</span></a>
        </nav>
        <div class="header-tools">
          <button class="theme-toggle" type="button" data-theme-toggle aria-label="Aktifkan mode gelap" aria-pressed="false" title="Aktifkan mode gelap">
            <span class="theme-icon" aria-hidden="true">☾</span>
          </button>
          <button class="menu-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false" aria-controls="primary-navigation">
            <span></span><span></span><span></span>
          </button>
        </div>
      </div>
    </header>

    <main id="konten-utama">
      <section class="catalog section-shell" id="katalog" aria-labelledby="catalog-title">
        <div class="catalog-heading">
          <div>
            <p class="eyebrow"><span></span> Koleksi Mais Batik</p>
            <h1 id="catalog-title">Temukan batik <em>favoritmu.</em></h1>
            <p class="catalog-description">Pilihan batik untuk menemani hari istimewa maupun keseharian.</p>
          </div>
          <span class="catalog-note">Dikelola dengan<br><strong>cerita dan rasa</strong></span>
        </div>

        <?php if ($catalogError !== null): ?>
          <div class="catalog-error" role="alert">
            <p class="eyebrow">Katalog belum tersedia</p>
            <p><?= catalogEscape($catalogError) ?></p>
          </div>
        <?php else: ?>
          <div class="catalog-tools" aria-label="Filter katalog produk">
            <label class="search-field">
              <span class="visually-hidden">Cari nama produk</span>
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="10.8" cy="10.8" r="6.8"></circle>
                <path d="m16 16 4.5 4.5"></path>
              </svg>
              <input id="product-search" type="search" placeholder="Cari produk..." autocomplete="off">
            </label>
            <label class="category-field">
              <span>Kategori</span>
              <select id="category-filter" aria-label="Filter berdasarkan kategori">
                <option value="semua">Semua kategori</option>
                <?php foreach ($categories as $category => $label): ?>
                  <option value="<?= catalogEscape($category) ?>"><?= catalogEscape($label) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>

          <div class="catalog-summary">
            <p id="product-count" aria-live="polite">Menampilkan <?= count($products) ?> produk</p>
            <p>Stok dan harga mengikuti data katalog.</p>
          </div>

          <div class="product-grid" id="product-grid">
            <?php foreach ($products as $product): ?>
              <?php
              $imagePath = (string) $product['image_path'];
              $safeImagePath = preg_match('/^assets\/images\/products\/[A-Za-z0-9._-]+\.jpg$/iD', $imagePath) === 1
                  && is_file(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imagePath));
              $searchText = mb_strtolower(
                  implode(' ', [$product['name'], $product['category'], $product['variant']]),
                  'UTF-8'
              );
              $inStock = (int) $product['stock'] > 0;
              $priceText = 'Rp' . number_format((int) $product['price_min'], 0, ',', '.')
                  . ' – Rp' . number_format((int) $product['price_max'], 0, ',', '.');
              $detail = sprintf(
                  '%s. Varian: %s. %s.',
                  $categoryLabels[$product['category']] ?? $product['category'],
                  $product['variant'],
                  $inStock ? 'Stok tersedia' : 'Stok sedang habis'
              );
              ?>
              <article class="product-card"
                data-category="<?= catalogEscape($product['category']) ?>"
                data-search="<?= catalogEscape($searchText) ?>">
                <div class="product-image">
                  <?php if ($safeImagePath): ?>
                    <img src="<?= catalogEscape($imagePath) ?>" alt="Foto <?= catalogEscape($product['name']) ?>" width="640" height="720" loading="lazy">
                  <?php else: ?>
                    <div class="missing-product-image" role="img" aria-label="Foto produk belum tersedia">Mais Batik</div>
                  <?php endif; ?>
                  <span class="product-category"><?= catalogEscape($categoryLabels[$product['category']] ?? $product['category']) ?></span>
                  <span class="stock-badge <?= $inStock ? '' : 'stock-sold-out' ?>">
                    <span aria-hidden="true"></span><?= $inStock ? 'Stok tersedia · ' . catalogEscape($product['stock']) : 'Stok habis' ?>
                  </span>
                </div>
                <div class="product-info">
                  <p class="product-variant"><?= catalogEscape($product['variant']) ?></p>
                  <h2><?= catalogEscape($product['name']) ?></h2>
                  <p class="product-price"><?= catalogEscape($priceText) ?></p>
                  <div class="product-actions">
                    <?php if ($inStock): ?>
                      <a class="product-order" href="mailto:info@maisbatik.example?subject=Pesan%20<?= rawurlencode((string) $product['name']) ?>">Pesan <span aria-hidden="true">→</span></a>
                    <?php else: ?>
                      <span class="product-order is-disabled" aria-disabled="true">Stok habis</span>
                    <?php endif; ?>
                    <button class="product-detail" type="button"
                      data-product="<?= catalogEscape($product['name']) ?>"
                      data-description="<?= catalogEscape($detail) ?>">Detail</button>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
          <p class="catalog-empty" id="catalog-empty" <?= $products === [] ? '' : 'hidden' ?>>
            <?= $products === [] ? 'Belum ada produk aktif di katalog. Silakan kembali lagi nanti.' : 'Produk tidak ditemukan. Coba kata kunci atau kategori lain.' ?>
          </p>
        <?php endif; ?>
      </section>
    </main>

    <dialog class="product-dialog" id="product-dialog" aria-labelledby="dialog-title">
      <form method="dialog"><button class="dialog-close" type="submit" aria-label="Tutup detail">×</button></form>
      <p class="eyebrow"><span></span> Detail produk</p>
      <h2 id="dialog-title"></h2>
      <p id="dialog-description"></p>
      <a class="button button-primary" id="dialog-order" href="mailto:info@maisbatik.example">Tanyakan produk <span aria-hidden="true">→</span></a>
    </dialog>

    <footer class="site-footer">
      <div class="footer-main">
        <div class="footer-brand">
          <a class="brand brand-footer" href="index.html#home" aria-label="Mais Batik, kembali ke halaman utama">
            <span class="brand-mark" aria-hidden="true">M</span>
            <span class="brand-name">mais<span>batik</span></span>
          </a>
          <p>Merawat cerita, merayakan budaya.</p>
        </div>
        <div class="footer-links">
          <p class="footer-label">Jelajahi</p>
          <a href="katalog.php">Katalog</a>
          <a href="index.html#order">Order</a>
          <a href="index.html#booking">Booking</a>
        </div>
        <div class="footer-links">
          <p class="footer-label">Hubungi kami</p>
          <a href="mailto:info@maisbatik.example">info@maisbatik.example</a>
        </div>
        <a class="back-to-top" href="#home">Kembali ke atas <span aria-hidden="true">↑</span></a>
      </div>
      <div class="footer-bottom">
        <span>© <span id="current-year"><?= date('Y') ?></span> Mais Batik</span>
        <span>Dibuat dengan rasa dan cerita.</span>
      </div>
    </footer>
  </body>
</html>
