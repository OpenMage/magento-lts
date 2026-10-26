<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only iterables can be unpacked, list<string>|string given in argument #1.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Report/CustomerController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only iterables can be unpacked, list<string>|string given in argument #1.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Report/ProductController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only iterables can be unpacked, list<string>|string given in argument #1.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Report/ReviewController.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
