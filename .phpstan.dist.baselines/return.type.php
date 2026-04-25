<?php declare(strict_types = 1);

// total 12 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Config::getResourceGroup() should return bool|Mage_Core_Model_Config_Element but returns Varien_Simplexml_Element.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Model_Product_Price::getOptions() should return Mage_Bundle_Model_Resource_Option_Collection but returns array.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Indexer_Abstract::_getAttribute() should return Mage_Catalog_Model_Resource_Eav_Attribute but returns Mage_Eav_Model_Entity_Attribute_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogInventory_Model_Resource_Stock_Item_Collection::_initSelect() should return $this(Mage_CatalogInventory_Model_Resource_Stock_Item_Collection) but returns Varien_Db_Select.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Resource/Stock/Item/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogSearch_Model_Advanced::getProductCollection() should return Mage_CatalogSearch_Model_Resource_Advanced_Collection but returns array|float|int|string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Advanced.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Url::getRequest() should return Mage_Core_Controller_Request_Http but returns Zend_Controller_Request_Http.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Config::getAttribute() should return Mage_Eav_Model_Entity_Attribute_Abstract|false but returns Mage_Eav_Model_Entity_Attribute_Interface.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Abstract::getAttribute() should return Mage_Catalog_Model_Resource_Eav_Attribute|false but returns Mage_Eav_Model_Entity_Attribute_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ImportExport_Block_Adminhtml_Export_Filter::prepareCollection() should return Mage_Eav_Model_Resource_Entity_Attribute_Collection but returns Varien_Data_Collection|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Block/Adminhtml/Export/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ProductAlert_Helper_Data::createBlock() should return Mage_ProductAlert_Block_Email_Price|Mage_ProductAlert_Block_Email_Stock but returns Mage_Core_Block_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Rule_Model_Condition_Product_Abstract::getAttributeObject() should return Mage_Catalog_Model_Resource_Eav_Attribute but returns Mage_Eav_Model_Entity_Attribute_Abstract|Varien_Object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Simplexml_Config::getXpath() should return array<Varien_Simplexml_Element>|false but returns non-empty-array<SimpleXMLElement>.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Simplexml/Config.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
