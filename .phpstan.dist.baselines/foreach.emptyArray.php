<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Empty array passed to foreach.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Cart.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
