<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in *, float|null given on the right side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency/Filter.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
