<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot cast int|Mage_Sales_Model_Order_Payment|string|null to int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Payment/Transaction/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot cast object to int.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
