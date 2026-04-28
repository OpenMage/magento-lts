<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int|false given on the right side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int|false given on the right side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int<0, max>|false given on the right side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int|false given on the right side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int<0, max>|false given on the right side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, Mage_Core_Model_Config_Element given on the right side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, float|int|null given on the right side.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, int|null given on the right side.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
