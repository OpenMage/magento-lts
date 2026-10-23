<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Unreachable statement - code above always terminates.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url/Rewrite.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Unreachable statement - code above always terminates.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url/Rewrite/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Unreachable statement - code above always terminates.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Coupon/Usage.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
