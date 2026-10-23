<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Call to static method PHPUnit\\Framework\\Assert::assertTrue() with true and \'no exception…\' will always evaluate to true.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Catalog/Model/Product/Option/Type/FileTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
