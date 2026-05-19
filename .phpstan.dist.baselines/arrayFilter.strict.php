<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Helper/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Array.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Resource/Rating.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Lowstock/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Label/Pdf/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Label/Pdf/PageBuilder.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps/Rest/Client.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to function array_filter() requires parameter #2 to be passed to avoid loose comparison semantics.',
    'count' => 3,
    'path' => __DIR__ . '/../shell/translations.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
