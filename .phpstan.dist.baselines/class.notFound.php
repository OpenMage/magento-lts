<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Call to method toHtml() on an unknown class Mage_Adminhtml_Block_Api_Tab_Useredit.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Api/Edituser.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to method toHtml() on an unknown class Mage_Adminhtml_Block_Api_Grid_User.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Api/Users.php',
];
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
