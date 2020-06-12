<?php
declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

$slices = [
    'prose' => require __DIR__ . '/../slices/prose.php',
    'header-illustration' => require __DIR__ . '/../slices/header-illustration.php',
    'prose-float' => require __DIR__ . '/../slices/prose-float.php',
    'code' => require __DIR__ . '/../slices/code.php',
];
$labels = [];

return T::sliceZone(
    'Document Body',
    $slices,
    $labels
);
