<?php declare(strict_types = 1);

// total 1 error

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, Mage_Core_Model_Config_Element given on the right side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Session.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
