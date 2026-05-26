<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in *, float|int|string|null given on the left side.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
