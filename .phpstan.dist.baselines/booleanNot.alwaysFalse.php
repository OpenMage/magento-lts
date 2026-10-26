<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Coupon/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Status/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Coupon.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/persistent/checkout/onepage/login.phtml',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
