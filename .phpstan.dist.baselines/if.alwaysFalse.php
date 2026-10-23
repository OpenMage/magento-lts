<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always false.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer/Console.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always false.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'If condition is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Total/Quote/Weee.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
