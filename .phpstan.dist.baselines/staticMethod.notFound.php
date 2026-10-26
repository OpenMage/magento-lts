<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Call to an undefined static method Mage_Catalog_Model_Product_Type_Price|Mage_Core_Model_Abstract::calculatePrice().',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to an undefined static method Mage_Catalog_Model_Product_Type_Price|Mage_Core_Model_Abstract::calculatePrice().',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to an undefined static method Mage_Core_Model_Config_Data::afterSave().',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Config/Price/Include.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
