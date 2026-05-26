<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, float|null given on the left side.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, float|int|string|null given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int<0, max>|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Front.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int<0, max>|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int<0, max>|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url/Rewrite/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, SimpleXMLElement given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Xml/Excel.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int<0, max>|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int|false given on the left side.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Entity/Summary/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int|false given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Index/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, float|int|null given on the left side.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
