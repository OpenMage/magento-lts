<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Static property OpenMage\\Tests\\Unit\\Mage\\Core\\Model\\Layout\\ValidatorTest::$subject is never read, only written.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/Layout/ValidatorTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
