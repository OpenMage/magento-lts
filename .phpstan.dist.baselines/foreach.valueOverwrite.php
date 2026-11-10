<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $type with its value variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $product with its value variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $address with its value variable.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $rate with its value variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $rate with its value variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $package with its value variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $template with its value variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Model/Widget/Instance.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $row with its value variable.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
