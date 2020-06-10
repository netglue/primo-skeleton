<?php
declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

return T::slice(
    'Rich Text + Image',
    '2 Column Alternating Layout',
    [],
    [
        'content' => T::richText('Your words', null, T::blocksText(), true),
        'image' => T::img('Illustration', null, 800, 800),
    ],
    'vertical_split'
);
