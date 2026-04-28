<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Comparison operation ">" between int<0, 100> and 100 is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Front.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
