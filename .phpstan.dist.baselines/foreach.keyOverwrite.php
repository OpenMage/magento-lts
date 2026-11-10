<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $value with its key variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Block/Adminhtml/Export/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $productId with its key variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $correlationName with its key variable.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
