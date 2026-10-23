<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Elseif condition is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Rest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
