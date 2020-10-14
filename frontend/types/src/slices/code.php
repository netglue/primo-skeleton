<?php

declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

return T::slice(
    'Code Block',
    'Syntax highlighted code',
    [
        'language' => T::select('Language', null, [
            'html',
            'css',
            'xml',
            'php',
            'javascript',
            'http',
            'haskell',
            'json',
            'shell',
            'yaml',
            'sql',
        ], 'php'),
        'code' => T::richText(
            'Your code',
            null,
            [T::PRE],
            false
        ),
    ],
    [],
    'code'
);
