<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Right side of && is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Right side of && is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Right side of && is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template/Tokenizer/Parameter.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
