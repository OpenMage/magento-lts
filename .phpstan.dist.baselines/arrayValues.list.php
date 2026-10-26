<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array (array{non-empty-string, non-empty-string}) of array_values is already a list, call has no effect.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array (non-empty-list) of array_values is already a list, call has no effect.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
