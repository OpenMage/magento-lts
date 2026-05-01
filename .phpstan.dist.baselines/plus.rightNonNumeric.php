<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, float|int|string|null given on the right side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, float|int|null given on the right side.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
