<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Comparison operation "==" between array|null and 1 results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Comparison operation "==" between array|null and 2 results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
