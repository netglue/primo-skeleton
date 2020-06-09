<?php
declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

return [
    'General' => [
        'title' => T::richText('Page Title', null, [T::H1], false, false, true),
        'description' => T::richText('Page Description', 'This description helps you leave notes and information about the page', [T::blocksText()], true),
    ],
    'SEO' => require __DIR__ . '/partial/seo.php',
];
