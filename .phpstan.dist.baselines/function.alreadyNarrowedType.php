<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function is_array() with non-empty-array will always evaluate to true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function is_numeric() with int will always evaluate to true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
