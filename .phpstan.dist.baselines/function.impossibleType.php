<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function is_null() with Mage_Newsletter_Model_Queue will always evaluate to false.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Template.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
