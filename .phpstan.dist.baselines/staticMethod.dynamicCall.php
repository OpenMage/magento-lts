<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Dynamic call to static method Mage_Adminhtml_Helper_Data::getUrl().',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Checkout/Formkey.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
