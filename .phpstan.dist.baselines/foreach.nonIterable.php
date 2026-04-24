<?php declare(strict_types = 1);

// total 4 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Argument of an invalid type string supplied for foreach, only iterables are supported.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Navigation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Argument of an invalid type string supplied for foreach, only iterables are supported.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Front.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Argument of an invalid type Mage_Reports_Model_Report supplied for foreach, only iterables are supported.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Totals.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Argument of an invalid type Zend_Db_Statement_Interface supplied for foreach, only iterables are supported.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Db/Object/Trigger.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
