<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Strict comparison using !== between mixed and 0 will always evaluate to true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Strict comparison using !== between array{\'link_id = ?\': mixed}|array{\'link_id in (?)\': array<mixed, mixed>}|array{\'sample_id = ?\': mixed} and array{} will always evaluate to true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Resource/Link.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Strict comparison using !== between array{\'sample_id = ?\': mixed}|array{\'sample_id in (?)\': mixed} and array{} will always evaluate to true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Resource/Sample.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
