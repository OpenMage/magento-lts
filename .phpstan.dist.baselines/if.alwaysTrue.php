<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Api/Tab/Rolesedit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/Tab/Rolesedit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Search/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Resource/Link.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Resource/Sample.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/onepage/progress/shipping.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/persistent/checkout/onepage/login.phtml',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
