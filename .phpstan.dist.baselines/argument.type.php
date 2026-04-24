<?php declare(strict_types = 1);

// total 128 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $grid of method Mage_Reports_Model_Totals::countTotals() expects Mage_Adminhtml_Block_Report_Product_Grid, $this(Mage_Adminhtml_Block_Report_Grid) given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cache of method Varien_Simplexml_Config::setCache() expects Varien_Simplexml_Config_Cache_Abstract, Zend_Cache_Core given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $field of method Mage_Adminhtml_Model_Config_Data::_isValidField() expects Mage_Core_Model_Config_Element, Varien_Simplexml_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Config/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Customer/System/Config/ValidatevatController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleModel of method Mage_Adminhtml_Tax_RuleController::_isValidRuleRequest() expects Mage_Tax_Model_Calculation_Rule, Mage_Core_Model_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Tax/RuleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $resource of method Mage_Api_Model_Config::loadAclResources() expects Mage_Core_Model_Config_Element|null, Varien_Simplexml_Element given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $code of class SoapFault constructor expects array|string|null, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Adapter/Soap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $source of static method Mage_Api_Model_Wsdl_Config_Element::_getChildren() expects Varien_Simplexml_Element, SimpleXMLElement given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $role of method Zend_Acl::hasRole() expects string|Zend_Acl_Role_Interface, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $role of method Zend_Acl::isAllowed() expects string|Zend_Acl_Role_Interface|null, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cache of method Varien_Simplexml_Config::setCache() expects Varien_Simplexml_Config_Cache_Abstract, Zend_Cache_Core given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $lowerOrEqualsTo of method Mage_Api2_Model_Config::getResourceLastVersion() expects int|null, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Dispatcher.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $renderer of method Mage_Api2_Model_Resource::setRenderer() expects Mage_Api2_Model_Renderer_Interface, Mage_Core_Model_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $code of method Mage_Api2_Model_Response::addMessage() expects string, int given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $selectionProduct of method Mage_Bundle_Model_Product_Price::getSelectionFinalTotalPrice() expects Mage_Catalog_Model_Product, Mage_Bundle_Model_Selection given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $selection of method Mage_Bundle_Model_Option::addSelection() expects Mage_Bundle_Model_Selection, Mage_Catalog_Model_Product given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Option/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%d"), bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 of function sprintf is expected to be int by placeholder #3 ("%%d"), bool given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #5 of function sprintf is expected to be int by placeholder #4 ("%%d"), bool given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attribute of method Mage_Catalog_Model_Layer::_filterFilterableAttributes() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $index of method Mage_Catalog_Model_Resource_Layer_Filter_Decimal::applyFilterToCollection() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Decimal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributes of method Mage_Catalog_Model_Api_Resource::_isAllowedAttribute() expects array|null, stdClass|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $skeletonId of method Mage_Eav_Model_Entity_Attribute_Set::initFromSkeleton() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Set/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $heigth of method Varien_Image::setWatermarkHeigth() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Catalog_Model_Resource_Attribute::_clearUselessAttributeValues() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $newOptionId of method Mage_Catalog_Model_Product_Option_Value::duplicate() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #8 $productId of static method Mage_Catalog_Model_Product_Type_Price::calculatePrice() expects int|null, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $aggregationId of method Mage_CatalogIndex_Model_Resource_Aggregation::_saveTagRelations() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Aggregation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $storeIds of method Mage_CatalogIndex_Model_Resource_Setup::_setWebsiteInfo() expects array, Mage_Core_Model_Website given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%2$d"), bool given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Resource/Stock.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #5 of function sprintf is expected to be int by placeholder #4 ("%%4$d"), bool given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Resource/Stock.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $combine of method Mage_CatalogRule_Model_Observer::_removeAttributeFromConditions() expects Mage_CatalogRule_Model_Rule_Condition_Combine, Mage_Rule_Model_Condition_Combine given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product of method Mage_CatalogRule_Model_Resource_Rule::applyAllRules() expects int|Mage_Catalog_Model_Product|null, Mage_Core_Model_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $product of method Mage_CatalogRule_Model_Resource_Rule::applyToProduct() expects Mage_Catalog_Model_Product, Mage_Core_Model_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $typeId of method Mage_CatalogSearch_Model_Resource_Fulltext::_getProductTypeInstance() expects string, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $typeId of method Mage_CatalogSearch_Model_Resource_Fulltext::_getProductChildrenIds() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $message of method Mage_Checkout_Model_Session::addQuoteItemMessage() expects Mage_Core_Model_Message, Mage_Core_Model_Message_Error|Mage_Core_Model_Message_Notice|Mage_Core_Model_Message_Success|Mage_Core_Model_Message_Warning given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attr of method Mage_ConfigurableSwatches_Helper_Data::attrIsSwatchType() expects int|Mage_Eav_Model_Attribute, Mage_Catalog_Model_Resource_Eav_Attribute given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Block/Catalog/Layer/State/Swatch.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attr of method Mage_ConfigurableSwatches_Helper_Data::attrIsSwatchType() expects int|Mage_Eav_Model_Attribute, Mage_Catalog_Model_Resource_Eav_Attribute given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Zend_Controller_Response_Abstract::setHeader() expects string, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Front/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Zend_Controller_Response_Abstract::setHeader() expects string, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $useRouterName of method Mage_Core_Controller_Varien_Router_Standard::collectRoutes() expects string, bool given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Admin.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(string): bool)|null, Closure(string, string=): string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/EnvironmentConfigLoader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $arguments of static method Mage::getResourceModel() expects array, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $constructArguments of method Mage_Core_Model_Config::getModelInstance() expects array|object, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cache of method Varien_Simplexml_Config::setCache() expects Varien_Simplexml_Config_Cache_Abstract, Zend_Cache_Core given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Varien_Simplexml_Config::setNode() expects string, null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $policyCode of method Mage_Core_Model_Domainpolicy::_getDomainPolicyByCode() expects string, int given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Domainpolicy.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Mage_Core_Model_Email_Template::setId() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_App::getStore() expects bool|int|Mage_Core_Model_Store|string|null, Varien_Object|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $params of method Mage_Core_Model_File_Storage_Database_Abstract::__construct() expects array, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Storage/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $params of method Mage_Core_Model_File_Storage_Database_Abstract::__construct() expects array, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Storage/Directory/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $node of method Varien_Simplexml_Config::setXml() expects Varien_Simplexml_Element, SimpleXMLElement given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $node of method Varien_Simplexml_Config::setXml() expects Varien_Simplexml_Element, SimpleXMLElement|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sourceData of method Varien_Simplexml_Config::__construct() expects string|Varien_Simplexml_Element|null, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $adapter of method Varien_Db_Adapter_Interface::setCacheAdapter() expects Zend_Cache_Backend_Interface, Zend_Cache_Core given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Mage_Core_Model_Config_Element::is() expects string|true, 1 given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $host of method Mage_Core_Model_Session_Abstract::addHost() expects string, true given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $scope of method Mage_Core_Model_Translate::_addData() expects string, false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Translate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $storeId of method Mage_Core_Model_Resource_Translate_String::deleteTranslate() expects int|null, false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Translate/Inline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $entityId of method Mage_Eav_Model_Entity_Abstract::load() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attr of method Mage_ConfigurableSwatches_Helper_Data::attrIsSwatchType() expects int|Mage_Eav_Model_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Block/Adminhtml/Attribute/Edit/Options/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $modelClass of static method Mage::getResourceModel() expects string, Mage_Eav_Model_Entity_Interface given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Convert/Adapter/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute of method Mage_Eav_Model_Resource_Entity_Attribute_Option::addOptionValueToCollection() expects Mage_Eav_Model_Entity_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $code of method Mage_Eav_Model_Entity_Setup::updateEntityType() expects string, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $value of method Mage_Eav_Model_Entity_Setup::updateEntityType() expects string|null, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attribute of static method Mage_Eav_Model_Attribute_Data::factory() expects Mage_Eav_Model_Attribute, Mage_Eav_Model_Entity_Attribute given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $str of method Mage_Core_Helper_UnserializeArray::unserialize() expects string|null, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(mixed): int<0, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 ...$values of function sprintf expects bool|float|int|string|null, array<string, int|string>|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Lock.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $adapter of method Varien_Db_Adapter_Interface::setCacheAdapter() expects Zend_Cache_Backend_Interface, Zend_Cache_Core given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Resource/Lock/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Mage_Core_Model_Resource_Setup::setConfigData() expects string, int given.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Block/Authorizenet/Form/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%02d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Block/Info/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Mage_Core_Model_Config::saveConfig() expects string, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Fieldset/Deprecated.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $setToken of method Mage_Paypal_Controller_Express_Abstract::_initToken() expects string|null, false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%02d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $amount of method Mage_Paypal_Model_Express::_callDoAuthorize() expects int, float given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $datetime of class DateTime constructor expects string, null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express/Checkout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $amount of method Mage_Sales_Model_Order_Payment::registerCaptureNotification() expects float, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $url of method Zend_Http_Client_Adapter_Interface::write() expects Zend_Uri_Http, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment of method Mage_Paypal_Model_Payflowpro::_buildBasicRequest() expects Mage_Sales_Model_Order_Payment, Mage_Payment_Model_Info given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%02d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/PaypalUk/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $interval of method Varien_Db_Adapter_Interface::getDateAddSql() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $order of method Mage_Sales_Model_Order_Payment_Transaction::setOrder() expects bool|Mage_Sales_Model_Order_Payment|null, Mage_Sales_Model_Order given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shouldSave of method Mage_Sales_Model_Order_Payment_Transaction::closeAuthorization() expects bool, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $txnId of method Mage_Sales_Model_Order_Payment_Transaction::_beforeLoadByTxnId() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $text of method Zend_Pdf_Canvas_Abstract::drawText() expects string, (float|int) given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Shipment/Packaging.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Sales_Model_Order_Shipment_Api::_getCarriers() expects Mage_Eav_Model_Entity_Abstract, Mage_Sales_Model_Order given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Sales_Model_Order_Shipment_Api::_getCarriers() expects Mage_Eav_Model_Entity_Abstract, Mage_Sales_Model_Order_Shipment given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Sales_Model_Order_Shipment_Api::_getCarriers() expects Mage_Eav_Model_Entity_Abstract, Mage_Sales_Model_Order given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Service/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $combine of method Mage_SalesRule_Model_Observer::_removeAttributeFromConditions() expects Mage_Rule_Model_Condition_Combine, Mage_Rule_Model_Action_Collection given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be float by placeholder #1 ("%%.1f"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sitemap/Model/Sitemap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Tag_Model_Resource_Tag_Collection::addProductFilter() expects int, true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Product/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(mixed): int<0, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Controller/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute of method Mage_Catalog_Block_Product_Compare_List::getProductAttributeValue() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute of method Mage_Catalog_Block_Product_Compare_List::getProductAttributeValue() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_sum expects an array of values castable to number, list<string> given.',
    'count' => 3,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $mode of function ftp_fput expects 1|2, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $chunks of method Varien_Cache_Backend_Memcached::_cleanTheMess() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Backend/Memcached.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(mixed): array<int<0, max>, non-empty-string> given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $index of method Varien_Data_Form_Element_Abstract::getEscapedValue() expects string|null, int<0, max> given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Multiline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $result of method Varien_Db_Adapter_Pdo_Mysql::_debugStat() expects Zend_Db_Statement_Pdo|null, PDOStatement|Zend_Db_Statement_Interface given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of function simplexml_load_string expects string, true given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of method Varien_Simplexml_Element::asNiceXml() expects string, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Simplexml/Element.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
