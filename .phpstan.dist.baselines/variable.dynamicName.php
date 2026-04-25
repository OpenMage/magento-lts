<?php declare(strict_types = 1);

// total 10 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Variable variables are not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable variables are not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable variables are not allowed.',
    'count' => 8,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Helper/Data.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
