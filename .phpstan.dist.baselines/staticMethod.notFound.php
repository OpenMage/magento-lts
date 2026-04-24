<?php declare(strict_types = 1);

// total 1 error

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Call to an undefined static method Mage_Core_Model_Config_Data::afterSave().',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Config/Price/Include.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
