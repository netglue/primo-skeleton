<?php
declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

return T::slice(
    'Illustration Header',
    'An illustration with a header text',
    [
        'title' => T::richText('Your words', null, [T::H1, T::H2, T::B, T::I], false),
        'lead' => T::richText('Lead Text', null, [T::P], false),
        'image' => T::img('Illustration', null, 800, 800),
    ],
    [],
    'vertical_split'
);
