<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison using == between true and true will always evaluate to true.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
