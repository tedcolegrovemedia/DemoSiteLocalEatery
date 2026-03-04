<?php
declare(strict_types=1);

require __DIR__ . '/lib/content.php';

function post_value(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

function parse_name_price_lines(string $raw): array
{
    $items = [];
    $lines = preg_split('/\R/', $raw) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = explode('|', $line, 2);
        $name = trim($parts[0] ?? '');
        $price = trim($parts[1] ?? '');

        if ($name === '') {
            continue;
        }

        $items[] = ['name' => $name, 'price' => $price];
    }

    return $items;
}

function parse_hours_lines(string $raw): array
{
    $hours = [];
    $lines = preg_split('/\R/', $raw) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = explode('|', $line, 2);
        $day = trim($parts[0] ?? '');
        $time = trim($parts[1] ?? '');

        if ($day === '') {
            continue;
        }

        $hours[] = ['day' => $day, 'time' => $time];
    }

    return $hours;
}

function slugify(string $label): string
{
    $slug = strtolower($label);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : 'category';
}

function items_to_text(array $items): string
{
    $lines = [];
    foreach ($items as $item) {
        $lines[] = trim((string) ($item['name'] ?? '')) . '|' . trim((string) ($item['price'] ?? ''));
    }
    return implode("\n", $lines);
}

function hours_to_text(array $hours): string
{
    $lines = [];
    foreach ($hours as $hour) {
        $lines[] = trim((string) ($hour['day'] ?? '')) . '|' . trim((string) ($hour['time'] ?? ''));
    }
    return implode("\n", $lines);
}

