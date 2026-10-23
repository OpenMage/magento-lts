<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Trait OpenMage\\Tests\\Unit\\Traits\\DataProvider\\Mage\\Dataflow\\Model\\Convert\\Adapter\\Http\\CurlTrait is used zero times and is not analysed.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Traits/DataProvider/Mage/Dataflow/Model/Convert/Adapter/Http/CurlTrait.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Trait OpenMage\\Tests\\Unit\\Traits\\DataProvider\\Mage\\Downloadable\\Helper\\DownloadTrait is used zero times and is not analysed.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Traits/DataProvider/Mage/Downloadable/Helper/DownloadTrait.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Trait OpenMage\\Tests\\Unit\\Traits\\DataProvider\\Mage\\Oauth\\Model\\ServerTrait is used zero times and is not analysed.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Traits/DataProvider/Mage/Oauth/Model/ServerTrait.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Trait OpenMage\\Tests\\Unit\\Traits\\DataProvider\\Mage\\SalesRule\\Model\\Resource\\Coupon\\UsageTrait is used zero times and is not analysed.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Traits/DataProvider/Mage/SalesRule/Model/Resource/Coupon/UsageTrait.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Trait OpenMage\\Tests\\Unit\\Traits\\DataProvider\\Mage\\SalesRule\\Model\\Resource\\CouponTrait is used zero times and is not analysed.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Traits/DataProvider/Mage/SalesRule/Model/Resource/CouponTrait.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
