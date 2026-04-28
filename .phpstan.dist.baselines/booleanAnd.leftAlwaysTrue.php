<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Left side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Left side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Link/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Left side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Left side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Left side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/price.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Left side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/multishipping/address/select.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Left side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/price.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Left side of && is always true.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Cache/Backend/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Left side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Convert/Mapper/Column.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
