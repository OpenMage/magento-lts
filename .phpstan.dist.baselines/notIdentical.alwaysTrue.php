<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Strict comparison using !== between mixed and 0 will always evaluate to true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
