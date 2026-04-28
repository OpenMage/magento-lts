<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot cast array<string>|string to string.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Block/Adminhtml/System/Config/Form/Field/Usps/AbstractTestButton.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot cast array<string>|string to string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Block/Checkout/Address/Verification.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot cast string|Varien_Object to string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Observer/PrepareShipmentForLabels.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot cast object|string to string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot cast object to string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
