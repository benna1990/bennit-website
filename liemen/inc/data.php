<?php
// Gedeelde helpers voor Liemen Bouw en Advies — laadt/bewaart projecten en rendert kaarten.

define('LB_BASE', dirname(__DIR__));
define('LB_DATA', LB_BASE . '/data/projects.json');
define('LB_ASSETS', LB_BASE . '/assets');

function lb_e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/** Laad projecten, gesorteerd op 'order'. */
function lb_load_projects() {
    if (!file_exists(LB_DATA)) return [];
    $data = json_decode(file_get_contents(LB_DATA), true);
    if (!is_array($data)) return [];
    usort($data, function ($a, $b) {
        return ((int)($a['order'] ?? 0)) <=> ((int)($b['order'] ?? 0));
    });
    return $data;
}

/** Bewaar projecten (volgorde wordt hernummerd op basis van array-volgorde). */
function lb_save_projects($projects) {
    $projects = array_values($projects);
    foreach ($projects as $i => &$p) { $p['order'] = $i + 1; }
    unset($p);
    if (!is_dir(dirname(LB_DATA))) mkdir(dirname(LB_DATA), 0775, true);
    return file_put_contents(
        LB_DATA,
        json_encode($projects, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        LOCK_EX
    ) !== false;
}

/** Bepaal de beeld-variant op basis van aantal foto's en labels. */
function lb_media_variant($images) {
    $n = count($images);
    if ($n <= 1) return 'single';
    if ($n === 2) {
        foreach ($images as $im) {
            if (!empty($im['label'])) return 'ba';
        }
        return 'duo';
    }
    return 'trio';
}

/** Render één projectkaart (zelfde markup als de statische site). */
function lb_render_card($p) {
    $images = $p['images'] ?? [];
    if (!$images) return '';
    $variant = lb_media_variant($images);
    ob_start(); ?>
          <article class="card">
            <div class="card__media card__media--<?= $variant ?>">
              <?php foreach ($images as $im): ?>
              <figure><img src="assets/<?= lb_e($im['src']) ?>" alt="<?= lb_e($im['alt'] ?? ($p['title'] ?? '')) ?>" loading="lazy" /><?php if (!empty($im['label'])): ?><figcaption><?= lb_e($im['label']) ?></figcaption><?php endif; ?></figure>
              <?php endforeach; ?>
            </div>
            <div class="card__body">
              <div class="card__top"><h3><?= lb_e($p['title'] ?? '') ?></h3><span class="card__tag"><?= lb_e($p['type'] ?? '') ?></span></div>
              <p class="card__desc"><?= lb_e($p['description'] ?? '') ?></p>
            </div>
          </article>
    <?php
    return ob_get_clean();
}

/** Verzamel alle keywords voor een meta-tag. */
function lb_all_keywords($projects) {
    $kw = [];
    foreach ($projects as $p) {
        foreach (preg_split('/\s*,\s*/', (string)($p['keywords'] ?? '')) as $k) {
            $k = trim($k);
            if ($k !== '') $kw[strtolower($k)] = $k;
        }
    }
    return implode(', ', array_values($kw));
}
