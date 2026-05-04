<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Payment_Model_Recurring_Profile::exportStartDatetime() should return string|Zend_Date but empty return statement found.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
