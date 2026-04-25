<?php declare(strict_types = 1);

// total 9 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation "*" between string and 1 results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tax/Rate/Grid/Renderer/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation "+" between string and string results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation "-" between string and string results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation "." between Mage_Eav_Model_Entity_Interface and \'_collection\' results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Convert/Adapter/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation "+" between non-falsy-string and 1 results in an error.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Log.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation "-" between array|string and array|string results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation "*" between float and string results in an error.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
