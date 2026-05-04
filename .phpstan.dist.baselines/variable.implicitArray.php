<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Implicit array creation is not allowed - variable $pricing does not exist.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Type/Configurable/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Implicit array creation is not allowed - variable $termKeys might not exist.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Block/Term.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Implicit array creation is not allowed - variable $fields might not exist.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Session/Parser/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Implicit array creation is not allowed - variable $itemCollsX might not exist.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Shipment/Packaging.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Implicit array creation is not allowed - variable $result might not exist.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Rate/Result.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Implicit array creation is not allowed - variable $tmp might not exist.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Rate/Result.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Implicit array creation is not allowed - variable $classToRate might not exist.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Implicit array creation is not allowed - variable $ratesByType might not exist.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Implicit array creation is not allowed - variable $data does not exist.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Implicit array creation is not allowed - variable $params might not exist.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/tag/customer/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Implicit array creation is not allowed - variable $fields might not exist.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Convert/Parser/Csv.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
