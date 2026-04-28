<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Strict comparison using === between mixed and null will always evaluate to false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Lock.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Strict comparison using === between non-empty-list and array{} will always evaluate to false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Strict comparison using === between mixed and false will always evaluate to false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Simplexml/Config.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