$content = load_content();
$status = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated = $content;

    $updated['site']['title'] = post_value('site_title', (string) $content['site']['title']);
    $updated['site']['description'] = post_value('site_description', (string) $content['site']['description']);
    $updated['site']['brand'] = post_value('site_brand', (string) $content['site']['brand']);
    $updated['site']['footer'] = post_value('site_footer', (string) $content['site']['footer']);

    $updated['hero']['eyebrow'] = post_value('hero_eyebrow', (string) $content['hero']['eyebrow']);
    $updated['hero']['headline'] = post_value('hero_headline', (string) $content['hero']['headline']);
    $updated['hero']['description'] = post_value('hero_description', (string) $content['hero']['description']);
    $updated['hero']['primaryCtaLabel'] = post_value('hero_primary_cta_label', (string) $content['hero']['primaryCtaLabel']);
    $updated['hero']['primaryCtaLink'] = post_value('hero_primary_cta_link', (string) $content['hero']['primaryCtaLink']);
    $updated['hero']['menuCtaLabel'] = post_value('hero_menu_cta_label', (string) $content['hero']['menuCtaLabel']);

    $updated['feature']['heading'] = post_value('feature_heading', (string) $content['feature']['heading']);
    $updated['feature']['title'] = post_value('feature_title', (string) $content['feature']['title']);
    $updated['feature']['description'] = post_value('feature_description', (string) $content['feature']['description']);
    $updated['feature']['price'] = post_value('feature_price', (string) $content['feature']['price']);

    $updated['menuModal']['eyebrow'] = post_value('menu_modal_eyebrow', (string) $content['menuModal']['eyebrow']);
    $updated['menuModal']['title'] = post_value('menu_modal_title', (string) $content['menuModal']['title']);

    $labels = $_POST['menu_category_label'] ?? [];
    $itemsRaw = $_POST['menu_category_items'] ?? [];
    $categories = [];

    if (is_array($labels) && is_array($itemsRaw)) {
        foreach ($labels as $i => $labelRaw) {
            $label = is_string($labelRaw) ? trim($labelRaw) : '';
            $itemsText = isset($itemsRaw[$i]) && is_string($itemsRaw[$i]) ? $itemsRaw[$i] : '';
            $items = parse_name_price_lines($itemsText);

            if ($label === '' || $items === []) {
                continue;
            }

            $categories[] = [
                'id' => slugify($label),
                'label' => $label,
                'items' => $items,
            ];
        }
    }

    if ($categories !== []) {
        $updated['menuCategories'] = $categories;
    }

    $updated['story']['eyebrow'] = post_value('story_eyebrow', (string) $content['story']['eyebrow']);
    $updated['story']['title'] = post_value('story_title', (string) $content['story']['title']);
    $updated['story']['body'] = post_value('story_body', (string) $content['story']['body']);

    $updated['visit']['eyebrow'] = post_value('visit_eyebrow', (string) $content['visit']['eyebrow']);
    $updated['visit']['title'] = post_value('visit_title', (string) $content['visit']['title']);
    $updated['visit']['address'] = post_value('visit_address', (string) $content['visit']['address']);
    $updated['visit']['contact'] = post_value('visit_contact', (string) $content['visit']['contact']);
    $updated['visit']['hoursTitle'] = post_value('visit_hours_title', (string) $content['visit']['hoursTitle']);

    $hours = parse_hours_lines(post_value('visit_hours', hours_to_text($content['visit']['hours'])));
    if ($hours !== []) {
        $updated['visit']['hours'] = $hours;
    }

    if (save_content($updated)) {
        $status = 'Saved. Homepage content updated.';
        $content = load_content();
    } else {
        $error = 'Could not save content.json. Check write permissions on data/content.json.';
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin | <?= esc((string) $content['site']['brand']) ?></title>
    <link rel="stylesheet" href="admin.css" />
  </head>
  <body>
    <main class="admin-shell">
      <header class="admin-top">
        <div>
          <p class="kicker">Content Admin</p>
          <h1>Edit Homepage Content</h1>
          <p>Changes save to <code>data/content.json</code> and go live on the homepage.</p>
        </div>
        <a class="btn subtle" href="index.php">View Site</a>
      </header>

      <?php if ($status !== ''): ?>
        <p class="notice success"><?= esc($status) ?></p>
      <?php endif; ?>
      <?php if ($error !== ''): ?>
        <p class="notice error"><?= esc($error) ?></p>
      <?php endif; ?>

      <form method="post" class="admin-form">
        <section class="card">
          <h2>Site Settings</h2>
          <label>Site Title<input name="site_title" value="<?= esc((string) $content['site']['title']) ?>" /></label>
          <label>Meta Description<textarea name="site_description" rows="2"><?= esc((string) $content['site']['description']) ?></textarea></label>
          <label>Brand Name<input name="site_brand" value="<?= esc((string) $content['site']['brand']) ?>" /></label>
          <label>Footer Text<input name="site_footer" value="<?= esc((string) $content['site']['footer']) ?>" /></label>
        </section>

        <section class="card">
          <h2>Hero</h2>
          <label>Eyebrow<input name="hero_eyebrow" value="<?= esc((string) $content['hero']['eyebrow']) ?>" /></label>
          <label>Headline<input name="hero_headline" value="<?= esc((string) $content['hero']['headline']) ?>" /></label>
          <label>Description<textarea name="hero_description" rows="3"><?= esc((string) $content['hero']['description']) ?></textarea></label>
          <div class="grid-two">
            <label>Main Button Label<input name="hero_primary_cta_label" value="<?= esc((string) $content['hero']['primaryCtaLabel']) ?>" /></label>
            <label>Main Button Link<input name="hero_primary_cta_link" value="<?= esc((string) $content['hero']['primaryCtaLink']) ?>" /></label>
          </div>
          <label>Menu Button Label<input name="hero_menu_cta_label" value="<?= esc((string) $content['hero']['menuCtaLabel']) ?>" /></label>
        </section>

        <section class="card">
          <h2>Feature Card</h2>
          <label>Heading<input name="feature_heading" value="<?= esc((string) $content['feature']['heading']) ?>" /></label>
          <label>Dish Name<input name="feature_title" value="<?= esc((string) $content['feature']['title']) ?>" /></label>
          <label>Description<textarea name="feature_description" rows="2"><?= esc((string) $content['feature']['description']) ?></textarea></label>
          <label>Price<input name="feature_price" value="<?= esc((string) $content['feature']['price']) ?>" /></label>
        </section>

        <section class="card">
          <h2>Menu Modal</h2>
          <label>Eyebrow<input name="menu_modal_eyebrow" value="<?= esc((string) $content['menuModal']['eyebrow']) ?>" /></label>
          <label>Title<input name="menu_modal_title" value="<?= esc((string) $content['menuModal']['title']) ?>" /></label>

          <div class="menu-categories" id="menu-categories">
            <?php foreach ($content['menuCategories'] as $category): ?>
              <article class="menu-category">
                <div class="menu-category-head">
                  <h3>Category</h3>
                  <button class="btn danger" type="button" data-remove-category>Remove</button>
                </div>
                <label>Category Label<input name="menu_category_label[]" value="<?= esc((string) $category['label']) ?>" /></label>
                <label>Items (one per line: <code>Name|Price</code>)
                  <textarea name="menu_category_items[]" rows="5"><?= esc(items_to_text($category['items'])) ?></textarea>
                </label>
              </article>
            <?php endforeach; ?>
          </div>

          <button class="btn subtle" type="button" id="add-category">Add Category</button>
        </section>

        <section class="card">
          <h2>Story</h2>
          <label>Eyebrow<input name="story_eyebrow" value="<?= esc((string) $content['story']['eyebrow']) ?>" /></label>
          <label>Title<input name="story_title" value="<?= esc((string) $content['story']['title']) ?>" /></label>
          <label>Body<textarea name="story_body" rows="4"><?= esc((string) $content['story']['body']) ?></textarea></label>
        </section>

        <section class="card">
          <h2>Visit</h2>
          <label>Eyebrow<input name="visit_eyebrow" value="<?= esc((string) $content['visit']['eyebrow']) ?>" /></label>
          <label>Title<input name="visit_title" value="<?= esc((string) $content['visit']['title']) ?>" /></label>
          <label>Address<input name="visit_address" value="<?= esc((string) $content['visit']['address']) ?>" /></label>
          <label>Contact Line<input name="visit_contact" value="<?= esc((string) $content['visit']['contact']) ?>" /></label>
          <label>Hours Header<input name="visit_hours_title" value="<?= esc((string) $content['visit']['hoursTitle']) ?>" /></label>
          <label>Hours (one per line: <code>Day|Time</code>)
            <textarea name="visit_hours" rows="4"><?= esc(hours_to_text($content['visit']['hours'])) ?></textarea>
          </label>
        </section>

        <div class="actions">
          <button class="btn" type="submit">Save Content</button>
          <a class="btn subtle" href="index.php">Cancel</a>
        </div>
      </form>
    </main>

    <template id="menu-category-template">
      <article class="menu-category">
        <div class="menu-category-head">
          <h3>Category</h3>
          <button class="btn danger" type="button" data-remove-category>Remove</button>
        </div>
        <label>Category Label<input name="menu_category_label[]" value="" /></label>
        <label>Items (one per line: <code>Name|Price</code>)
          <textarea name="menu_category_items[]" rows="5"></textarea>
        </label>
      </article>
    </template>

    <script>
      const container = document.getElementById('menu-categories');
      const addButton = document.getElementById('add-category');
      const template = document.getElementById('menu-category-template');

      function wireRemoveButtons(root = document) {
        root.querySelectorAll('[data-remove-category]').forEach((button) => {
          button.onclick = () => {
            const card = button.closest('.menu-category');
            if (card) card.remove();
          };
        });
      }

      wireRemoveButtons();

      addButton?.addEventListener('click', () => {
        const clone = template.content.cloneNode(true);
        container.appendChild(clone);
        wireRemoveButtons(container);
      });
    </script>
  </body>
</html>
