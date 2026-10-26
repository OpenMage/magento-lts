<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Comparison operation "<" between int<80200, 80599> and 80200 is always false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Validator/Image.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
