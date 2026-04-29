<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Elseif condition is always true.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/Select.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
