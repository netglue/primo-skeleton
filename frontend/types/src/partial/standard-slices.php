<?php
declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

$slices = [
    'prose' => require __DIR__ . '/../slices/prose.php',
    'header-illustration' => require __DIR__ . '/../slices/header-illustration.php',
    'prose-float' => require __DIR__ . '/../slices/prose-float.php',
    'code' => require __DIR__ . '/../slices/code.php',
    'tagged-documents' => require __DIR__ . '/../slices/tagged-documents.php',
    'related-documents' => require __DIR__ . '/../slices/related-documents.php',
];
$labels = [];

return T::sliceZone(
    'Document Body',
    $slices,
    $labels
);
