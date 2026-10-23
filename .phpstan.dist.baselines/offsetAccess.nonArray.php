<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot use array destructuring on array<mixed>|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Wsi/Handler.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot use array destructuring on array|true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot use array destructuring on list<int|null>|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productimg.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot use array destructuring on array<int|string, int|string>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Validator/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot use array destructuring on array|true.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot use array destructuring on array<int|string, int|string>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
