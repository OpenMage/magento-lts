<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'For loop initial assignment overwrites variable $realPathParts.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'For loop initial assignment overwrites variable $result.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
