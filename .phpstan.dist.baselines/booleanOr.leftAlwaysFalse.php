<?php declare(strict_types = 1);

// total 1 error

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Left side of || is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template/Tokenizer/Variable.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
