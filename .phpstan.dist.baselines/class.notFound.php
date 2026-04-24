<?php declare(strict_types = 1);

// total 3 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Call to method getCollection() on an unknown class Mage_Permissions_Model_Users.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/Grid/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to method getCollection() on an unknown class Mage_Permissions_Model_Roles.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/Usernroles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to method getCollection() on an unknown class Mage_Permissions_Model_Users.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/Usernroles.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
