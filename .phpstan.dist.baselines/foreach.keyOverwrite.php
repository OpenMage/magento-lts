<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $elmNamespace with its key variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $sectionName with its key variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Renderer/Xml/Writer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $websiteId with its key variable.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $value with its key variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Block/Adminhtml/Export/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $productId with its key variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $type with its key variable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $wsName with its key variable.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Convert/Parser/Xml/Excel.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $correlationName with its key variable.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Foreach overwrites $field with its key variable.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
