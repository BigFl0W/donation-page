<?php
declare(strict_types=1);
require_once __DIR__ . '/config/autoload.php';

use App\Database;
use App\Helpers;

$defaults = require __DIR__ . '/config/programme_defaults.php';
$programmeSettings = [];
$rawProgrammeSettings = Database::fetchAll("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'programme_%'") ?: [];
foreach ($rawProgrammeSettings as $settingRow) {
    $programmeSettings[$settingRow['setting_key']] = $settingRow['setting_value'];
}

$getProgrammeValue = static function (string $key) use ($programmeSettings, $defaults): string {
    return (string)($programmeSettings['programme_' . $key] ?? $defaults[$key] ?? '');
};

$renderProgrammeBody = static function (string $text): string {
    $text = trim(str_replace("\r\n", "\n", $text));
    if ($text === '') {
        return '';
    }

    $lines = array_values(array_filter(array_map('trim', explode("\n", $text)), static fn($line) => $line !== ''));
    $html = '';
    $paragraph = [];
    $listItems = [];

    $flushParagraph = static function () use (&$paragraph, &$html): void {
        if (!$paragraph) {
            return;
        }
        $html .= '<p>' . nl2br(Helpers::e(implode("\n", $paragraph))) . '</p>';
        $paragraph = [];
    };

    $flushList = static function () use (&$listItems, &$html): void {
        if (!$listItems) {
            return;
        }
        $html .= '<ul class="programme-list">';
        foreach ($listItems as $item) {
            $html .= '<li>' . Helpers::e($item) . '</li>';
        }
        $html .= '</ul>';
        $listItems = [];
    };

    foreach ($lines as $line) {
        if (preg_match('/^\((?:[ivxlcdm]+|\d+)\)\s*(.+)$/i', $line, $matches)) {
            $flushParagraph();
            $listItems[] = $matches[1];
            continue;
        }

        if ($listItems) {
            $flushList();
        }

        $paragraph[] = $line;
    }

    $flushParagraph();
    $flushList();

    return $html;
};

