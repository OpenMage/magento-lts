<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in post-increment, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in post-increment, int<1, max>|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
