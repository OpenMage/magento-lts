<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Comparison operation "<" between int<1, max>|null and *NEVER* results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
