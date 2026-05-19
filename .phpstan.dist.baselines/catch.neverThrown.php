<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Dead catch - Exception is never thrown in the try block.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/Recurring/ProfileController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Dead catch - Exception is never thrown in the try block.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Dead catch - Mage_Core_Exception is never thrown in the try block.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/controllers/DownloadController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Dead catch - Mage_Core_Exception is never thrown in the try block.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Dead catch - Exception is never thrown in the try block.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Controller/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Dead catch - PhpUnitsOfMeasure\\Exception\\UnknownUnitOfMeasure is never thrown in the try block.',
    'count' => 2,
    'path' => __DIR__ . '/../tests/unit/Mage/Usa/Helper/DataTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
