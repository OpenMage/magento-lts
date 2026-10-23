<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Db_Adapter_Pdo_Mysql@anonymous/tests/unit/Mage/SalesRule/Model/Resource/CouponTest.php:34::fetchOne() never returns null so it can be removed from the return type.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/SalesRule/Model/Resource/CouponTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
