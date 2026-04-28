<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Result of && is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Simplexml/Config.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
