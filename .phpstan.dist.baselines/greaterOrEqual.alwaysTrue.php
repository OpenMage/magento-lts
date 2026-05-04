<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Comparison operation ">=" between int<0, max> and 0 is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
