<?php
declare(strict_types=1);

function content_file_path(): string
{
    return __DIR__ . '/../data/content.json';
}

function default_content(): array
{
    return [
        'site' => [
            'title' => 'Cinder & Sage | Neighborhood Kitchen',
            'description' => 'Cinder & Sage is a local eatery serving wood-fired comfort food, seasonal plates, and handcrafted drinks.',
            'brand' => 'Cinder & Sage',
            'footer' => 'Crafted for good company.',
        ],
        'hero' => [
            'eyebrow' => 'Neighborhood Kitchen',
            'headline' => 'Get your ticket to flavor town.',
            'description' => 'From sunrise pastries to late-night small plates, Cinder & Sage brings together fresh ingredients from nearby farms and the people who call this block home.',
            'primaryCtaLabel' => 'Book a Table',
            'primaryCtaLink' => '#visit',
            'menuCtaLabel' => 'See Menu',
        ],
        'feature' => [
            'heading' => "Tonight's Feature",
            'title' => 'Smoked Tomato Bucatini',
            'description' => 'House pasta, charred cherry tomatoes, basil oil, whipped ricotta.',
            'price' => '$19',
        ],
        'menuModal' => [
            'eyebrow' => 'Sample Menu',
            'title' => 'Seasonal Favorites',
        ],
        'menuCategories' => [
            [
                'id' => 'starters',
                'label' => 'Starters',
                'items' => [
                    ['name' => 'Roasted Carrot Hummus', 'price' => '$10'],
                    ['name' => 'Crispy Calamari', 'price' => '$14'],
                    ['name' => 'Grilled Flatbread', 'price' => '$12'],
                ],
            ],
            [
                'id' => 'mains',
                'label' => 'Mains',
                'items' => [
                    ['name' => 'Oak-Fire Half Chicken', 'price' => '$24'],
                    ['name' => 'Smoked Tomato Bucatini', 'price' => '$19'],
                    ['name' => 'Seared Market Fish', 'price' => '$29'],
                ],
            ],
            [
                'id' => 'drinks',
                'label' => 'Drinks',
                'items' => [
                    ['name' => 'Citrus Rosemary Soda', 'price' => '$6'],
                    ['name' => 'House Sangria', 'price' => '$11'],
                    ['name' => 'Cold Brew Tonic', 'price' => '$7'],
                ],
            ],
        ],
        'story' => [
            'eyebrow' => 'Who We Are',
            'title' => 'Built by Locals, for Locals',
            'body' => 'Cinder & Sage started as a weekend pop-up and grew into a full-time neighborhood dining room. We focus on simple techniques, bold flavors, and hospitality that feels like your favorite corner table.',
        ],
        'visit' => [
            'eyebrow' => 'Plan Your Visit',
            'title' => 'Find Us in the Heart of River District',
            'address' => '245 Market Lane, River District',
            'contact' => '(555) 014-6734 • hello@cinderandsage.com',
            'hoursTitle' => 'Hours',
            'hours' => [
                ['day' => 'Mon - Thu', 'time' => '11am - 9pm'],
                ['day' => 'Fri - Sat', 'time' => '11am - 11pm'],
                ['day' => 'Sunday', 'time' => '10am - 8pm'],
            ],
        ],
    ];
}

function merge_content(array $defaults, array $override): array
{
    foreach ($override as $key => $value) {
        if (is_array($value) && isset($defaults[$key]) && is_array($defaults[$key]) && array_is_list($value) === array_is_list($defaults[$key])) {
            $defaults[$key] = merge_content($defaults[$key], $value);
            continue;
        }
        $defaults[$key] = $value;
    }

    return $defaults;
}

function load_content(): array
{
    $defaults = default_content();
    $path = content_file_path();

    if (!is_file($path)) {
        return $defaults;
    }

    $raw = file_get_contents($path);
    if ($raw === false) {
        return $defaults;
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return $defaults;
    }

    return merge_content($defaults, $decoded);
}

function save_content(array $content): bool
{
    $json = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    $path = content_file_path();
    return file_put_contents($path, $json . PHP_EOL, LOCK_EX) !== false;
}

function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
