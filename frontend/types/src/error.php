<?php

declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

require_once 'global-config.php';

return [
    'General' => [
        'title' => T::richText('Error Title', null, [T::H1], false, false, true),
        'description' => T::richText('Error Description', 'This description helps you leave notes and information about the page', T::blocksText(), true),
        'errorCode' => T::number('HTTP Error Code', 'ie 404', 300, 599),
    ],
    'Content' => [
        'body' => require __DIR__ . '/partial/standard-slices.php',
    ],
    'SEO' => require __DIR__ . '/partial/seo.php',
];
