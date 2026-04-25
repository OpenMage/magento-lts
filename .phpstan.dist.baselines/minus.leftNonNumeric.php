<?php declare(strict_types = 1);

// total 1 error

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only numeric types are allowed in -, SimpleXMLElement given on the left side.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Xml/Excel.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
