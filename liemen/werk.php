<?php require __DIR__ . '/inc/data.php'; $projects = lb_load_projects(); ?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Projecten — Liemen Bouw en Advies</title>
  <meta name="description" content="Een overzicht van recente projecten van Liemen Bouw en Advies in Diemen: badkamers, toiletten, renovaties, verbouwingen en maatwerk — voor particulier en bedrijf." />
  <meta name="keywords" content="<?= lb_e(lb_all_keywords($projects)) ?>" />
  <meta name="theme-color" content="#FBFAF8" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

  <!-- ============ HEADER ============ -->
  <header class="hdr" id="top">
    <div class="wrap hdr__inner">
      <a href="index.php" class="logo">Liemen <span>Bouw en Advies</span></a>
      <nav class="nav" aria-label="Hoofdmenu">
        <button class="nav__btn" aria-expanded="false" aria-controls="menu" aria-label="Menu">
          <span></span><span></span><span></span>
        </button>
        <ul id="menu" class="nav__list">
          <li><a href="index.php#diensten">Diensten</a></li>
          <li><a href="werk.php">Projecten</a></li>
          <li><a href="index.php#over">Over</a></li>
          <li><a href="index.php#contact" class="btn btn--sm">Contact</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main>
    <!-- ============ PAGINA-INTRO ============ -->
    <section class="sec page-head">
      <div class="wrap">
        <p class="eyebrow">Projecten</p>
        <h1>Recent werk</h1>
        <p class="lead">Een greep uit recente klussen — van badkamers en toiletten tot
          renovaties, verbouwingen en maatwerk. Klik op een foto voor een grotere weergave.</p>
      </div>
    </section>

    <!-- ============ ALLE PROJECTEN ============ -->
    <section class="sec sec--stone" style="padding-top:0;">
      <div class="wrap">
        <div class="work-grid">
<?php foreach ($projects as $p) { echo lb_render_card($p); } ?>
        </div>

        <div class="work-more">
          <a href="index.php#contact" class="btn">Zelf een project? Neem contact op</a>
        </div>
      </div>
    </section>
  </main>

  <!-- ============ FOOTER ============ -->
  <footer class="ftr">
    <div class="wrap ftr__inner">
      <div class="ftr__brand">
        <span class="logo logo--light">Liemen <span>Bouw en Advies</span></span>
        <p>Aannemersbedrijf in burgerlijke en utiliteitsbouw — bouw, renovatie en verbouwing.</p>
      </div>
      <nav class="ftr__nav" aria-label="Footer">
        <a href="index.php#diensten">Diensten</a>
        <a href="werk.php">Projecten</a>
        <a href="index.php#over">Over</a>
        <a href="index.php#contact">Contact</a>
      </nav>
      <div class="ftr__meta">
        <p>Martin Luther Kinglaan 42, 1111 LH Diemen</p>
        <p>KvK 42002246 · 06 47 47 04 05</p>
        <p>&copy; <span id="year">2026</span> Liemen Bouw en Advies</p>
      </div>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
