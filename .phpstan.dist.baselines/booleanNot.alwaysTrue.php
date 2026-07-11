<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Onepage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url/Rewrite.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url/Rewrite/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Service/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always true.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/system/store/tree.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
