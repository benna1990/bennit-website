<?php require __DIR__ . '/inc/data.php'; $projects = lb_load_projects(); $featured = array_filter($projects, fn($p) => !empty($p['featured'])); ?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Liemen Bouw en Advies — aannemersbedrijf in Diemen</title>
  <meta name="description" content="Liemen Bouw en Advies in Diemen. Aannemersbedrijf in burgerlijke en utiliteitsbouw: bouw, renovatie en verbouwing, met bouwadvies en -begeleiding. Eén aanspreekpunt van advies tot oplevering." />
  <meta name="theme-color" content="#FBFAF8" />
  <meta property="og:title" content="Liemen Bouw en Advies" />
  <meta property="og:description" content="Met aandacht gebouwd, van advies tot oplevering." />
  <meta property="og:type" content="website" />
  <meta property="og:locale" content="nl_NL" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

  <!-- ============ HEADER ============ -->
  <header class="hdr" id="top">
    <div class="wrap hdr__inner">
      <a href="#top" class="logo">Liemen <span>Bouw en Advies</span></a>
      <nav class="nav" aria-label="Hoofdmenu">
        <button class="nav__btn" aria-expanded="false" aria-controls="menu" aria-label="Menu">
          <span></span><span></span><span></span>
        </button>
        <ul id="menu" class="nav__list">
          <li><a href="#diensten">Diensten</a></li>
          <li><a href="werk.php">Projecten</a></li>
          <li><a href="#over">Over</a></li>
          <li><a href="#contact" class="btn btn--sm">Contact</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main>
    <!-- ============ HERO (split) ============ -->
    <section class="hero wrap">
      <div class="hero__text">
        <p class="eyebrow">Aannemersbedrijf · burgerlijke &amp; utiliteitsbouw</p>
        <h1>Met aandacht gebouwd, van advies tot oplevering.</h1>
        <p class="lead">
          Bouw, renovatie en verbouwing — met eerlijk bouwadvies en begeleiding
          vooraf. Ik denk met u mee én verzorg de uitvoering, voor particulier
          en bedrijf.
        </p>
        <div class="hero__actions">
          <a href="#projecten" class="btn">Bekijk mijn werk</a>
          <a href="#contact" class="link">Neem contact op →</a>
        </div>
      </div>
      <figure class="hero__media">
        <img class="media media--hero" src="assets/badkamer2-na.jpg"
             alt="Strak afgewerkte badkamer met inloopdouche, dubbele wastafel en natuurtinten" />
      </figure>
    </section>

    <!-- ============ WAARDEN (rustige band) ============ -->
    <section class="values wrap">
      <div class="value">
        <h3>Burgerlijke &amp; utiliteitsbouw</h3>
        <p>Van woning tot bedrijfspand — degelijk bouwwerk dat jaren meegaat.</p>
      </div>
      <div class="value">
        <h3>Renovatie &amp; verbouwing</h3>
        <p>Verbouwen, uitbreiden en renoveren met oog voor detail en afwerking.</p>
      </div>
      <div class="value">
        <h3>Advies &amp; begeleiding</h3>
        <p>Bouwadvies en -begeleiding bij uw renovatie of verbouwing, van plan tot oplevering.</p>
      </div>
    </section>

    <!-- ============ DIENSTEN ============ -->
    <section class="sec" id="diensten">
      <div class="wrap split">
        <div class="split__head">
          <p class="eyebrow">Diensten</p>
          <h2>Wat ik voor u doe</h2>
          <p class="muted">Of het nu om advies of om de uitvoering gaat: u heeft steeds één vast aanspreekpunt, van het eerste plan tot de oplevering.</p>
        </div>
        <div class="split__body two">
          <div>
            <h3>Bouwadvies &amp; begeleiding</h3>
            <ul class="list">
              <li>Bouwadvies bij renovatie &amp; verbouwing</li>
              <li>Bouwbegeleiding &amp; directievoering</li>
              <li>Vergunningen, tekeningen &amp; haalbaarheid</li>
              <li>Kostenraming &amp; planning</li>
            </ul>
          </div>
          <div>
            <h3>Bouw &amp; uitvoering</h3>
            <ul class="list">
              <li>Verbouwing &amp; renovatie</li>
              <li>Aan- en uitbouw &amp; dakkapellen</li>
              <li>Burgerlijke &amp; utiliteitsbouw (woning en bedrijfspand)</li>
              <li>Badkamers, keukens &amp; interieurbouw</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ PROJECTEN ============ -->
    <section class="sec sec--stone" id="projecten">
      <div class="wrap">
        <header class="sec__head">
          <p class="eyebrow">Projecten</p>
          <h2>Recent werk</h2>
        </header>

        <div class="work-grid">
<?php foreach ($featured as $p) { echo lb_render_card($p); } ?>
        </div>

        <div class="work-more">
          <a href="werk.php" class="btn">Alle projecten bekijken →</a>
        </div>
      </div>
    </section>

    <!-- ============ OVER (split met beeld) ============ -->
    <section class="sec" id="over">
      <div class="wrap over">
        <figure class="over__media">
          <img class="media media--portrait" src="assets/portret.png"
               alt="Portret van de vakman achter Liemen Bouw en Advies" />
        </figure>
        <div class="over__text">
          <p class="eyebrow">Over Liemen</p>
          <h2>Geen anoniem bouwbedrijf — gewoon een vakman</h2>
          <p>
            U krijgt één vast gezicht: dezelfde persoon die het advies geeft, de
            offerte maakt en straks met de gereedschapskist voor de deur staat.
            Klein genoeg om u echt te kennen, ervaren genoeg om het goed te doen.
          </p>
          <p class="muted">
            Vanuit Diemen werk ik voor particulieren die hun huis willen
            verbeteren en voor bedrijven die een betrouwbare partner zoeken.
            Waar nodig schakel ik vaste vakmensen bij, zodat het werk altijd
            klopt. Afspraak is afspraak.
          </p>
        </div>
      </div>
    </section>

    <!-- ============ CONTACT ============ -->
    <section class="sec sec--stone" id="contact">
      <div class="wrap contact">
        <div class="contact__l">
          <p class="eyebrow">Contact</p>
          <h2>Begin met een gesprek</h2>
          <p class="muted">Vrijblijvend kennismaken? Ik denk graag met u mee over de mogelijkheden.</p>
          <ul class="contact__reach">
            <li><span>Bel</span><a href="tel:+31647470405">06 47 47 04 05</a></li>
            <li><span>Mail</span><a href="mailto:info@liemenbouwenadvies.nl">info@liemenbouwenadvies.nl</a></li>
            <li><span>Adres</span>Martin Luther Kinglaan 42, 1111 LH Diemen</li>
            <li><span>Werkgebied</span>Diemen en omstreken</li>
          </ul>
        </div>

        <form class="form" action="#" method="post" novalidate>
          <label>Naam<input type="text" name="naam" autocomplete="name" required /></label>
          <div class="form__row">
            <label>E-mail<input type="email" name="email" autocomplete="email" required /></label>
            <label>Telefoon<input type="tel" name="tel" autocomplete="tel" /></label>
          </div>
          <label>Uw bericht<textarea name="bericht" rows="3" required></textarea></label>
          <button type="submit" class="btn btn--block">Verstuur aanvraag</button>
        </form>
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
        <a href="#diensten">Diensten</a>
        <a href="werk.php">Projecten</a>
        <a href="#over">Over</a>
        <a href="#contact">Contact</a>
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
