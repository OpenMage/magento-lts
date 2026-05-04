<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter &$costArr by-ref type of method Mage_Usa_Model_Shipping_Carrier_Ups::processShippingRestRateForItem() expects array<float|string>, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
