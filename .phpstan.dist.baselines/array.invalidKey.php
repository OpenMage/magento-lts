<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Possibly invalid array key type int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Possibly invalid array key type int|string|Zend_Db_Expr.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Possibly invalid array key type string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Possibly invalid array key type array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Helper/EnvironmentConfigLoaderTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Possibly invalid array key type bool|string.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Helper/EnvironmentConfigLoaderTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
