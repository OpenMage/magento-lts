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

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