$page_title = 'Programme | ' . Helpers::brandName();
$hero_title = $getProgrammeValue('hero_title');
$section_title = 'Programme';
$mediaSlots = [];
for ($i = 1; $i <= 6; $i++) {
    $path = trim((string)($programmeSettings["programme_media_{$i}"] ?? ''));
    if ($path !== '') {
        $mediaSlots[] = $path;
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<style>
  .breadcrumbs-page-wrap { display:none !important; }
  .programme-page {
    background:
      radial-gradient(circle at top left, rgba(216, 169, 61, 0.08), transparent 28%),
      linear-gradient(180deg, #fffdf8 0%, #f8fbf8 100%);
  }
  .programme-hero {
    padding: 120px 0 70px;
    position: relative;
    overflow: hidden;
  }
  .programme-hero::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
      radial-gradient(circle at 85% 18%, rgba(17, 71, 52, 0.08), transparent 25%),
      radial-gradient(circle at 10% 80%, rgba(215, 151, 6, 0.08), transparent 22%);
    pointer-events: none;
  }
  .programme-kicker {
    display:inline-flex;
    align-items:center;
    gap:10px;
    color:#0f5d47;
    font-weight:800;
    letter-spacing:0.12em;
    text-transform:uppercase;
    font-size:0.82rem;
    margin-bottom:18px;
  }
  .programme-kicker::before {
    content:"";
    width:42px;
    height:2px;
    background:#d8a93d;
  }
  .programme-heading {
    font-size: clamp(2.8rem, 6vw, 5.2rem);
    line-height: 0.96;
    font-weight: 800;
    color: #132a24;
    margin-bottom: 24px;
  }
  .programme-intro {
    color:#53635d;
    font-size:1.04rem;
    line-height:1.9;
    max-width:720px;
  }
  .programme-shell {
    padding: 0 0 100px;
  }
  .programme-feature-grid {
    display:grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
    gap: 34px;
    margin-top: 42px;
    align-items: start;
  }
  .programme-card,
  .programme-media-card {
    background:#fff;
    border:1px solid rgba(19,42,36,0.08);
    border-radius:28px;
    box-shadow:0 20px 60px rgba(16,24,40,0.06);
  }
  .programme-card {
    padding:34px;
  }
  .programme-card h3 {
    font-size:1.45rem;
    color:#17342c;
    margin-bottom:16px;
    font-weight:800;
  }
  .programme-card .programme-body {
    color:#586862;
    line-height:1.95;
    font-size:0.98rem;
  }
  .programme-card .programme-body p {
    margin:0 0 1.15rem;
  }
  .programme-card .programme-body p:last-child {
    margin-bottom:0;
  }
  .programme-list {
    list-style:none;
    margin:0 0 1.25rem;
    padding:0;
    display:grid;
    gap:12px;
  }
  .programme-list li {
    position:relative;
    padding-left:22px;
    margin:0;
  }
  .programme-list li::before {
    content:"";
    position:absolute;
    left:0;
    top:0.82em;
    width:9px;
    height:9px;
    border-radius:50%;
    background:linear-gradient(135deg, #d8a93d 0%, #0f5d47 100%);
    transform:translateY(-50%);
    box-shadow:0 0 0 5px rgba(216, 169, 61, 0.10);
  }
  .programme-card.alt {
    background: linear-gradient(180deg, #fff 0%, #fcf7ea 100%);
  }
  .programme-card.commitment {
    background: linear-gradient(135deg, #10382f 0%, #174f42 100%);
    color:#f8fbf9;
  }
  .programme-card.commitment h3,
  .programme-card.commitment .programme-body {
    color:#f8fbf9;
  }
  .programme-card.commitment .programme-body {
    opacity:0.92;
  }
  .programme-media-stack {
    display:grid;
    gap:20px;
    position:sticky;
    top:120px;
  }
  .programme-media-card {
    padding:18px;
  }
  .programme-media-card h4 {
    font-size:1rem;
    font-weight:800;
    color:#17342c;
    margin-bottom:8px;
  }
  .programme-media-card p {
    color:#62716b;
    font-size:0.92rem;
    line-height:1.7;
    margin-bottom:16px;
  }
  .programme-media-mosaic {
    display:grid;
    gap:16px;
    grid-template-columns:repeat(2, minmax(0, 1fr));
  }
  .programme-media-tile {
    overflow:hidden;
    border-radius:22px;
    min-height:180px;
    background:#eef3ef;
    position:relative;
  }
  .programme-media-tile:nth-child(1) { grid-column:span 2; min-height:250px; }
  .programme-media-tile:nth-child(4) { grid-column:span 2; min-height:220px; }
  .programme-media-tile:nth-child(6) { min-height:220px; }
  .programme-media-tile img,
  .programme-media-tile video {
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
  }
  .programme-media-empty {
    display:flex;
    align-items:center;
    justify-content:center;
    min-height:240px;
    border-radius:22px;
    border:1px dashed rgba(19,42,36,0.18);
    color:#6a7973;
    background:#f7faf8;
    text-align:center;
    padding:20px;
  }
  .programme-sections {
    display:grid;
    gap:28px;
    margin-top:28px;
  }
  .programme-cta {
    margin-top:36px;
    padding:28px 30px;
    background:linear-gradient(135deg, #fff3d4 0%, #fdf8ea 100%);
    border-radius:28px;
    border:1px solid rgba(216, 169, 61, 0.18);
  }
  .programme-cta h4 {
    color:#17342c;
    font-size:1.3rem;
    font-weight:800;
    margin-bottom:10px;
  }
  .programme-cta p {
    color:#5b6963;
    margin:0;
    line-height:1.85;
  }
  @media (max-width: 991px) {
    .programme-feature-grid,
    .programme-section-grid,
    .programme-section-grid.reverse { grid-template-columns:1fr; }
    .programme-media-stack {
      position:static;
    }
  }
</style>

<section class="programme-page">
  <div class="programme-hero">
    <div class="container">
      <span class="programme-kicker"><?php echo Helpers::e($getProgrammeValue('hero_kicker')); ?></span>
      <h1 class="programme-heading"><?php echo Helpers::e($getProgrammeValue('hero_title')); ?></h1>
      <div class="programme-intro"><?php echo nl2br(Helpers::e($getProgrammeValue('hero_intro'))); ?></div>

      <div class="programme-feature-grid">
        <div>
          <div class="programme-sections">
            <?php for ($i = 1; $i <= 4; $i++): ?>
              <article class="programme-card <?php echo $i % 2 === 0 ? 'alt' : ''; ?>">
                <h3><?php echo Helpers::e($getProgrammeValue("section_{$i}_title")); ?></h3>
                <div class="programme-body"><?php echo $renderProgrammeBody($getProgrammeValue("section_{$i}_body")); ?></div>
              </article>
            <?php endfor; ?>
          </div>
        </div>

        <aside class="programme-media-stack">
          <div class="programme-media-card">
            <h4><?php echo Helpers::e($getProgrammeValue('media_heading')); ?></h4>
            <p><?php echo Helpers::e($getProgrammeValue('media_intro')); ?></p>
            <?php if ($mediaSlots): ?>
              <div class="programme-media-mosaic">
                <?php foreach ($mediaSlots as $mediaPath): ?>
                  <?php $ext = strtolower(pathinfo($mediaPath, PATHINFO_EXTENSION)); ?>
                  <div class="programme-media-tile">
                    <?php if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'], true)): ?>
                      <video src="<?php echo Helpers::e($mediaPath); ?>" controls muted playsinline></video>
                    <?php else: ?>
                      <img src="<?php echo Helpers::e($mediaPath); ?>" alt="Programme media">
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="programme-media-empty">Programme media uploads from the admin dashboard will appear here.</div>
            <?php endif; ?>
          </div>
        </aside>
      </div>
    </div>
  </div>

  <div class="programme-shell">
    <div class="container">
      <div class="programme-sections">
        <?php for ($i = 5; $i <= 8; $i++): ?>
          <article class="programme-card <?php echo $i % 2 === 0 ? 'alt' : ''; ?>">
            <h3><?php echo Helpers::e($getProgrammeValue("section_{$i}_title")); ?></h3>
            <div class="programme-body"><?php echo $renderProgrammeBody($getProgrammeValue("section_{$i}_body")); ?></div>
          </article>
        <?php endfor; ?>

        <article class="programme-card commitment">
          <h3><?php echo Helpers::e($getProgrammeValue('commitment_title')); ?></h3>
          <div class="programme-body"><?php echo $renderProgrammeBody($getProgrammeValue('commitment_body')); ?></div>
        </article>

        <div class="programme-cta">
          <h4><?php echo Helpers::e($getProgrammeValue('cta_title')); ?></h4>
          <p><?php echo nl2br(Helpers::e($getProgrammeValue('cta_text'))); ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
