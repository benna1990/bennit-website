<?php
session_start();
require __DIR__ . '/config.php';
require dirname(__DIR__) . '/inc/data.php';

/* ---------------- Helpers ---------------- */
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_check() {
    if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
        http_response_code(400);
        exit('Ongeldige sessie. Ga terug en probeer opnieuw.');
    }
}
function redirect($q = '') {
    header('Location: index.php' . ($q ? ('?' . $q) : ''));
    exit;
}
function slugify($s) {
    $s = strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-') ?: 'project';
}
function save_uploads($titleForAlt) {
    $added = [];
    if (empty($_FILES['uploads']) || !is_array($_FILES['uploads']['name'])) return $added;
    $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    $count = count($_FILES['uploads']['name']);
    for ($i = 0; $i < $count; $i++) {
        if ($_FILES['uploads']['error'][$i] !== UPLOAD_ERR_OK) continue;
        $tmp = $_FILES['uploads']['tmp_name'][$i];
        $orig = $_FILES['uploads']['name'][$i];
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!isset($allowed[$ext])) continue;
        if (getimagesize($tmp) === false) continue;            // echt een afbeelding?
        if ($_FILES['uploads']['size'][$i] > 15 * 1024 * 1024) continue; // max 15MB
        $name = slugify(pathinfo($orig, PATHINFO_FILENAME)) . '-' . substr(uniqid(), -6) . '.' . $ext;
        if (move_uploaded_file($tmp, LB_ASSETS . '/' . $name)) {
            $added[] = ['src' => $name, 'label' => '', 'alt' => $titleForAlt];
        }
    }
    return $added;
}

/* ---------------- Auth ---------------- */
$action = $_POST['action'] ?? ($_GET['action'] ?? '');

if ($action === 'login') {
    if (password_verify($_POST['password'] ?? '', LB_ADMIN_PASS_HASH)) {
        session_regenerate_id(true);
        $_SESSION['auth'] = true;
        redirect();
    } else {
        $loginError = 'Onjuist wachtwoord.';
    }
}
if ($action === 'logout') { session_destroy(); header('Location: index.php'); exit; }

$authed = !empty($_SESSION['auth']);

/* ---------------- Acties (alleen ingelogd) ---------------- */
if ($authed && $_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['save', 'delete', 'move'], true)) {
    csrf_check();
    $projects = lb_load_projects();

    if ($action === 'save') {
        $id = trim($_POST['id'] ?? '');
        $isNew = ($id === '');
        if ($isNew) $id = slugify($_POST['title'] ?? 'project') . '-' . substr(uniqid(), -4);

        // Bestaande afbeeldingen heropbouwen uit het formulier
        $images = [];
        $srcs = $_POST['img_src'] ?? [];
        foreach ($srcs as $idx => $src) {
            if (!empty($_POST['img_delete'][$idx])) continue;
            $images[] = [
                'src'   => $src,
                'label' => trim($_POST['img_label'][$idx] ?? ''),
                'alt'   => trim($_POST['img_alt'][$idx] ?? ''),
                'ord'   => (int)($_POST['img_order'][$idx] ?? $idx),
            ];
        }
        usort($images, fn($a, $b) => $a['ord'] <=> $b['ord']);
        foreach ($images as &$im) unset($im['ord']);
        unset($im);

        // Nieuwe uploads toevoegen
        $title = trim($_POST['title'] ?? '');
        foreach (save_uploads($title ?: 'Projectfoto') as $up) $images[] = $up;

        $record = [
            'id'          => $id,
            'title'       => $title,
            'type'        => ($_POST['type'] ?? 'Particulier') === 'Zakelijk' ? 'Zakelijk' : 'Particulier',
            'description' => trim($_POST['description'] ?? ''),
            'keywords'    => trim($_POST['keywords'] ?? ''),
            'featured'    => !empty($_POST['featured']),
            'order'       => (int)($_POST['order'] ?? 999),
            'images'      => $images,
        ];

        $found = false;
        foreach ($projects as &$p) {
            if (($p['id'] ?? '') === $id) { $record['order'] = $p['order'] ?? $record['order']; $p = $record; $found = true; break; }
        }
        unset($p);
        if (!$found) $projects[] = $record;

        usort($projects, fn($a, $b) => ((int)($a['order'] ?? 0)) <=> ((int)($b['order'] ?? 0)));
        lb_save_projects($projects);
        redirect('saved=1');
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $projects = array_values(array_filter($projects, fn($p) => ($p['id'] ?? '') !== $id));
        lb_save_projects($projects);
        redirect('deleted=1');
    }

    if ($action === 'move') {
        $id = $_POST['id'] ?? '';
        $dir = $_POST['dir'] ?? '';
        $projects = array_values($projects);
        foreach ($projects as $i => $p) {
            if (($p['id'] ?? '') === $id) {
                $j = $dir === 'up' ? $i - 1 : $i + 1;
                if ($j >= 0 && $j < count($projects)) {
                    $tmp = $projects[$i]; $projects[$i] = $projects[$j]; $projects[$j] = $tmp;
                }
                break;
            }
        }
        lb_save_projects($projects);
        redirect();
    }
}

