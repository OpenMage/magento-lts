<?php declare(strict_types = 1);

// total 47 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Eav/Source.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Type/Configurable/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Resource/Indexer/Stock.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Resource/Indexer/Stock/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Model/Resource/Catalog/Product/Attribute/Super/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Fallback.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Iterator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, array|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Customer/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, array|null given.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Grouped.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Log.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Visitor/Online.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, int<0, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sitemap/Model/Resource/Catalog/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sitemap/Model/Resource/Cms/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Resource/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Only booleans are allowed in a while condition, mixed given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Cache/Backend/Database.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
