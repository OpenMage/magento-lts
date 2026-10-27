<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @method for _getResource() references deprecated class Mage_Admin_Model_Resource_Acl_Role.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Acl/Role.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @method for getResource() references deprecated class Mage_Admin_Model_Resource_Acl_Role.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Acl/Role.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @method for getCollection() references deprecated class Mage_Directory_Model_Resource_Currency_Collection:
since 1.5.0.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @method for getResourceCollection() references deprecated class Mage_Directory_Model_Resource_Currency_Collection:
since 1.5.0.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
