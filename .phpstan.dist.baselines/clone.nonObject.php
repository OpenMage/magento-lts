<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot clone Mage_Catalog_Model_Resource_Eav_Attribute|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot clone non-object variable $widgets of type Varien_Simplexml_Element|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Model/Widget.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
