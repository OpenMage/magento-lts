<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Block_Abstract::getChildData() should return mixed but return statement is missing.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Recurring_Profile::getInfoValue() should return mixed but return statement is missing.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Filter_Email::filter() should return mixed but return statement is missing.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Email.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
