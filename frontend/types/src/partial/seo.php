<?php
declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

return [
    'uid' => T::uid('URL', 'Provide a unique url for this type of document'),
    'meta-title' => T::text('Browser Title', 'Provide a title for search engines'),
    'meta-description' => T::text('Search Engine Description', 'The page description shows up search results'),
    'robots' => T::select('Search Engines', 'Choose an indexing strategy', [
        'index,follow',
        'index,nofollow',
        'noindex,follow',
        'noindex,nofollow',
    ], 'index,follow'),
    'sitemap' => T::boolean('Include in Sitemap?', 'Exclude', 'Include', true),
    'priority' => T::number('Sitemap Priority', '1-100', 1, 100),
    'search' => T::boolean('Include in site search?', 'Exclude', 'Include', true),
];
