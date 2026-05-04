<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation ".=" between array<string|null>|string|null and non-falsy-string results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation "*=" between float|string and 1 results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation ".=" between non-falsy-string and array<string|null>|string|null results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Block/Editable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation ".=" between Mage_Sales_Model_Order_Status_History|string and non-falsy-string results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation "+=" between string|null and -1|1 results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Coupon/Usage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation ".=" between 0|0.0|\'\'|\'0\'|array{}|false|null and \'<ul class="messages…\' results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/core/messages.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Binary operation ".=" between int|list<mixed>|object|string and non-falsy-string results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Debug.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
