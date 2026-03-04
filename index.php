<?php
declare(strict_types=1);

require __DIR__ . '/lib/content.php';

$content = load_content();
$site = $content['site'];
$hero = $content['hero'];
$feature = $content['feature'];
$menuModal = $content['menuModal'];
$menuCategories = $content['menuCategories'];
$story = $content['story'];
$visit = $content['visit'];
$primaryCtaLink = sanitize_href((string) $hero['primaryCtaLink'], '#visit');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= esc((string) $site['title']) ?></title>
    <meta name="description" content="<?= esc((string) $site['description']) ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Space+Grotesk:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header class="site-header">
      <div class="header-inner">
        <div class="brand"><?= esc((string) $site['brand']) ?></div>
        <button class="menu-toggle" aria-expanded="false" aria-controls="main-nav">Menu</button>
        <nav id="main-nav" class="main-nav">
          <button class="nav-link-btn" type="button" data-open-menu>Menu</button>
          <a href="#story">Our Story</a>
          <a href="#visit">Visit</a>
          <a href="admin.php">Admin</a>
        </nav>
      </div>
    </header>

    <main>
      <section class="hero">
        <div class="hero-copy">
          <p class="eyebrow"><?= esc((string) $hero['eyebrow']) ?></p>
          <h1><?= esc((string) $hero['headline']) ?></h1>
          <p><?= esc((string) $hero['description']) ?></p>
          <div class="hero-actions">
            <a class="btn btn-primary" href="<?= esc($primaryCtaLink) ?>"><?= esc((string) $hero['primaryCtaLabel']) ?></a>
            <button class="btn btn-secondary" type="button" data-open-menu><?= esc((string) $hero['menuCtaLabel']) ?></button>
          </div>
        </div>
        <div class="hero-card">
          <h2><?= esc((string) $feature['heading']) ?></h2>
          <p class="feature-title"><?= esc((string) $feature['title']) ?></p>
          <p><?= esc((string) $feature['description']) ?></p>
          <p class="feature-price"><?= esc((string) $feature['price']) ?></p>
        </div>
      </section>

      <section id="story" class="story-section">
        <div class="section-head">
          <p class="eyebrow"><?= esc((string) $story['eyebrow']) ?></p>
          <h2><?= esc((string) $story['title']) ?></h2>
        </div>
        <p><?= esc((string) $story['body']) ?></p>
      </section>

      <section id="visit" class="visit-section">
        <div>
          <p class="eyebrow"><?= esc((string) $visit['eyebrow']) ?></p>
          <h2><?= esc((string) $visit['title']) ?></h2>
          <p><?= esc((string) $visit['address']) ?></p>
          <p><?= esc((string) $visit['contact']) ?></p>
        </div>
        <div class="hours">
          <h3><?= esc((string) $visit['hoursTitle']) ?></h3>
          <ul>
            <?php foreach ($visit['hours'] as $hour): ?>
              <li><span><?= esc((string) $hour['day']) ?></span><b><?= esc((string) $hour['time']) ?></b></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </section>
    </main>

    <div
      id="menu-modal"
      class="menu-modal"
      role="dialog"
      aria-modal="true"
      aria-labelledby="menu-modal-title"
      aria-hidden="true"
    >
      <div class="menu-modal-card">
        <div class="menu-modal-head">
          <div class="section-head">
            <p class="eyebrow"><?= esc((string) $menuModal['eyebrow']) ?></p>
            <h2 id="menu-modal-title"><?= esc((string) $menuModal['title']) ?></h2>
          </div>
          <button class="menu-modal-close" type="button" aria-label="Close menu">Close</button>
        </div>
        <div class="menu-filters" role="group" aria-label="Filter menu categories">
          <button class="menu-filter is-active" type="button" data-menu-filter="all">All</button>
          <?php foreach ($menuCategories as $category): ?>
            <button class="menu-filter" type="button" data-menu-filter="<?= esc((string) $category['id']) ?>"><?= esc((string) $category['label']) ?></button>
          <?php endforeach; ?>
        </div>
        <div class="menu-grid">
          <?php foreach ($menuCategories as $category): ?>
            <article data-menu-category="<?= esc((string) $category['id']) ?>">
              <h3><?= esc((string) $category['label']) ?></h3>
              <ul>
                <?php foreach ($category['items'] as $item): ?>
                  <li><span><?= esc((string) $item['name']) ?></span><b><?= esc((string) $item['price']) ?></b></li>
                <?php endforeach; ?>
              </ul>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <footer class="site-footer">
      <p>© <span id="year"></span> <?= esc((string) $site['brand']) ?>. <?= esc((string) $site['footer']) ?></p>
    </footer>

    <button
      id="accessibility-toggle"
      class="accessibility-toggle accessibility-toggle-floating"
      type="button"
      aria-pressed="false"
      aria-label="Enable accessible version"
      title="Enable accessible version"
    >
      <span class="accessibility-icon" aria-hidden="true">♿</span>
      <span class="sr-only">Accessible version toggle</span>
    </button>

    <script src="script.js"></script>
  </body>
</html>
