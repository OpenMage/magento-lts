<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $destination in isset() always exists and is always null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
