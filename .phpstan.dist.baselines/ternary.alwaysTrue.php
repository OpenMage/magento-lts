<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Ternary operator condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/Product/SetController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Ternary operator condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Ternary operator condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Messages.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Ternary operator condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/price.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Ternary operator condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/onepage/progress/shipping.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Ternary operator condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/price.phtml',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
