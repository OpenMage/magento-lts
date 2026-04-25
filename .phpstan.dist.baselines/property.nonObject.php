<?php declare(strict_types = 1);

// total 2 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot access property $_items on array.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
