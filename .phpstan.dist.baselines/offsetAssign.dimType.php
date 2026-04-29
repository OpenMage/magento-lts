<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot assign new offset to array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Renderer/Xml.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot assign offset \'from\' to array<mixed, mixed>|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Advanced.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot assign new offset to list|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Action/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot assign offset \'mode\' to array|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot assign new offset to array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/HTTP/Client/Socket.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