$projects = $authed ? lb_load_projects() : [];
$editId = $_GET['edit'] ?? null;
$showNew = isset($_GET['new']);
$editing = null;
if ($editId) {
    foreach ($projects as $p) if (($p['id'] ?? '') === $editId) $editing = $p;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex" />
  <title><?= lb_e(LB_ADMIN_TITLE) ?></title>
  <style>
    :root{--ink:#1e1b18;--muted:#6e665d;--line:#e3ddd2;--bg:#f6f3ee;--accent:#b4513a;--accent-d:#984230;}
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;background:var(--bg);color:var(--ink);line-height:1.55}
    a{color:var(--accent-d)}
    .topbar{display:flex;align-items:center;justify-content:space-between;background:#fff;border-bottom:1px solid var(--line);padding:.9rem 1.4rem}
    .topbar h1{font-size:1.1rem;margin:0}
    .wrap{max-width:900px;margin:0 auto;padding:1.6rem 1.2rem 4rem}
    .btn{display:inline-flex;align-items:center;gap:.4em;background:var(--accent);color:#fff;border:0;border-radius:8px;padding:.6em 1.1em;font-size:.95rem;font-weight:600;cursor:pointer;text-decoration:none}
    .btn:hover{background:var(--accent-d)}
    .btn--ghost{background:#fff;color:var(--ink);border:1px solid var(--line)}
    .btn--sm{padding:.35em .7em;font-size:.85rem;border-radius:6px}
    .btn--danger{background:#fff;color:#b3261e;border:1px solid #e7c3c0}
    .card{background:#fff;border:1px solid var(--line);border-radius:12px;padding:1.1rem 1.2rem;margin-bottom:1rem}
    .row{display:flex;align-items:center;gap:1rem}
    .row + .row{margin-top:.5rem}
    .grow{flex:1}
    .thumb{width:60px;height:46px;object-fit:cover;border-radius:6px;border:1px solid var(--line)}
    .pill{font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--accent);border:1px solid var(--line);border-radius:999px;padding:.2em .6em}
    .pill--on{background:var(--accent);color:#fff;border-color:var(--accent)}
    label{display:block;font-size:.8rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin:.9rem 0 .3rem}
    input[type=text],textarea,select{width:100%;font:inherit;padding:.6em .7em;border:1px solid var(--line);border-radius:8px;background:#fff}
    textarea{resize:vertical}
    .muted{color:var(--muted)}
    .flash{background:#e8f3e8;border:1px solid #bcd9bc;color:#2c5e2c;padding:.7em 1em;border-radius:8px;margin-bottom:1rem}
    .imgrow{display:flex;gap:.7rem;align-items:flex-start;border:1px solid var(--line);border-radius:8px;padding:.6rem;margin-bottom:.6rem;background:#fbfaf8}
    .imgrow .fields{flex:1;display:grid;grid-template-columns:90px 1fr 70px 80px;gap:.5rem;align-items:center}
    .imgrow .fields label{margin:0 0 .15rem}
    .inline{display:flex;align-items:center;gap:.5rem}
    h2{font-size:1.25rem;margin:.2rem 0 1rem}
    @media(max-width:620px){.imgrow .fields{grid-template-columns:1fr 1fr}}
  </style>
</head>
<body>

<?php if (!$authed): ?>
  <div class="wrap" style="max-width:380px;margin-top:8vh">
    <div class="card">
      <h2>Inloggen</h2>
      <?php if (!empty($loginError)): ?><p style="color:#b3261e"><?= lb_e($loginError) ?></p><?php endif; ?>
      <form method="post">
        <input type="hidden" name="action" value="login" />
        <label>Wachtwoord</label>
        <input type="password" name="password" autofocus required />
        <div style="margin-top:1rem"><button class="btn" type="submit">Inloggen</button></div>
      </form>
    </div>
    <p class="muted" style="font-size:.85rem;text-align:center">Beheer van projecten · Liemen Bouw en Advies</p>
  </div>

<?php else: ?>
  <div class="topbar">
    <h1><?= lb_e(LB_ADMIN_TITLE) ?></h1>
    <div class="inline">
      <a class="btn btn--ghost btn--sm" href="../index.php" target="_blank">Bekijk site ↗</a>
      <a class="btn btn--ghost btn--sm" href="?action=logout">Uitloggen</a>
    </div>
  </div>
  <div class="wrap">

    <?php if (isset($_GET['saved'])): ?><div class="flash">Project opgeslagen.</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="flash">Project verwijderd.</div><?php endif; ?>

    <?php if ($editing !== null || $showNew):
        $p = $editing ?? ['id'=>'','title'=>'','type'=>'Particulier','description'=>'','keywords'=>'','featured'=>false,'order'=>count($projects)+1,'images'=>[]]; ?>
      <h2><?= $editing ? 'Project bewerken' : 'Nieuw project' ?></h2>
      <form method="post" enctype="multipart/form-data" class="card">
        <input type="hidden" name="action" value="save" />
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>" />
        <input type="hidden" name="id" value="<?= lb_e($p['id']) ?>" />
        <input type="hidden" name="order" value="<?= (int)($p['order'] ?? 999) ?>" />

        <label>Titel</label>
        <input type="text" name="title" value="<?= lb_e($p['title']) ?>" required />

        <div class="row" style="gap:1.2rem">
          <div class="grow">
            <label>Type</label>
            <select name="type">
              <option <?= ($p['type']==='Particulier'?'selected':'') ?>>Particulier</option>
              <option <?= ($p['type']==='Zakelijk'?'selected':'') ?>>Zakelijk</option>
            </select>
          </div>
          <div class="grow">
            <label>Op voorpagina tonen</label>
            <div class="inline" style="margin-top:.55rem">
              <input type="checkbox" id="featured" name="featured" value="1" <?= !empty($p['featured'])?'checked':'' ?> style="width:auto" />
              <label for="featured" style="margin:0;text-transform:none;letter-spacing:0;font-weight:500;color:var(--ink)">Ja, toon dit project op de homepage</label>
            </div>
          </div>
        </div>

        <label>Omschrijving</label>
        <textarea name="description" rows="3"><?= lb_e($p['description']) ?></textarea>

        <label>Keywords (komma-gescheiden, voor SEO)</label>
        <input type="text" name="keywords" value="<?= lb_e($p['keywords']) ?>" />

        <label>Foto's</label>
        <?php if (!empty($p['images'])): foreach ($p['images'] as $idx => $im): ?>
          <div class="imgrow">
            <img class="thumb" src="../assets/<?= lb_e($im['src']) ?>" alt="" />
            <input type="hidden" name="img_src[<?= $idx ?>]" value="<?= lb_e($im['src']) ?>" />
            <div class="fields">
              <div><label>Label</label>
                <select name="img_label[<?= $idx ?>]">
                  <option value="" <?= empty($im['label'])?'selected':'' ?>>—</option>
                  <option <?= (($im['label']??'')==='Voor')?'selected':'' ?>>Voor</option>
                  <option <?= (($im['label']??'')==='Na')?'selected':'' ?>>Na</option>
                </select>
              </div>
              <div><label>Beschrijving (alt)</label>
                <input type="text" name="img_alt[<?= $idx ?>]" value="<?= lb_e($im['alt'] ?? '') ?>" />
              </div>
              <div><label>Volgorde</label>
                <input type="text" name="img_order[<?= $idx ?>]" value="<?= $idx ?>" />
              </div>
              <div><label>Verwijderen</label>
                <input type="checkbox" name="img_delete[<?= $idx ?>]" value="1" style="width:auto" />
              </div>
            </div>
          </div>
        <?php endforeach; endif; ?>

        <label>Foto's toevoegen (meerdere mogelijk)</label>
        <input type="file" name="uploads[]" accept="image/*" multiple />
        <p class="muted" style="font-size:.82rem">Tip: bij voor/na-projecten zet je na het uploaden het label op "Voor" en "Na".</p>

        <div class="row" style="margin-top:1.2rem">
          <button class="btn" type="submit">Opslaan</button>
          <a class="btn btn--ghost" href="index.php">Annuleren</a>
        </div>
      </form>

    <?php else: ?>
      <div class="row" style="justify-content:space-between;margin-bottom:1rem">
        <h2 style="margin:0">Projecten (<?= count($projects) ?>)</h2>
        <a class="btn" href="?new=1">+ Nieuw project</a>
      </div>

      <?php if (!$projects): ?>
        <p class="muted">Nog geen projecten. Klik op “Nieuw project”.</p>
      <?php endif; ?>

      <?php foreach ($projects as $i => $p): ?>
        <div class="card">
          <div class="row">
            <?php if (!empty($p['images'][0]['src'])): ?>
              <img class="thumb" src="../assets/<?= lb_e($p['images'][0]['src']) ?>" alt="" />
            <?php endif; ?>
            <div class="grow">
              <strong><?= lb_e($p['title']) ?></strong>
              <div class="inline" style="margin-top:.3rem">
                <span class="pill"><?= lb_e($p['type']) ?></span>
                <span class="pill <?= !empty($p['featured'])?'pill--on':'' ?>"><?= !empty($p['featured'])?'op voorpagina':'niet op voorpagina' ?></span>
                <span class="muted" style="font-size:.82rem"><?= count($p['images'] ?? []) ?> foto('s)</span>
              </div>
            </div>
            <div class="inline">
              <form method="post" style="display:inline"><input type="hidden" name="action" value="move"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= lb_e($p['id']) ?>"><input type="hidden" name="dir" value="up"><button class="btn btn--ghost btn--sm" <?= $i===0?'disabled':'' ?> title="Omhoog">↑</button></form>
              <form method="post" style="display:inline"><input type="hidden" name="action" value="move"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= lb_e($p['id']) ?>"><input type="hidden" name="dir" value="down"><button class="btn btn--ghost btn--sm" <?= $i===count($projects)-1?'disabled':'' ?> title="Omlaag">↓</button></form>
              <a class="btn btn--ghost btn--sm" href="?edit=<?= urlencode($p['id']) ?>">Bewerken</a>
              <form method="post" style="display:inline" onsubmit="return confirm('Dit project verwijderen?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= lb_e($p['id']) ?>"><button class="btn btn--danger btn--sm">Verwijderen</button></form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

      <p class="muted" style="font-size:.82rem;margin-top:1.5rem">
        Tip: de volgorde hierboven bepaalt ook de volgorde op de site. Vink “op voorpagina” aan om een project op de homepage te tonen.
      </p>
    <?php endif; ?>

  </div>
<?php endif; ?>

</body>
</html>
