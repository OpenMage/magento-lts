<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int|null given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Convert/Profile/Edit/Tab/Run.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int<0, max>|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, float|null given on the left side.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, float|int|string|null given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int<0, max>|false given on the left side.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Http.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int<0, max>|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int|false given on the left side.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Link/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int<0, max>|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Breadcrumbs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int<0, max>|false given on the left side.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, float|null given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int<0, max>|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../get.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int<0, max>|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in +, int|null given on the left side.',
    'count' => 3,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
