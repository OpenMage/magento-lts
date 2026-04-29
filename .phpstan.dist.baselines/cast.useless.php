<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Casting to int something that\'s already int<min, -1>|int<1, max>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Attribute.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
