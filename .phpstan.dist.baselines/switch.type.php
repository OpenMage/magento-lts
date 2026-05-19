<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Switch condition type (true) does not match case condition $encodedValue === \\false (false).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Switch condition type (true) does not match case condition $encodedValue === \\null (false).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Switch condition type (true) does not match case condition $encodedValue === \\true (false).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Switch condition type (string|false) does not match case condition self::ADDITIONAL_PROTECTION_ROUNDING_CEIL (int).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Switch condition type (string|false) does not match case condition self::ADDITIONAL_PROTECTION_ROUNDING_FLOOR (int).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Switch condition type (string|false) does not match case condition self::ADDITIONAL_PROTECTION_ROUNDING_ROUND (int).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Switch condition type (string|false) does not match case condition self::ADDITIONAL_PROTECTION_VALUE_CONFIG (int).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Switch condition type (string|false) does not match case condition self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL (int).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Switch condition type (string|false) does not match case condition self::ADDITIONAL_PROTECTION_VALUE_SUBTOTAL_WITH_DISCOUNT (int).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
