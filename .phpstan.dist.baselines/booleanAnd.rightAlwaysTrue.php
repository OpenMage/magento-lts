<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Right side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/Order/CreditmemoController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Right side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Type/Db/Mysqli.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Right side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Right side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Right side of && is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Resource/Tax.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
