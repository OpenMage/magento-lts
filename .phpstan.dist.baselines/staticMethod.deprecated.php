<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getVersionInfo() of class Mage.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
