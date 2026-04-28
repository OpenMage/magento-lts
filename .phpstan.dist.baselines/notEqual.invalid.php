<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Comparison operation "!=" between array|int<min, -1>|int<1, max> and array{0} results in an error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Report/Collection/Abstract.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
