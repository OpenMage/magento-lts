<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of function is_dir expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of function is_readable expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of method Mage_Core_Model_Logger::log() expects string, array|object|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $observer of method Varien_Event_Collection::addObserver() expects Varien_Event_Observer, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $options of static method Mage::_setConfigModel() expects array, array|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $options of static method Mage::_setIsInstalled() expects array, array|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $level of method Mage_Core_Model_Logger::log() expects array{\'DEBUG\', \'INFO\', \'NOTICE\', \'WARNING\', \'ERROR\', \'CRITICAL\', \'ALERT\', \'EMERGENCY\'}|array{100, 200, 250, 300, 400, 500, 550, 600}|Monolog\\Level::Alert|Monolog\\Level::Critical|Monolog\\Level::Debug|Monolog\\Level::Emergency|Monolog\\Level::Error|Monolog\\Level::Info|Monolog\\Level::Notice|Monolog\\Level::Warning|null, array{\'DEBUG\', \'INFO\', \'NOTICE\', \'WARNING\', \'ERROR\', \'CRITICAL\', \'ALERT\', \'EMERGENCY\'}|array{100, 200, 250, 300, 400, 500, 550, 600}|int|Monolog\\Level::Alert|Monolog\\Level::Critical|Monolog\\Level::Debug|Monolog\\Level::Emergency|Monolog\\Level::Error|Monolog\\Level::Info|Monolog\\Level::Notice|Monolog\\Level::Warning|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $file of method Mage_Core_Model_Logger::log() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $parent of method Mage_Admin_Model_Acl::addRoleParent() expects string|Zend_Acl_Role, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $rolesArr of method Mage_Admin_Model_Resource_Acl::loadRoles() expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $rulesArr of method Mage_Admin_Model_Resource_Acl::loadRules() expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $assert of method Zend_Acl::allow() expects Zend_Acl_Assert_Interface|null, object|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $assert of method Zend_Acl::deny() expects Zend_Acl_Assert_Interface|null, object|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $encodedValue of method Mage_Core_Helper_Data::jsonDecode() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Block.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_intersect expects array, array|Varien_Simplexml_Element given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Rules.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $encodedValue of method Mage_Core_Helper_Data::jsonDecode() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Variable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key of method Varien_Object::setData() expects array|string, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $withoutTime of static method Varien_Date::now() expects bool, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of class SimpleXMLElement constructor expects string, SimpleXMLElement|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/AdminNotification/Model/Feed.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_split expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/AdminNotification/Model/Feed.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function array_key_exists expects array, array|Varien_Simplexml_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Api/Tab/Rolesedit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $groupId of method Mage_Eav_Model_Entity_Attribute_Abstract::isInGroup() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $groupId of method Mage_Eav_Model_Resource_Entity_Attribute_Collection::setAttributeGroupFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Attribute/Set/Main.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, Mage_Core_Block_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Options/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $websiteId of method Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Websites::hasWebsite() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Websites.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $groupId of method Mage_Catalog_Model_Product::getAttributes() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $type of method Mage_Core_Model_Layout::createBlock() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, Mage_Core_Block_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Helper/Form/Gallery/Content.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_CatalogSearch_Model_Query::setStoreId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Search/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of method Mage_Cms_Model_Wysiwyg_Images_Storage::getFilesCollection() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Cms/Wysiwyg/Images/Content/Files.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customer of method Mage_Newsletter_Model_Subscriber::loadByCustomer() expects Mage_Customer_Model_Customer, Mage_Customer_Model_Customer|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Newsletter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customer of method Mage_Log_Model_Customer::loadByCustomer() expects int|Mage_Log_Model_Customer, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Wishlist_Model_Resource_Item_Collection::addCustomerIdFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be string by placeholder #2 ("%%s"), int|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Form/Element/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 ...$arg1 of function max expects non-empty-array, array<mixed> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Graph.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 ...$arg1 of function min expects non-empty-array, array<mixed> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Graph.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function implode expects array, array|object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Graph.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_Template::emulateDesign() expects int|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Queue/Preview.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $elementId of method Varien_Data_Form_Abstract::addField() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Queue/Preview/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function addcslashes expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Template/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_Template::emulateDesign() expects int|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Template/Preview.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $elementId of method Varien_Data_Form_Abstract::addField() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Template/Preview/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $response_str of static method Zend_Http_Response::extractCode() expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Notification/Security.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $parent of method Mage_Adminhtml_Block_Page_Menu::_buildMenuArray() expects Varien_Simplexml_Element, Varien_Simplexml_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Page/Menu.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function array_key_exists expects array, array|Varien_Simplexml_Element given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/Tab/Rolesedit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $separator of function explode expects non-empty-string, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Promo/Widget/Chooser/Daterange.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $date of class Zend_Date constructor expects array|int|string|Zend_Date|null, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $grid of method Mage_Reports_Model_Totals::countTotals() expects Mage_Adminhtml_Block_Report_Product_Grid, $this(Mage_Adminhtml_Block_Report_Grid) given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeIds of method Mage_Reports_Model_Resource_Tag_Collection::addPopularity() expects array|int, array|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Tag/Popular/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Rating_Model_Resource_Rating_Collection::setStoreFilter() expects array|int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Review/Rating/Detailed.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, Mage_Adminhtml_Block_Sales_Grid given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $collection of method Mage_Adminhtml_Block_Widget_Grid::setCollection() expects Varien_Data_Collection, Mage_Core_Model_Resource_Db_Collection_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Creditmemo/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $collection of method Mage_Adminhtml_Block_Widget_Grid::setCollection() expects Varien_Data_Collection, Mage_Core_Model_Resource_Db_Collection_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Invoice/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Wishlist_Model_Resource_Wishlist_Collection::filterByCustomerId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Items/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Search/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Catalog_Model_Resource_Product_Compare_Item_Collection::setCustomerId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Sidebar/Compared.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Catalog_Model_Resource_Product_Compare_Item_Collection::setCustomerId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Sidebar/Pcompared.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Collection_Abstract<Mage_Catalog_Model_Product>::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Sidebar/Pcompared.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subjectId of method Mage_Reports_Model_Resource_Event_Collection::addRecentlyFiler() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Sidebar/Pviewed.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $collection of method Mage_Adminhtml_Block_Widget_Grid::setCollection() expects Varien_Data_Collection, Mage_Core_Model_Resource_Db_Collection_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shipmentId of method Mage_Sales_Model_Resource_Order_Shipment_Item_Collection::setShipmentFilter() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Shipment/Packaging/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $orderId of method Mage_Sales_Model_Resource_Order::aggregateProductsByTypes() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Adminhtml_Block_Sales_Order_View_Info::_prepareAccountDataSortOrder() expects array<int, array<string, array<string>|string|null>>, array<int, array<string, mixed>> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function implode expects array<string>, array<int, array<string|null>|string|null> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $collection of method Mage_Adminhtml_Block_Widget_Grid::setCollection() expects Varien_Data_Collection, Mage_Core_Model_Resource_Db_Collection_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Shipment/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, object|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $separator of function explode expects non-empty-string, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $type of static method Mage::getBaseUrl() expects \'direct_link\'|\'js\'|\'link\'|\'media\'|\'skin\'|\'web\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/Form/Field/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/Form/Field/Select/Allowspecific.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $collection of method Mage_Adminhtml_Block_Widget_Grid::setCollection() expects Varien_Data_Collection, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Email/Template/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_App_Emulation::startEnvironmentEmulation() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Email/Template/Preview.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $groupId of method Mage_Core_Model_Resource_Store_Collection::addGroupFilter() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $website of method Mage_Core_Model_Resource_Store_Group_Collection::addWebsiteFilter() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, Mage_Adminhtml_Block_Tag_Tab_All given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Tag_Model_Resource_Tag_Collection::addStoreFilter() expects array|int, array<array|bool|string|void|Zend_Db_Expr>|int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tag/Grid/All.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Tag_Model_Resource_Tag_Collection::addStoreFilter() expects array|int, array<array|bool|string|void|Zend_Db_Expr>|int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tag/Grid/Pending.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Tag_Model_Resource_Tag_Collection::addStoreFilter() expects array|int, array<array|bool|string|void|Zend_Db_Expr>|int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tag/Tag/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Tag_Model_Resource_Tag_Collection::addSummary() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tag/Tag/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, Mage_Core_Block_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Urlrewrite/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Adminhtml_Block_Widget_Container::_prepareButtonBlockId() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Container.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $className of method Varien_Data_Form_Abstract::addType() expects string, string|void given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, Mage_Core_Block_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Form/Container.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_walk_recursive expects array|object, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, array{$this(Mage_Adminhtml_Block_Widget_Grid), string} given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, Mage_Core_Block_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $column of method Mage_Adminhtml_Block_Widget_Grid::addColumn() expects array, array|Varien_Object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Mage_Catalog_Model_Resource_Product_Link_Product_Collection::addLinkModelFieldToFilter() expects array|null, array<array|bool|string|void|Zend_Db_Expr>|int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $offset of function array_splice expects int, int<0, max>|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func expects callable(): mixed, array<mixed, mixed>|Closure given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function addslashes expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Longtext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function implode expects array<string>, list<array<string|null>|string|null> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Options.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $currency of method Mage_Core_Model_Locale::currency() expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $replace of function str_replace expects array<string>|string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Text.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of method Mage_Core_Helper_String::strlen() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Wrapline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of method Mage_Core_Helper_String::substr() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Wrapline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $configuration of method Mage_Adminhtml_Block_Widget_Grid_Massaction_Item_Additional_Default::createFromConfiguration() expects array, array<string>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Massaction/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, Mage_Core_Block_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Massaction/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, Mage_Core_Block_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/View/Container.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function base64_decode expects string, array<mixed>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Helper/Js.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Helper/Sales.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $format of function vsprintf expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Helper/Sales.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strtolower expects string, string|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Helper/Sales.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_match expects string, array<string>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Helper/Sales.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cache of method Varien_Simplexml_Config::setCache() expects Varien_Simplexml_Config_Cache_Abstract, Zend_Cache_Core given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sectionNode of method Mage_Adminhtml_Model_Config::getAttributeModule() expects Varien_Simplexml_Element|false|null, array<Varien_Simplexml_Element>|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $groupNode of method Mage_Adminhtml_Model_Config::getAttributeModule() expects Varien_Simplexml_Element|false|null, array<Varien_Simplexml_Element>|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $fieldNode of method Mage_Adminhtml_Model_Config::getAttributeModule() expects Varien_Simplexml_Element|false|null, array<Varien_Simplexml_Element>|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $field of method Mage_Adminhtml_Model_Config_Data::_isValidField() expects Mage_Core_Model_Config_Element, Varien_Simplexml_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Config/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function array_key_exists expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Config/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customer of method Mage_Sales_Model_Quote::loadByCustomer() expects int|Mage_Customer_Model_Customer, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item of method Mage_Adminhtml_Model_Sales_Order_Create::_getQuoteItem() expects int|Mage_Sales_Model_Quote_Item, int|Mage_Sales_Model_Quote_Item_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $option of method Mage_Catalog_Model_Product_Option_Type_Default::setOption() expects Mage_Catalog_Model_Product_Option, Mage_Catalog_Model_Product_Option|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $qty of method Mage_Adminhtml_Model_Sales_Order_Create::moveQuoteItem() expects int, float|int<1, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $source of method Mage_Core_Helper_Data::copyFieldset() expects array|Varien_Object, Mage_Sales_Model_Order_Address|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $timestamp of static method Carbon\\Carbon::createFromTimestamp() expects float|int|string, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Random.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function rtrim expects string, array<string>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Baseurl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, Mage_Core_Model_Config_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Currency/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $typeId of method Mage_Catalog_Model_Resource_Product_Attribute_Collection::setEntityTypeFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Clone/Media/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, Mage_Core_Model_Config_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Source/Catalog/GridPerPage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, Mage_Core_Model_Config_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Source/Catalog/ListPerPage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Model_Url::setRouteParams() expects non-empty-array<mixed>, array<mixed> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Model_App::saveUseCache() expects array, array|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/CacheController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productIds of method Mage_Catalog_Model_Product_Action::updateAttributes() expects array, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/Product/Action/AttributeController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productIds of method Mage_Catalog_Model_Product_Action::updateWebsites() expects array, array|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/Product/Action/AttributeController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/Product/Action/AttributeController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Zend_Filter_NormalizedToLocalized::filter() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/Product/Action/AttributeController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function base64_decode expects string, array<mixed>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/ProductController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Cms/Wysiwyg/ImagesController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of method Mage_Cms_Helper_Wysiwyg_Images::getImageHtmlDeclaration() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Cms/Wysiwyg/ImagesController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of method Mage_Cms_Model_Wysiwyg_Images_Storage::resizeOnTheFly() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Cms/Wysiwyg/ImagesController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function str_starts_with expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Cms/Wysiwyg/ImagesController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $needle of function str_starts_with expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Cms/Wysiwyg/ImagesController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Cms/WysiwygController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Sales_Model_Quote::updateItem() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Customer/Cart/Product/Composite/CartController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $block of method Mage_Adminhtml_Controller_Action::_addContent() expects Mage_Core_Block_Abstract, Mage_Adminhtml_Block_Customer_Config given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Customer/ConfigController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Customer/System/Config/ValidatevatController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Wishlist_Model_Wishlist::updateItem() expects int|Mage_Wishlist_Model_Item, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Customer/Wishlist/Product/Composite/WishlistController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $addressId of method Mage_Customer_Model_Customer::getAddressItemById() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/CustomerController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Zend_Controller_Response_Abstract::setHeader() expects string, array|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/CustomerController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $userId of method Mage_Adminhtml_Permissions_RoleController::_addUserToRole() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Permissions/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $userId of method Mage_Adminhtml_Permissions_RoleController::_deleteUserFromRole() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Permissions/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $roleId of method Mage_Adminhtml_Permissions_RoleController::_addUserToRole() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Permissions/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $roleId of method Mage_Adminhtml_Permissions_RoleController::_deleteUserFromRole() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Permissions/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, bool|float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/Order/CreateController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shipment of method Mage_Adminhtml_Sales_Order_ShipmentController::_createShippingLabel() expects Mage_Sales_Model_Order_Shipment, Mage_Sales_Model_Order_Shipment|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shipment of method Mage_Adminhtml_Sales_Order_ShipmentController::_createShippingLabel() expects Mage_Sales_Model_Order_Shipment, bool|Mage_Sales_Model_Order_Shipment given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shipment of method Mage_Adminhtml_Sales_Order_ShipmentController::_saveShipment() expects Mage_Sales_Model_Order_Shipment, Mage_Sales_Model_Order_Shipment|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shipment of method Mage_Sales_Model_Order_Pdf_Shipment_Packaging::getPdf() expects Mage_Sales_Model_Order_Shipment|null, Mage_Sales_Model_Order_Shipment|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/OrderController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $order of method Mage_Sales_Model_Order_Payment::setOrder() expects Mage_Sales_Model_Order, Mage_Sales_Model_Order|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/TransactionsController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $transactionTo of method Mage_Sales_Model_Order_Payment::importTransactionInfo() expects Mage_Sales_Model_Order_Payment_Transaction, Mage_Sales_Model_Order_Payment_Transaction|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/TransactionsController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $websiteId of method Mage_Adminhtml_Block_Shipping_Carrier_Tablerate_Grid::setWebsiteId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/ConfigController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $modelClass of static method Mage::getModel() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/CurrencyController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $num of function abs expects float|int, float|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/CurrencyController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleModel of method Mage_Adminhtml_Tax_RuleController::_isValidRuleRequest() expects Mage_Tax_Model_Calculation_Rule, Mage_Core_Model_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Tax/RuleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $host of method Zend_Uri_Http::setHost() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of function basename expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $node of method Varien_Simplexml_Config::setXml() expects Varien_Simplexml_Element, Varien_Simplexml_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $resource of method Mage_Api_Model_Config::loadAclResources() expects Mage_Core_Model_Config_Element|null, Varien_Simplexml_Element given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $parent of method Mage_Api_Model_Acl::addRoleParent() expects array|string|Zend_Acl_Role_Interface, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $rolesArr of method Mage_Api_Model_Resource_Acl::loadRoles() expects array<array>, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $rulesArr of method Mage_Api_Model_Resource_Acl::loadRules() expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $assert of method Zend_Acl::allow() expects Zend_Acl_Assert_Interface|null, object|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $assert of method Zend_Acl::deny() expects Zend_Acl_Assert_Interface|null, object|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Mage_Api_Model_Resource_Acl_Role::setCreated() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Acl/Role.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, object|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $code of class SoapFault constructor expects array|string|null, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Adapter/Soap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Mage_Core_Model_Email_Template_Filter::filter() expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Adapter/Soap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function preg_replace expects array<float|int|string>|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Adapter/Soap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, array{object|false, string} given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Handler/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, object|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Handler/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sessId of method Mage_Api_Model_Session::isLoggedIn() expects string|false, stdClass|string|null given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Handler/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sessionId of method Mage_Api_Model_Server_Handler_Abstract::_startSession() expects string|null, stdClass|string|null given.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Handler/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $xml of method Mage_Api_Model_Resource_Abstract::setResourceConfig() expects Varien_Simplexml_Element, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Handler/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function preg_replace expects array<float|int|string>|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/V2/Adapter/Soap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/V2/Adapter/Soap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/V2/Handler.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $needle of function str_contains expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/V2/Handler.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function preg_replace expects array<float|int|string>|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Wsi/Adapter/Soap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Wsi/Adapter/Soap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Wsi/Handler.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $needle of function str_contains expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Wsi/Handler.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $user of method Mage_Api_Model_Resource_User::hasAssigned2Role() expects int|Mage_Api_Model_User, int|Mage_Core_Model_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $source of static method Mage_Api_Model_Wsdl_Config_Element::_getChildren() expects Varien_Simplexml_Element, SimpleXMLElement given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $source of static method Mage_Api_Model_Wsdl_Config_Element::_getChildren() expects Varien_Simplexml_Element, Varien_Simplexml_Element|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $elmNamespace of method Mage_Api_Model_Wsdl_Config_Element::getElementByName() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $namespace of method SimpleXMLElement::addAttribute() expects string|null, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function uasort expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $roleId of method Mage_Api2_Model_Acl::addRole() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $operationType of method Mage_Api2_Model_Config::getResourceEntityOnlyAttributes() expects \'read\'|\'write\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Filter/Attribute/ResourcePermission.php',
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
    'rawMessage' => 'Parameter #1 $roleId of method Mage_Api2_Model_Resource_Acl_Global_Rule_Collection::addFilterByRoleId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global/Rule/ResourcePermission.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $node of method Mage_Api2_Model_Acl_Global_Rule_Tree::_addOperations() expects Varien_Simplexml_Element, array|Varien_Simplexml_Element given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global/Rule/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $node of method Mage_Api2_Model_Acl_Global_Rule_Tree::_addPrivileges() expects Varien_Simplexml_Element, array|Varien_Simplexml_Element given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global/Rule/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $name of method Mage_Api2_Model_Acl_Global_Rule_Tree::_addOperations() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global/Rule/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $name of method Mage_Api2_Model_Acl_Global_Rule_Tree::_addPrivileges() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global/Rule/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $role of method Mage_Api2_Model_Auth_User_Admin::setRole() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Auth/User/Admin.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cache of method Varien_Simplexml_Config::setCache() expects Varien_Simplexml_Config_Cache_Abstract, Zend_Cache_Core given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $node of method Varien_Simplexml_Config::setXml() expects Varien_Simplexml_Element, Varien_Simplexml_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function htmlspecialchars expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Dispatcher.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $lowerOrEqualsTo of method Mage_Api2_Model_Config::getResourceLastVersion() expects int|null, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Dispatcher.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_match expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Dispatcher.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Api2_Model_Renderer_Xml::_prepareData() expects array|Varien_Object, array<mixed, mixed>|object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Renderer/Xml.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $qualifiedName of method SimpleXMLElement::addChild() expects string, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Renderer/Xml/Writer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_split expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $xmlObject of method Mage_Api2_Model_Request_Interpreter_Xml::_toArray() expects SimpleXMLElement, SimpleXMLElement|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Request/Interpreter/Xml.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $actionType of method Mage_Api2_Model_Resource::setActionType() expects \'collection\'|\'entity\', string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $keys of function array_combine expects an array of values castable to string, array<string|void> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $keys of function array_combine expects array<int|string>, array<string|void> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $operation of method Mage_Api2_Model_Resource::setOperation() expects \'create\'|\'delete\'|\'read\'|\'retrieve\'|\'update\'|\'write\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource.php',
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
    'rawMessage' => 'Parameter #2 $operationType of method Mage_Api2_Model_Resource::getEntityOnlyAttributes() expects \'read\'|\'write\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $operationType of method Mage_Api2_Model_Resource::getIncludedAttributes() expects \'read\'|\'write\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource/Validator/Eav.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of function basename expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Route/ApiType.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $adminId of method Mage_Api2_Adminhtml_Api2_RoleController::_addUserToRole() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/controllers/Adminhtml/Api2/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $adminId of method Mage_Api2_Adminhtml_Api2_RoleController::_deleteUserFromRole() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/controllers/Adminhtml/Api2/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $roleId of method Mage_Api2_Model_Resource_Acl_Global_Rule_Collection::addFilterByRoleId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/controllers/Adminhtml/Api2/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $roleId of method Mage_Api2_Adminhtml_Api2_RoleController::_addUserToRole() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/controllers/Adminhtml/Api2/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $roleId of method Mage_Api2_Adminhtml_Api2_RoleController::_deleteUserFromRole() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/controllers/Adminhtml/Api2/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of static method Mage_Core_Helper_Data::currencyByStore() expects float, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Catalog/Product/View/Type/Bundle/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $haystack of function in_array expects array, array|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Catalog/Product/View/Type/Bundle/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $childId of method Mage_Bundle_Model_Resource_Selection::getParentIdsByChild() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productIds of method Mage_Bundle_Model_Resource_Price_Index::loadPriceIndex() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $price of method Mage_Catalog_Model_Product::setFinalPrice() expects float|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 ...$arg1 of function min expects non-empty-array, array<int, float|int> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $qty of method Mage_Catalog_Model_Product_Type_Price::_applyOptionsPrice() expects float, float|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $selectionProduct of method Mage_Bundle_Model_Product_Price::getSelectionFinalTotalPrice() expects Mage_Catalog_Model_Product, Mage_Bundle_Model_Selection given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $bundleQty of method Mage_Bundle_Model_Product_Price::getLowestPrice() expects int, float|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $bundleQty of method Mage_Bundle_Model_Product_Price::getSelectionFinalTotalPrice() expects float|int, float|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $pId of method Mage_CatalogRule_Model_Resource_Rule::getRulePrice() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #5 $multiplyQty of method Mage_Bundle_Model_Product_Price::getSelectionFinalTotalPrice() expects bool, bool|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $parentId of method Mage_Bundle_Model_Resource_Bundle::saveProductRelations() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Bundle_Model_Option::getSearchableData() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Bundle_Model_Resource_Bundle::dropAllQuoteChildItems() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Bundle_Model_Resource_Bundle::dropAllUnneededSelections() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Bundle_Model_Resource_Option_Collection::setProductIdFilter() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Bundle_Model_Resource_Option_Collection::joinValues() expects int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $selection of method Mage_Bundle_Model_Option::addSelection() expects Mage_Bundle_Model_Selection, Mage_Catalog_Model_Product given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Option/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 ...$arg1 of function min expects non-empty-array, list<float> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $wId of method Mage_CatalogRule_Model_Resource_Rule::getRulePrice() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $websiteId of method Mage_Bundle_Model_Resource_Price_Index::_savePriceIndex() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $gId of method Mage_CatalogRule_Model_Resource_Rule::getRulePrice() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $groupId of method Mage_Bundle_Model_Resource_Price_Index::_savePriceIndex() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $input of function array_rand expects non-empty-array, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/Model/Zend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $login of method Mage_Captcha_Model_Zend::_isOverLimitAttempts() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/Model/Zend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/controllers/Adminhtml/RefreshController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/controllers/RefreshController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Category/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productType of method Mage_Catalog_Block_Product_Abstract::_preparePriceRenderer() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productTypeId of method Mage_Catalog_Block_Product_Abstract::_getPriceBlock() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Catalog_Model_Resource_Product_Compare_Item_Collection::setCustomerId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Compare/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Collection_Abstract<Mage_Catalog_Model_Product>::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Compare/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $visitorId of method Mage_Catalog_Model_Resource_Product_Compare_Item_Collection::setVisitorId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Compare/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $bundleProduct of method Mage_Bundle_Model_Product_Price::getLowestPrice() expects Mage_Catalog_Model_Product, Mage_Catalog_Model_Product|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $width of method Mage_Catalog_Helper_Image::resize() expects int, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $page of method Mage_Page_Block_Html_Pager::getPageUrl() expects int, float|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Widget/Html/Pager.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Category::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Seo/Sitemap/Tree/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $store of method Mage_Catalog_Model_Resource_Abstract::getAttributeRawValue() expects int|Mage_Core_Model_Store, Mage_Core_Model_Store|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Widget/Link.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $sorted of method Mage_Catalog_Model_Category::getCategories() expects bool, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Category.php',
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
    'rawMessage' => 'Parameter #1 $imageOpacity of method Mage_Catalog_Helper_Image::setWatermarkImageOpacity() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $size of method Mage_Catalog_Helper_Image::setWatermarkSize() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $size of method Mage_Catalog_Model_Product_Image::setWatermarkSize() expects array, array|bool given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Output.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Varien_Filter_Template::filter() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Output.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $categoryId of method Mage_Catalog_Model_Product::canBeShowInCategory() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Model_Product::getIdBySku() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Catalog_Model_Resource_Product_Compare_Item_Collection::setCustomerId() expects int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product/Compare.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Collection_Abstract<Mage_Catalog_Model_Product>::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product/Compare.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $visitorId of method Mage_Catalog_Model_Resource_Product_Compare_Item_Collection::setVisitorId() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product/Compare.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Product_Flat_Flag::isStoreBuilt() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $operationType of method Mage_Api2_Model_Resource::getEntityOnlyAttributes() expects \'read\'|\'write\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $operationType of method Mage_Api2_Model_Resource::getIncludedAttributes() expects \'read\'|\'write\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%d"), int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Category/Rest/Admin/V1.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%d"), int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Category/Rest/Admin/V1.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $src of method Varien_Io_File::write() expects resource|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Image/Rest/Admin/V1.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityPkValue of method Mage_Review_Model_Review::getTotalReviews() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $storeId of method Mage_Review_Model_Review::getTotalReviews() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $resourceInstanceId of method Mage_Api2_Model_Resource::_multicall() expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Rest/Admin/V1.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Collection_Abstract<Mage_Catalog_Model_Product>::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Rest/Admin/V1.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Customer_Model_Session::setCustomerId() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Rest/Customer/V1.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Abstract::load() expects int|string|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Store::load() expects int|string|null, float|int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Website/Validator/Admin/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Website::load() expects int|string|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Website/Validator/Admin/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%d"), float|int|string given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Website/Validator/Admin/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%d"), int|string|null given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Website/Validator/Admin/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%d"), int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Website/Validator/Admin/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%d"), int|string|null given.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Website/Validator/Admin/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Category::setStoreId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Catalog_Model_Category_Api::_getProductId() expects int|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Collection_Abstract<Mage_Catalog_Model_Category>::setStoreId() expects int|Mage_Core_Model_Store|string, int|Mage_Core_Model_Store|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $afterCategoryId of method Mage_Catalog_Model_Category::move() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ids of method Mage_Catalog_Model_Convert_Adapter_Product::_addAffectedEntityIds() expects array|int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $idx of method Varien_Object_Cache::load() expects object|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityTypeId of method Mage_Eav_Model_Convert_Parser_Abstract::getAttributeSetId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Parser/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $idx of method Varien_Object_Cache::load() expects object|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Parser/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Catalog_Model_Design::_extractSettings() expects Mage_Catalog_Model_Category|Mage_Catalog_Model_Product, Mage_Catalog_Model_Category|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Design.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Catalog_Model_Design::_inheritDesign() expects Varien_Object, array|Mage_Catalog_Model_Category|Mage_Catalog_Model_Product given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Design.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attribute of method Mage_Catalog_Model_Layer::_filterFilterableAttributes() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $categoryId of method Mage_Catalog_Model_Resource_Product_Collection::addUrlRewrite() expects int|string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Abstract::load() expects int|string|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Category::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $index of method Mage_Catalog_Model_Resource_Layer_Filter_Decimal::applyFilterToCollection() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Decimal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $num of function ceil expects float|int, float|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $num of function floor expects float|int, float|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $oldRoundPrices of method Mage_Catalog_Model_Layer_Filter_Price_Algorithm::_mergeRoundPrices() expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $newRoundPrices of method Mage_Catalog_Model_Layer_Filter_Price_Algorithm::_mergeRoundPrices() expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function array_slice expects int|null, float|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $categories of method Mage_Catalog_Model_Observer::_addCategoriesToMenu() expects array|Varien_Data_Tree_Node_Collection, array|Varien_Data_Collection|Varien_Data_Tree_Node_Collection given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $oldId of method Mage_Catalog_Model_Resource_Product::duplicate() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $oldProductId of method Mage_Catalog_Model_Product_Option::duplicate() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productType of static method Mage_Catalog_Model_Product_Type::priceFactory() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strtolower expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $newId of method Mage_Catalog_Model_Resource_Product::duplicate() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $newProductId of method Mage_Catalog_Model_Product_Option::duplicate() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributes of method Mage_Catalog_Model_Api_Resource::_isAllowedAttribute() expects array|null, stdClass|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attributeId of method Mage_Catalog_Model_Product_Attribute_Api::options() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $priceObject of method Mage_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract::_isPriceFixed() expects Mage_Catalog_Model_Product_Type_Price, Mage_Catalog_Model_Product_Type_Price|Mage_Core_Model_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice_Abstract::deletePriceData() expects int, int|string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice_Abstract::loadPriceData() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $productTypeId of method Mage_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract::preparePriceData() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $input of method Mage_Core_Model_Date::timestamp() expects int|string|null, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Startdate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $groupId of method Mage_Eav_Model_Resource_Entity_Attribute_Collection::setAttributeGroupFilter() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $skeletonId of method Mage_Eav_Model_Entity_Attribute_Set::initFromSkeleton() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Set/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $typeId of method Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection::setEntityTypeFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Set/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Catalog_Model_Product_Attribute_Tierprice_Api::_initProduct() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Tierprice/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $visitorId of method Mage_Catalog_Model_Product_Compare_Item::addVisitorId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Compare/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_Catalog_Model_Product_Flat_Indexer::deleteStore() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Flat/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $heigth of method Varien_Image::setWatermarkHeigth() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $watermarkImage of method Varien_Image::watermark() expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $childIds of method Mage_Catalog_Model_Resource_Product_Indexer_Abstract::getRelationsByChild() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Indexer/Eav.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $childId of method Mage_Catalog_Model_Resource_Product_Indexer_Price::getProductParentsByChild() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Indexer/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ids of method Mage_Catalog_Model_Resource_Product_Indexer_Price::reindexProductIds() expects array|int, int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Link/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_CatalogInventory_Model_Stock_Status::updateStatus() expects int, int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Link/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Catalog_Model_Product_Link_Api::_initProduct() expects int, int|string given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Link/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Model_Product::getIdBySku() expects string, int|string given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Link/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ids of method Mage_Catalog_Model_Resource_Product_Indexer_Price::reindexProductIds() expects array|int, int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Link/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_CatalogInventory_Model_Stock_Status::updateStatus() expects int, int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Link/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Catalog_Model_Product_Link_Api::_initProduct() expects int, int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Link/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Model_Product::getIdBySku() expects string, int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Link/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $optionId of method Mage_Catalog_Model_Product_Option::deletePrices() expects int|string, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $optionId of method Mage_Catalog_Model_Product_Option::deleteTitles() expects int|string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $optionId of method Mage_Catalog_Model_Product_Option_Value::deleteValue() expects int|string, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $optionId of method Mage_Catalog_Model_Product_Option_Value::getValuesByOption() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $optionId of method Mage_Catalog_Model_Product::getOptionById() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of method Mage_Core_Helper_File_Storage_Database::saveFileToFilesystem() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shorthand of function ini_parse_quantity expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function md5 expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $optionTypeId of method Mage_Catalog_Model_Product_Option_Value::deleteValues() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Value.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function reset expects array|object, int|list<array> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Value/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Catalog_Model_Product_Option::getSearchableData() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $product of method Mage_Catalog_Model_Product_Type_Abstract::_prepareOptions() expects Mage_Catalog_Model_Product, Mage_Catalog_Model_Product|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $product of method Mage_Catalog_Model_Product_Type_Abstract::_prepareProduct() expects Mage_Catalog_Model_Product, Mage_Catalog_Model_Product|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $sku of method Mage_Catalog_Model_Product_Type_Abstract::getOptionSku() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $qty of method Mage_Catalog_Model_Product_Type_Price::_applyOptionsPrice() expects float, float|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, array|float given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $qty of method Mage_Catalog_Model_Product_Type_Price::_applyOptionsPrice() expects float, float|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $pId of method Mage_CatalogRule_Model_Resource_Rule::getRulePrice() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $categoryId of method Mage_Catalog_Model_Product_Url::_getRequestPath() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productIds of method Mage_Catalog_Model_Resource_Product_Website::getWebsites() expects array, array|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Catalog_Model_Resource_Attribute::_clearUselessAttributeValues() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Catalog_Model_Resource_Attribute::isUsedBySuperProducts() expects Mage_Core_Model_Abstract, Mage_Eav_Model_Entity_Attribute_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function implode expects array<string>, array<string|void> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $categoryId of method Mage_Catalog_Model_Resource_Category::getChildrenCount() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_Catalog_Model_Resource_Collection_Abstract<Mage_Catalog_Model_Category>::setStore() expects int|Mage_Core_Model_Store|string, Mage_Core_Model_Store|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $sorted of method Mage_Catalog_Model_Resource_Category_Tree::addCollectionData() expects bool, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_unshift expects array, list<array<string, string>>|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Attribute/Source/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityIds of method Mage_Catalog_Model_Resource_Category_Flat::_getAttributeValues() expects array|int|string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $parent of method Mage_Catalog_Model_Resource_Category_Collection::addParentPathFilter() expects string, string|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_Catalog_Model_Resource_Category_Flat::_createTable() expects array|int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Category_Flat::getMainStoreTable() expects int, array|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Category_Flat::getMainStoreTable() expects int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_Catalog_Model_Resource_Category_Flat::_getAttributeValues() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, array|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $storeId of method Mage_Catalog_Model_Resource_Category_Flat::getNodes() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Varien_Data_Collection_Db<Mage_Core_Model_Abstract>::addFieldToFilter() expects array|int|string|null, array<string, array<mixed>>|float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $connection of method Varien_Data_Tree_Dbp::__construct() expects Varien_Db_Adapter_Interface, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $nodeId of method Varien_Data_Tree::getNodeById() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_Catalog_Model_Resource_Category_Tree::_getInactiveItemIds() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $prevNode of method Mage_Catalog_Model_Resource_Category_Tree::_afterMove() expects Varien_Data_Tree_Node, Varien_Data_Tree_Node|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $prevNode of method Mage_Catalog_Model_Resource_Category_Tree::_beforeMove() expects Varien_Data_Tree_Node, Varien_Data_Tree_Node|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #3 ("%%2$d"), int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Collection_Abstract<T of Mage_Catalog_Model_Abstract>::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Varien_Db_Select::where() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $expression of class Zend_Db_Expr constructor expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute of method Mage_Catalog_Model_Resource_Abstract::_saveAttributeValue() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Catalog_Model_Resource_Eav_Attribute|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $result of method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media::_removeDuplicates() expects array, array|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $attributeId of method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media::_getLoadGallerySelect() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice_Abstract::deletePriceData() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Tierprice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice_Abstract::loadPriceData() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Tierprice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attribute of method Mage_Catalog_Model_Resource_Product_Collection::addAttributeToSelect() expects array|int|Mage_Core_Model_Config_Element|string, int|Mage_Eav_Model_Entity_Attribute_Interface|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Varien_Data_Collection_Db<Mage_Catalog_Model_Product>::_getConditionSql() expects array|int|string, array|int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Varien_Data_Collection_Db<Mage_Catalog_Model_Product>::_getConditionSql() expects array|int|string, int|string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $productTypeId of method Mage_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract::preparePriceData() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $storeId of method Mage_Catalog_Helper_Product_Url_Rewrite_Interface::getTableSelect() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Flat/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Product_Flat_Indexer::getFlatTableName() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Flat/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $data of method Varien_Db_Adapter_Pdo_Mysql::insertArray() expects array<int, list>, non-empty-array<int, array<mixed>> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Eav/Source.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Varien_Data_Collection_Db<Mage_Catalog_Model_Product>::_getConditionSql() expects array|int|string, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Link/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $newOptionId of method Mage_Catalog_Model_Product_Option_Value::duplicate() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Product_Option_Value_Collection::addPriceToResult() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Option/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Product_Option_Value_Collection::addTitleToResult() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Option/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_Catalog_Model_Resource_Product_Status::refreshEnabledIndex() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_Catalog_Model_Resource_Url::getCategory() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key of function array_key_exists expects int|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $locale of method Symfony\\Component\\String\\Slugger\\AsciiSlugger::setLocale() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Url::getProductsByStore() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Url::prepareRewrites() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $category of method Mage_Catalog_Model_Url::_refreshProductRewrite() expects Varien_Object, Varien_Object|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $parentPath of method Mage_Catalog_Model_Url::getCategoryRequestPath() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $rewrite of method Mage_Catalog_Model_Resource_Url::saveRewrite() expects Varien_Object, Varien_Object|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $rewrite of method Mage_Catalog_Model_Url::_saveRewriteHistory() expects Varien_Object, Varien_Object|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_Catalog_Model_Resource_Url::getCategories() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_Catalog_Model_Resource_Url::getCategory() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_match expects string, array<string>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Category::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/controllers/CategoryController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Catalog_Model_Resource_Product_Compare_Item_Collection::setCustomerId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/controllers/Product/CompareController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $visitorId of method Mage_Catalog_Model_Product_Compare_Item::addVisitorId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/controllers/Product/CompareController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $visitorId of method Mage_Catalog_Model_Resource_Product_Compare_Item_Collection::setVisitorId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/controllers/Product/CompareController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Mage_Core_Model_Resource_Setup::setConfigData() expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-install-1.6.0.0.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $groupId of method Mage_Eav_Model_Entity_Setup::addAttributeToGroup() expects int|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-install-1.6.0.0.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Adapter_Pdo_Mysql::disableTableKeys() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.13-1.6.0.0.14.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Adapter_Pdo_Mysql::enableTableKeys() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.13-1.6.0.0.14.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Select::insertFromSelect() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.13-1.6.0.0.14.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $id of method Mage_Eav_Model_Entity_Setup::getAttributeTable() expects int|string, int|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.13-1.6.0.0.14.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Varien_Db_Adapter_Pdo_Mysql::quoteInto() expects array|float|int|string|Zend_Db_Expr|Zend_Db_Select|null, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.13-1.6.0.0.14.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Varien_Db_Select::where() expects array|float|int|string|Zend_Db_Expr|Zend_Db_Select|null, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.13-1.6.0.0.14.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Adapter_Pdo_Mysql::disableTableKeys() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.19.1.3-1.6.0.0.19.1.4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Adapter_Pdo_Mysql::enableTableKeys() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.19.1.3-1.6.0.0.19.1.4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Select::insertFromSelect() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.19.1.3-1.6.0.0.19.1.4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $id of method Mage_Eav_Model_Entity_Setup::getAttributeTable() expects int|string, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.19.1.3-1.6.0.0.19.1.4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_CatalogIndex_Model_Resource_Aggregation::getCacheData() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Aggregation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $storeId of method Mage_CatalogIndex_Model_Resource_Aggregation::saveCacheData() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Aggregation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_CatalogIndex_Model_Resource_Abstract::setStoreId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_CatalogIndex_Model_Resource_Data_Abstract::fetchLinkInformation() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $store of method Mage_CatalogIndex_Model_Resource_Data_Abstract::getAttributeData() expects int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $store of method Mage_CatalogIndex_Model_Resource_Data_Abstract::getMinimalPrice() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_CatalogIndex_Model_Indexer::_afterPlainReindex() expects array|int|Mage_Core_Model_Store|Mage_Core_Model_Website, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_CatalogIndex_Model_Indexer::updateCatalogProductFlat() expects int|Mage_Core_Model_Store, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_CatalogIndex_Model_Resource_Indexer::updateCatalogProductFlat() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $productIds of method Mage_CatalogIndex_Model_Resource_Indexer::updateCatalogProductFlat() expects array|Mage_Catalog_Model_Product_Condition_Interface|null, array|int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $products of method Mage_CatalogIndex_Model_Indexer::updateCatalogProductFlat() expects array|int|Mage_Catalog_Model_Product|null, array|int|Mage_Catalog_Model_Product|Mage_Catalog_Model_Product_Condition_Interface|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $productId of method Mage_CatalogIndex_Model_Indexer_Abstract::saveIndex() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $productId of method Mage_CatalogIndex_Model_Indexer_Abstract::saveIndices() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, int|list|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer/Minimalprice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_CatalogIndex_Model_Resource_Abstract::setStoreId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $aggregationId of method Mage_CatalogIndex_Model_Resource_Aggregation::_saveTagRelations() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Aggregation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tags of method Mage_CatalogIndex_Model_Resource_Aggregation::_getTagIds() expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Aggregation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #5 $id of method Mage_CatalogIndex_Model_Resource_Data_Abstract::_prepareLinkFetchSelect() expects int, array|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $products of method Mage_CatalogIndex_Model_Resource_Indexer::reindexAttributes() expects array, array|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_App::getStore() expects bool|int|Mage_Core_Model_Store|string|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Varien_Db_Adapter_Pdo_Mysql::quoteInto() expects array|float|int|string|Zend_Db_Expr|Zend_Db_Select|null, array|int|Mage_Catalog_Model_Product_Condition_Interface|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $storeIds of method Mage_CatalogIndex_Model_Resource_Setup::_setWebsiteInfo() expects array, Mage_Core_Model_Website given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Block/Qtyincrements.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Block/Stockqty/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Abstract::load() expects int|string|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Api2/Stock/Item/Validator/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%d"), float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Api2/Stock/Item/Validator/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $childId of method Mage_CatalogInventory_Model_Resource_Indexer_Stock::getProductParentsByChild() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Indexer/Stock.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_CatalogInventory_Model_Observer::_getQuoteItemQtyForCheck() expects int, int|string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_CatalogInventory_Model_Stock_Status::updateStatus() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $quoteItemId of method Mage_CatalogInventory_Model_Observer::_getQuoteItemQtyForCheck() expects int, int|string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Observer.php',
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
    'rawMessage' => 'Parameter #1 $qty of method Mage_CatalogInventory_Model_Stock_Item::checkQty() expects float, float|int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $qty of method Mage_CatalogInventory_Model_Stock_Item::checkQtyIncrements() expects float|int, float|int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $productId of method Mage_CatalogInventory_Model_Resource_Stock_Item::loadByProductId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array<int>|void given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_CatalogInventory_Model_Stock_Status::_processChildren() expects int, int|void given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productIds of method Mage_CatalogInventory_Model_Stock_Status::getProductStatus() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $productType of method Mage_CatalogInventory_Model_Stock_Status::_processChildren() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array<int>|void given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $status of method Mage_CatalogInventory_Model_Stock_Status::_processChildren() expects int, bool|int given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $expression of class Zend_Db_Expr constructor expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Action/Index/Refresh.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $timestamp of method Mage_CatalogRule_Model_Action_Index_Refresh::_prepareGroupWebsite() expects string, int|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Action/Index/Refresh.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $timestamp of method Mage_CatalogRule_Model_Action_Index_Refresh::_reindex() expects int, int|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Action/Index/Refresh.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $combine of method Mage_CatalogRule_Model_Observer::_removeAttributeFromConditions() expects Mage_CatalogRule_Model_Rule_Condition_Combine, Mage_Rule_Model_Condition_Combine given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $pId of method Mage_CatalogRule_Model_Resource_Rule::getRulePrice() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $pId of method Mage_CatalogRule_Model_Resource_Rule::getRulePrice() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $expression of class Zend_Db_Expr constructor expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_CatalogRule_Model_Resource_Rule::_isProductMatchedRule() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_CatalogRule_Model_Resource_Rule::cleanProductData() expects int, int|string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_Rule_Model_Resource_Abstract::getCustomerGroupIds() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_Rule_Model_Resource_Abstract::getWebsiteIds() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleIds of method Mage_Rule_Model_Resource_Abstract::bindRuleToEntity() expects array|int|string, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Rule_Model_Abstract::getProductFlatSelect() expects int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $productIds of method Mage_CatalogRule_Model_Resource_Rule::cleanProductData() expects array<int|string|void|null>, array|int<min, -1>|int<1, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Rule_Model_Condition_Combine::validate() expects Varien_Object, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product of method Mage_CatalogRule_Model_Resource_Rule::applyAllRules() expects int|Mage_Catalog_Model_Product|null, Mage_Core_Model_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_CatalogRule_Model_Resource_Rule::getProductRuleIds() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_Rule_Model_Resource_Abstract::getCustomerGroupIds() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $product of method Mage_CatalogRule_Model_Resource_Rule::applyToProduct() expects Mage_Catalog_Model_Product, Mage_Core_Model_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $productId of method Mage_CatalogRule_Model_Resource_Rule::getRulesFromProduct() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function implode expects array<string>, array<array<string|null>|string|null> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of method Mage_Core_Helper_String::substr() expects int|null, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Eav_Model_Resource_Entity_Attribute_Collection::addStoreLabel() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Advanced.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $productId of method Mage_CatalogSearch_Model_Fulltext::cleanIndex() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Fulltext/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $productIds of method Mage_CatalogSearch_Model_Fulltext::rebuildIndex() expects array|int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Fulltext/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $date of function is_empty_date expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $typeId of method Mage_CatalogSearch_Model_Resource_Fulltext::_getProductTypeInstance() expects string, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $productId of method Mage_CatalogSearch_Model_Resource_Fulltext::cleanIndex() expects int|null, array|int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $typeId of method Mage_CatalogSearch_Model_Resource_Fulltext::_getProductChildrenIds() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $websiteId of method Mage_CatalogSearch_Model_Resource_Fulltext::_getProductChildrenIds() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeIds of method Mage_CatalogSearch_Model_Resource_Query_Collection::addStoreFilter() expects array|int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Query/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entity of method Mage_Eav_Model_Entity_Attribute_Abstract::setEntity() expects Mage_Eav_Model_Entity_Abstract, Mage_Catalog_Model_Resource_Product_Flat|Mage_Eav_Model_Entity_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Search/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_CatalogSearch_Model_Query::setStoreId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/controllers/ResultController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $modelClass of static method Mage::getModel() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Centinel/Model/Service.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_Checkout_Model_Resource_Agreement_Collection::addStoreFilter() expects int|Mage_Core_Model_Store, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Agreements.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Collection_Abstract<Mage_Catalog_Model_Product>::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Cart/Crosssell.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Checkout_Model_Session::getQuoteItemMessages() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Cart/Item/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Cart/Item/Renderer/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Cart/Item/Renderer/Grouped.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of method Mage_Core_Block_Abstract::getChild() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Multishipping/Payment/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of method Mage_Core_Block_Abstract::getChild() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Onepage/Payment/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_Checkout_Model_Resource_Agreement_Collection::addStoreFilter() expects int|Mage_Core_Model_Store, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Checkout_Model_Session::addQuoteItemMessage() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Sales_Model_Quote::getItemById() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Sales_Model_Quote::hasProductId() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $quoteId of method Mage_Checkout_Model_Session::setQuoteId() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $message of method Mage_Checkout_Model_Session::addQuoteItemMessage() expects Mage_Core_Model_Message, Mage_Core_Model_Message_Error|Mage_Core_Model_Message_Notice|Mage_Core_Model_Message_Success|Mage_Core_Model_Message_Warning given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_diff expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function implode expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Customer/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Sales_Model_Quote::removeItem() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Product/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Sales_Model_Quote::removeItem() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Product/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $quoteId of method Mage_Checkout_Model_Session::setQuoteId() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_Sales_Model_Quote::setStore() expects Mage_Core_Model_Store, Mage_Core_Model_Store|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $addressId of method Mage_Sales_Model_Quote::getShippingAddressByCustomerAddressId() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Multishipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $addressId of method Mage_Sales_Model_Quote::removeAddress() expects int, int|string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Multishipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item of method Mage_Sales_Model_Order_Item::setParentItem() expects Mage_Sales_Model_Order_Item, Mage_Sales_Model_Order_Item|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Multishipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Sales_Model_Quote::removeItem() expects int, int|string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Multishipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Sales_Model_Quote_Address::removeItem() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Multishipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $quoteItemId of method Mage_Sales_Model_Order::getItemByQuoteItemId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Multishipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_values expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Onepage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Customer_Model_Session::loginById() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Onepage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function implode expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Onepage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $websiteId of method Mage_Checkout_Model_Type_Onepage::_customerEmailExists() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Onepage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $code of method Mage_Core_Model_Message_Abstract::setCode() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of method Mage_Core_Model_Session_Abstract::addError() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of method Mage_Core_Model_Session_Abstract::addNotice() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Catalog_Helper_Product_View::prepareAndRender() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Varien_Filter_Template::filter() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Block/Widget/Block.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_Cms_Model_Page::checkIdentifier() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Controller/Router.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of static method Mage_Cms_Helper_Page::getConfigLabelFromConfigPath() expects \'web/default/cms…\'|\'web/default/cms_no…\', (int|string) given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Helper/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function strstr expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Helper/Wysiwyg/Images.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $search of function str_replace expects array<string>|string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Helper/Wysiwyg/Images.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Helper/Wysiwyg/Images.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Cms_Model_Resource_Page::getCmsPageIdentifierById() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Cms_Model_Resource_Block::lookupStoreIds() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Block.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Cms_Model_Resource_Block::lookupStoreIds() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Block.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $identifier of method Mage_Cms_Model_Resource_Page::_getLoadByIdentifierSelect() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $page of method Mage_Cms_Model_Resource_Page::getUsedInStoreConfigCollection() expects Mage_Cms_Model_Page, Mage_Cms_Model_Page|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $pageId of method Mage_Cms_Model_Resource_Page::lookupStoreIds() expects string, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $pageId of method Mage_Cms_Model_Resource_Page::lookupStoreIds() expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function array_key_exists expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of function filemtime expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of function basename expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attr of method Mage_ConfigurableSwatches_Helper_Data::attrIsSwatchType() expects int|Mage_Eav_Model_Attribute, Mage_Catalog_Model_Resource_Eav_Attribute given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Block/Catalog/Layer/State/Swatch.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $imageFallback of method Mage_ConfigurableSwatches_Block_Catalog_Media_Js_Abstract::_getJsImageFallbackString() expects array<string, array<string>>, array<array<string>|void> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Block/Catalog/Media/Js/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $width of method Mage_Catalog_Helper_Image::resize() expects int, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Mediafallback.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $width of function imagecreatetruecolor expects int<1, max>, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productimg.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $height of function imagecreatetruecolor expects int<1, max>, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productimg.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $red of function imagecolorallocate expects int<0, 255>, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productimg.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $green of function imagecolorallocate expects int<0, 255>, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productimg.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $blue of function imagecolorallocate expects int<0, 255>, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productimg.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $color of function imagefill expects int, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productimg.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attr of method Mage_ConfigurableSwatches_Helper_Data::attrIsSwatchType() expects int|Mage_Eav_Model_Attribute, Mage_Catalog_Model_Resource_Eav_Attribute given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key of static method Mage_ConfigurableSwatches_Helper_Data::normalizeKey() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Model/String/Normalized.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $block of method Mage_Core_Helper_Security::validateAgainstBlockMethodBlacklist() expects Mage_Core_Block_Abstract, array<Mage_Core_Block_Abstract>|Mage_Core_Block_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, array{non-empty-array<Mage_Core_Block_Abstract>|Mage_Core_Block_Abstract, mixed} given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $child of method Mage_Core_Block_Abstract::_beforeChildToHtml() expects Mage_Core_Block_Abstract, array<Mage_Core_Block_Abstract>|Mage_Core_Block_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $offset of function array_splice expects int, int|string given.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Html/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #5 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Html/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function str_starts_with expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $needle of function str_starts_with expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $type of static method Mage::getBaseUrl() expects \'direct_link\'|\'js\'|\'link\'|\'media\'|\'skin\'|\'web\', non-falsy-string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Text/Tag/Css.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $type of static method Mage::getBaseUrl() expects \'direct_link\'|\'js\'|\'link\'|\'media\'|\'skin\'|\'web\', non-falsy-string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Text/Tag/Css/Admin.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $type of static method Mage::getBaseUrl() expects \'direct_link\'|\'js\'|\'link\'|\'media\'|\'skin\'|\'web\', non-falsy-string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Text/Tag/Js.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Front/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Front/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Zend_Controller_Response_Abstract::setHeader() expects string, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Front/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::appendBody() expects string, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $handle of method Mage_Core_Model_Layout_Update::addHandle() expects array|string, array|string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Zend_Controller_Response_Abstract::setHeader() expects string, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $pathInfo of method Mage_Core_Controller_Request_Http::rewritePathInfo() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Front.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strtolower expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Admin.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $useRouterName of method Mage_Core_Controller_Varien_Router_Standard::collectRoutes() expects string, bool given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Admin.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function preg_replace expects array<float|int|string>|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Admin.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $action of method Mage_Core_Controller_Varien_Action::dispatch() expects string, array|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $action of method Mage_Core_Controller_Varien_Action::hasAction() expects string, array|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $frontName of method Mage_Core_Controller_Varien_Router_Standard::getModuleByFrontName() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $frontName of method Mage_Core_Controller_Varien_Router_Standard::getRouteByFrontName() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $route of method Mage_Core_Controller_Request_Http::setRouteName() expects string, int|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Zend_Controller_Request_Abstract::setActionName() expects string, array|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Zend_Controller_Request_Abstract::setControllerName() expects string, array|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Zend_Controller_Request_Abstract::setModuleName() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $controller of method Mage_Core_Controller_Varien_Router_Standard::_validateControllerClassName() expects string, array|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function htmlspecialchars expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strip_tags expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $allowedTags of method Mage_Core_Helper_Abstract::escapeHtml() expects array<string>|null, array<string>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $min (0) of function random_int expects lower number than parameter #2 $max (int<-1, max>).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_App::getStore() expects bool|int|Mage_Core_Model_Store|string|null, bool|int|string|Varien_Object|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $haystack of function in_array expects array, list<string>|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(string): bool)|null, Closure(string, string=): string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/EnvironmentConfigLoader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Mage_Core_Helper_EnvironmentConfigLoader::setCache() expects string, int|string given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/EnvironmentConfigLoader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Varien_Simplexml_Config::setNode() expects string, int|string given.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/EnvironmentConfigLoader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/File/Storage/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of static method Monolog\\Level::fromName() expects \'ALERT\'|\'Alert\'|\'alert\'|\'CRITICAL\'|\'Critical\'|\'critical\'|\'DEBUG\'|\'Debug\'|\'debug\'|\'EMERGENCY\'|\'Emergency\'|\'emergency\'|\'ERROR\'|\'Error\'|\'error\'|\'INFO\'|\'Info\'|\'info\'|\'NOTICE\'|\'Notice\'|\'notice\'|\'WARNING\'|\'Warning\'|\'warning\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Log.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key of method Mage_Core_Helper_String::_getLastSubkey() expects string, array<int|string, string>|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key of method Mage_Core_Helper_String::_removeSubkeyPartFromKey() expects string, array<int|string, string>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $param of method Mage_Core_Helper_String::_handleRecursiveParamForQueryStr() expects array<string, array<int|string, string>|string>, array<string, array<int|string, array<int|string, string>|string>|string> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function urldecode expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of method Mage_Core_Helper_String::strlen() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of method Mage_Core_Helper_String::substr() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $locale of function setlocale expects string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $offset of function substr expects int, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $offset of method Mage_Core_Helper_String::substr() expects int, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $prevChar of method Unserialize_Reader_ArrValue::read() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of method Mage_Core_Helper_String::substr() expects int|null, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strtolower expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $replace of function str_replace expects array<string>|string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter $countUnit of class Symfony\\Component\\Validator\\Constraints\\Length constructor expects \'bytes\'|\'codepoints\'|\'graphemes\'|null, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Validate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Abstract::_beforeLoad() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $arguments of static method Mage::getResourceModel() expects array, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $group of method Mage_Core_Model_Store::setGroup() expects Mage_Core_Model_Store_Group, Mage_Core_Model_Store_Group|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function str_starts_with expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Core_Model_App::_callObserverMethod() expects object, object|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of function mageDelTree expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $scopeCode of method Mage_Core_Model_App::_initCurrentStore() expects string, array|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $typeCode of method Mage_Core_Model_Cache::canUse() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $website of method Mage_Core_Model_Store::setWebsite() expects Mage_Core_Model_Website, Mage_Core_Model_Website|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $website of method Mage_Core_Model_Store_Group::setWebsite() expects Mage_Core_Model_Website, Mage_Core_Model_Website|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 ...$arrays of function array_merge expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $scopeType of method Mage_Core_Model_App::_initCurrentStore() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of method Mage_Core_Model_Resource::getConnection() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cache.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tags of method Mage_Core_Model_Cache::clean() expects array|string, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cache.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function current expects array|object, list<string>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function next expects array|object, list<string>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $options of method Mage_Core_Model_Config::init() expects array, array|Mage_Core_Model_Config_Options given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of method Varien_Simplexml_Config::getNode() expects string|null, list<string>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $callback of static method Mage::addObserver() expects callable(): mixed, array{object|string|false, string} given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $constructArguments of method Mage_Core_Model_Config::getModelInstance() expects array|object, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $haystack of function in_array expects array, list<string>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $options of function setcookie expects array{expires?: int, path?: string, domain?: string, secure?: bool, httponly?: bool, samesite?: \'Lax\'|\'lax\'|\'None\'|\'none\'|\'Strict\'|\'strict\'}, array{expires: mixed, path: string, domain: string, secure: bool|int, httponly: bool|null, samesite: string} given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cookie.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #7 $httponly of method Mage_Core_Model_Cookie::set() expects bool|null, bool|int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cookie.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $pattern of function preg_match expects string, array<string, int>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Date.php',
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
    'rawMessage' => 'Parameter #1 $filename of function file_exists expects string, bool|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of function filemtime expects string, bool|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of method Mage_Core_Helper_File_Storage_Database::saveFile() expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of method Mage_Core_Helper_File_Storage_Database::saveFileToFilesystem() expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $targetFile of method Mage_Core_Helper_Data::mergeFiles() expects string|false, bool|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function preg_replace_callback expects array<float|int|string>|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be string by placeholder #2 ("%%s"), string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $policyCode of method Mage_Core_Model_Domainpolicy::_getDomainPolicyByCode() expects string, int given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Domainpolicy.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $messageId of method Mage_Core_Model_Resource_Email_Queue::saveRecipients() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Queue.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $html of method Mage_Core_Model_Email_Template_Abstract::_applyInlineCss() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Mage_Core_Model_Email_Template::setId() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Abstract::load() expects int|string|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $unprocessedHtml of static method Pelago\\Emogrifier\\HtmlProcessor\\AbstractHtmlProcessor::fromHtml() expects non-empty-string, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_App::getStore() expects bool|int|Mage_Core_Model_Store|string|null, Varien_Object|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $allowedTags of method Mage_Core_Helper_Abstract::escapeHtml() expects array<string>|null, list<string>|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $known_string of function hash_equals expects string, string|false|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Encryption.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $files of method Mage_Core_Model_File_Storage_Database::importFiles() expects array, array|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Storage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, array|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Storage.php',
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
    'rawMessage' => 'Parameter #1 $data of function imagecreatefromstring expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Validator/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $width of function imagecreatetruecolor expects int<1, max>, int<0, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Validator/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $height of function imagecreatetruecolor expects int<1, max>, int<0, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Validator/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, array{Mage_Core_Helper_Abstract, mixed} given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Input/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of method Mage_Core_Model_Input_Filter::addFilter() expects string|Zend_Filter_Interface, array|Zend_Filter_Interface given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Input/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $objectOrClass of class ReflectionClass constructor expects class-string<T of object>|T of object, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Input/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Mage_Core_Helper_Purifier::purify() expects array<string>|string, array<string>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Input/Filter/MaliciousCode.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_reverse expects array, array<Varien_Simplexml_Element>|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, array{Mage_Core_Block_Abstract, string} given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, array{Mage_Core_Helper_Abstract, string} given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout.php',
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
    'rawMessage' => 'Parameter #1 $type of method Mage_Core_Model_Layout::createBlock() expects string, Mage_Core_Block_Abstract|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $parent of method Mage_Core_Model_Layout::_generateAction() expects Varien_Simplexml_Element, Varien_Simplexml_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $parent of method Mage_Core_Model_Layout::_generateBlock() expects Varien_Simplexml_Element, Varien_Simplexml_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $update of method Mage_Core_Model_Layout_Update::addUpdate() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout/Update.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $updates of method Mage_Core_Model_Layout_Update::addFallbackThemesLayoutUpdates() expects Mage_Core_Model_Config_Element, Mage_Core_Model_Config_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout/Update.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 ...$arrays of function array_merge expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout/Update.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout/Update.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $date of method Zend_Date::set() expects array|int|string|Zend_Date, array|int|string|Zend_Date|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function strpos expects string, string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function strrpos expects string, string|null given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function substr expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function ucwords expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $timezoneId of function date_default_timezone_set expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function preg_replace expects array<float|int|string>|string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $level of method Monolog\\Logger::log() expects \'alert\'|\'critical\'|\'debug\'|\'emergency\'|\'error\'|\'info\'|\'notice\'|\'warning\'|Monolog\\Level, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Logger.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $level of static method Mage_Core_Helper_Log::getLogLevelValue() expects int|Monolog\\Level|string|null, list<int|string>|Monolog\\Level::Alert|Monolog\\Level::Critical|Monolog\\Level::Debug|Monolog\\Level::Emergency|Monolog\\Level::Error|Monolog\\Level::Info|Monolog\\Level::Notice|Monolog\\Level::Warning|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Logger.php',
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
    'rawMessage' => 'Parameter #4 $currentId of method Mage_Core_Model_Resource_Design::_checkIntersection() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Design.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $messageId of method Mage_Core_Model_Resource_Email_Queue::getRecipients() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Email/Queue.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Mage_Core_Model_Resource::getIdxName() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $callback of function uksort expects callable(int|string, int|string): int, (Closure(string, string, string|null): (bool|null))|(Closure(string, string, string|null=): (bool|int)) given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $fromVersion of method Mage_Core_Model_Resource_Setup::_modifyResourceDb() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_Core_Model_Resource_Store::_updateGroupDefaultStore() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $groupId of method Mage_Core_Model_Resource_Store_Group::_updateStoreWebsite() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $websiteId of method Mage_Core_Model_Resource_Store_Group::_updateWebsiteDefaultGroup() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $groupId of method Mage_Core_Model_Resource_Store_Group::_updateWebsiteDefaultGroup() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $websiteId of method Mage_Core_Model_Resource_Store_Group::_updateStoreWebsite() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $host of method Mage_Core_Model_Session_Abstract::addHost() expects string, true given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func expects callable(): mixed, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Varien.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of method Mage_Core_Model_Cookie::delete() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Varien.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of method Mage_Core_Model_Cookie::get() expects string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Varien.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of method Mage_Core_Model_Cookie::renew() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Varien.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $options of function session_set_cookie_params expects array{lifetime?: int, path?: string|null, domain?: string|null, secure?: bool|null, httponly?: bool|null, samesite?: \'Lax\'|\'lax\'|\'None\'|\'none\'|\'Strict\'|\'strict\'}, array{lifetime: int, path: string, domain?: string, secure?: bool, httponly?: true, samesite?: string} given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Varien.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function str_contains expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $search of function str_replace expects array<string>|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function rtrim expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $url of method Mage_Core_Model_Store::_updatePathUseRewrites() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $xmlConfig of method Mage_Core_Helper_EnvironmentConfigLoader::overrideEnvironment() expects Varien_Simplexml_Config, Mage_Core_Model_Config|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $replace of function str_replace expects array<string>|string, string|null given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $groupId of method Mage_Core_Model_Resource_Store_Collection::addGroupFilter() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $scope of method Mage_Core_Model_Translate::_addData() expects string, false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Translate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func expects callable(): mixed, array{$this(Mage_Core_Model_Translate_Inline), array|string} given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Translate/Inline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func expects callable(): mixed, array|Closure|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Translate/Inline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $storeId of method Mage_Core_Model_Resource_Translate_String::deleteTranslate() expects int|null, false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Translate/Inline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Model_Url::setFragment() expects string, int<0, 65535>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Model_Url::setRouteParams() expects non-empty-array<mixed>, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $routeName of method Mage_Core_Controller_Varien_Front::getRouterByRoute() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $routeName of method Mage_Core_Controller_Varien_Router_Abstract::getFrontNameByRoute() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $routeParams of method Mage_Core_Model_Url::getRoutePath() expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $type of method Mage_Core_Model_Store::getBaseUrl() expects \'direct_link\'|\'js\'|\'link\'|\'media\'|\'skin\'|\'web\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function str_starts_with expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url/Rewrite.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function str_starts_with expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url/Rewrite/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $pathInfo of method Mage_Core_Controller_Request_Http::rewritePathInfo() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url/Rewrite/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $messageKey of method Mage_Core_Model_Validate_Abstract::_createMessage() expects string, int|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Validate/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $replace of function str_replace expects array<string>|string, string|Varien_Simplexml_Element given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Validate/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Variable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $website of method Mage_Core_Model_Resource_Store_Collection::addWebsiteFilter() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $website of method Mage_Core_Model_Resource_Store_Group_Collection::addWebsiteFilter() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $separator of function explode expects non-empty-string, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/functions.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $offset of function substr_replace expects array|int, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/functions.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $offset of function substr_replace expects array|int, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/functions.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, array{object, non-empty-string} given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cron/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $scheduleId of method Mage_Cron_Model_Resource_Schedule::trySetJobStatusAtomic() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cron/Model/Schedule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, list<string>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cron/Model/Schedule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Catalog_Model_Resource_Product_Compare_Item_Collection::setCustomerId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Account/Dashboard/Sidebar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Collection_Abstract<Mage_Catalog_Model_Product>::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Account/Dashboard/Sidebar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $str of function strtr expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Widget/Dob.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $options of method Mage_Customer_Helper_Data::_prepareNamePrefixSuffixOptions() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Customer_Model_Address::setCustomerId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $region of method Mage_Customer_Model_Address_Abstract::getRegionModel() expects int|null, float|int|string given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $haystack of function in_array expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Customer_Model_Address::setCustomerId() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Customer_Model_Address::setCustomerId() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Mage_Eav_Model_Attribute_Data_Text::validateValue() expects bool|string|null, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Attribute/Data/Postcode.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $haystack of function in_array expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Attribute/Data/Postcode.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $idx of method Varien_Object_Cache::load() expects object|string, Mage_Customer_Model_Customer|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Adapter/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityTypeId of method Mage_Eav_Model_Convert_Parser_Abstract::getAttributeSetId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Parser/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Customer_Model_Address::setCustomerId() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Parser/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $regionId of method Mage_Customer_Model_Address::setRegionId() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Parser/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $regionId of method Mage_Customer_Model_Address::setRegionId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Parser/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Customer_Helper_Data::generateResetPasswordLinkCustomerId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_Email_Template_Mailer::setStoreId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $storeId of method Mage_Customer_Model_Customer::_sendEmailTemplate() expects int|null, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function strpos expects string, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Source/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Source/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Abstract::load() expects int|string|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Address/Attribute/Backend/Region.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $entityId of method Mage_Eav_Model_Entity_Abstract::load() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entity of method Mage_Eav_Model_Entity_Collection_Abstract<Mage_Core_Model_Abstract>::setEntity() expects Mage_Eav_Model_Entity_Abstract, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Wishlist/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Customer_AccountController::_validateResetPasswordLinkToken() expects int, int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AccountController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AccountController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $arguments of static method Mage::getModel() expects array|object|string, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AccountController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $resetPasswordLinkToken of method Mage_Customer_AccountController::_validateResetPasswordLinkToken() expects string, int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AccountController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $storeId of method Mage_Customer_Model_Customer::sendNewAccountEmail() expects int|string, int|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AccountController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Customer_Model_Address::setCustomerId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AddressController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AddressController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AddressController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AddressController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Adapter_Pdo_Mysql::disableTableKeys() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/data/customer_setup/data-upgrade-1.6.2.0.4-1.6.2.0.5.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Adapter_Pdo_Mysql::enableTableKeys() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/data/customer_setup/data-upgrade-1.6.2.0.4-1.6.2.0.5.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Select::insertFromSelect() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/data/customer_setup/data-upgrade-1.6.2.0.4-1.6.2.0.5.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $id of method Mage_Eav_Model_Entity_Setup::getAttributeTable() expects int|string, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/data/customer_setup/data-upgrade-1.6.2.0.4-1.6.2.0.5.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, object|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Batch.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Batch/Io.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%d"), int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Batch/Io.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $container of method Mage_Dataflow_Model_Convert_Action_Abstract::setContainer() expects Mage_Dataflow_Model_Convert_Container_Interface, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Action/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_split expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Adapter/Http/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function fread expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Adapter/Std.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $item of method Mage_Dataflow_Model_Convert_Container_Collection::addItem() expects Mage_Dataflow_Model_Convert_Container_Interface, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Container/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $idx of method Varien_Object_Cache::load() expects object|string, Mage_Dataflow_Model_Batch_Export|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Mapper/Column.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $idx of method Varien_Object_Cache::load() expects object|string, Mage_Dataflow_Model_Batch_Import|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Mapper/Column.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $idx of method Varien_Object_Cache::load() expects object|string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, object|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, object|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Xml/Excel.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Xml/Excel.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Xml/Excel.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $container of method Mage_Dataflow_Model_Convert_Profile_Collection::addContainer() expects Mage_Dataflow_Model_Convert_Container_Interface, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Profile/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $source of method DOMDocument::loadXML() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function substr expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $id of method Mage_Dataflow_Model_Resource_Profile::isProfileExists() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Varien_Db_Select::where() expects array|float|int|string|Zend_Db_Expr|Zend_Db_Select|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Resource/Batch/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $countryId of method Mage_Directory_Model_Resource_Region_Collection::addCountryFilter() expects array|string, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $countryId of method Mage_Directory_Model_Resource_Region_Collection::addCountryFilter() expects array|string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Country.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Mage_Core_Model_Locale::getCountryTranslation() expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Country.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $options of method Zend_Currency::toCurrency() expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be float by placeholder #1 ("%%F"), float|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $uri of method Zend_Http_Client::setUri() expects string|Zend_Uri_Http, array<string>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency/Import/Webservicex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $modelClass of static method Mage::getModel() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, Mage_Core_Model_Config_Element|false given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Resource/Currency.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function feof expects resource, resource|Varien_Io_File given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Helper/Download.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function fgets expects resource, resource|Varien_Io_File given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Helper/Download.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_match expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Helper/Download.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product of method Mage_Downloadable_Model_Resource_Link_Collection::addProductToFilter() expects array|int|Mage_Catalog_Model_Product|null, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product of method Mage_Downloadable_Model_Resource_Sample_Collection::addProductToFilter() expects array|int|Mage_Catalog_Model_Product|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Downloadable_Model_Link::getSearchableData() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Downloadable_Model_Sample::getSearchableData() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Varien_Data_Collection_Db<Mage_Downloadable_Model_Sample>::addFieldToFilter() expects array|int|string|null, int<min, -1>|int<1, max>|Mage_Catalog_Model_Product given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Resource/Sample/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $linkType of method Mage_Downloadable_Helper_Download::setResource() expects \'file\'|\'url\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/controllers/Adminhtml/Downloadable/Product/EditController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Zend_Controller_Response_Abstract::setHeader() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/controllers/Adminhtml/Downloadable/Product/EditController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $linkType of method Mage_Downloadable_Helper_Download::setResource() expects \'file\'|\'url\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/controllers/DownloadController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Zend_Controller_Response_Abstract::setHeader() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/controllers/DownloadController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityTypeCode of method Mage_Eav_Helper_Data::getAttributeLockedFields() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Block/Adminhtml/Attribute/Edit/Main/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityTypeCode of method Mage_Eav_Helper_Data::getFrontendClasses() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Block/Adminhtml/Attribute/Edit/Main/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attr of method Mage_ConfigurableSwatches_Helper_Data::attrIsSwatchType() expects int|Mage_Eav_Model_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Block/Adminhtml/Attribute/Edit/Options/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $setId of method Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection::setAttributeFilter() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Block/Adminhtml/Attribute/Edit/Options/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract::getStoreOptionValues() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Block/Adminhtml/Attribute/Edit/Options/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Multiline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Multiline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Multiline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $modelClass of static method Mage::getResourceModel() expects string, Mage_Eav_Model_Entity_Interface given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Convert/Adapter/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, array{mixed, string} given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Eav_Model_Entity_Abstract::_afterDelete() expects Varien_Object, int|string|Varien_Object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Eav_Model_Entity_Abstract::_beforeDelete() expects Varien_Object, int|string|Varien_Object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Eav_Model_Entity_Abstract::loadAllAttributes() expects object|null, int|string|Varien_Object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $setId of method Mage_Eav_Model_Entity_Attribute_Abstract::isInSet() expects array|int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute of method Mage_Eav_Model_Entity_Abstract::_insertAttribute() expects Mage_Eav_Model_Entity_Attribute_Abstract, Mage_Catalog_Model_Resource_Eav_Attribute|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute of method Mage_Eav_Model_Entity_Abstract::_updateAttribute() expects Mage_Eav_Model_Entity_Attribute_Abstract, Mage_Catalog_Model_Resource_Eav_Attribute|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $object of method Mage_Eav_Model_Config::getEntityAttributeCodes() expects Varien_Object|null, object|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%d"), int<min, -1>|int<1, max>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attributeId of method Mage_Eav_Model_Resource_Entity_Attribute::getStoreLabelsByAttributeId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $entityTypeId of method Mage_Eav_Model_Resource_Entity_Attribute::loadByCode() expects int, float|int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_match expects string, int<min, -1>|int<1, max>|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Datetime.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $groupId of method Mage_Eav_Model_Resource_Entity_Attribute_Collection::setAttributeGroupFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Set.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $setId of method Mage_Eav_Model_Resource_Entity_Attribute_Set::getDefaultGroupId() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Set.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function strpos expects string, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $setId of method Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection::setAttributeFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute of method Mage_Eav_Model_Resource_Entity_Attribute_Option::addOptionValueToCollection() expects Mage_Eav_Model_Entity_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $resource of method Mage_Eav_Model_Entity_Collection_Abstract<Mage_Core_Model_Abstract>::__construct() expects Mage_Core_Model_Resource_Abstract|Varien_Db_Adapter_Interface|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attributeCode of method Mage_Eav_Model_Entity_Collection_Abstract<T of Mage_Core_Model_Abstract>::_addAttributeJoin() expects string, int|Mage_Core_Model_Config_Element|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $className of method Varien_Data_Collection<T of Mage_Core_Model_Abstract>::setItemObjectClass() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $conn of method Varien_Data_Collection_Db<T of Mage_Core_Model_Abstract>::setConnection() expects Varien_Db_Adapter_Interface, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $code of method Mage_Eav_Model_Entity_Setup::updateEntityType() expects string, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $id of method Mage_Core_Model_Resource_Setup::deleteTableRow() expects int|string, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $id of method Mage_Core_Model_Resource_Setup::updateTableRow() expects int|string, int|false given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $value of method Mage_Eav_Model_Entity_Setup::updateEntityType() expects string|null, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityTypeId of method Mage_Eav_Model_Entity_Store::loadByEntityStore() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $typeId of method Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection::setEntityTypeFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attribute of static method Mage_Eav_Model_Attribute_Data::factory() expects Mage_Eav_Model_Attribute, Mage_Eav_Model_Entity_Attribute given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $scope of method Mage_Eav_Model_Attribute_Data_Abstract::setRequestScope() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Adapter_Pdo_Mysql::describeTable() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Adapter_Interface::describeTable() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attributeSetId of method Mage_Eav_Model_Resource_Entity_Attribute_Group::updateDefaultGroup() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Entity/Attribute/Set.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Helper/Message.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $type of method Mage_GiftMessage_Helper_Message::isMessagesAvailable() expects \'address_item\'|\'config\'|\'item\'|\'items\'|\'order\'|\'order_item\', string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Helper/Message.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityId of method Mage_GiftMessage_Model_Api::_setGiftMessage() expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Model/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $quoteItemId of method Mage_GiftMessage_Model_Api::setForQuoteItem() expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Model/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of static method Mage::log() expects array|object|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GoogleAnalytics/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $num of function number_format expects float, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GoogleAnalytics/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shorthand of function ini_parse_quantity expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function substr expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, bool|float|int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_intersect expects an array of values castable to string, array<string|void> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, array|float|int|string|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, list<string>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $str of method Mage_Core_Helper_UnserializeArray::unserialize() expects string|null, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array<bool>|void given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $typeId of method Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection::setEntityTypeFilter() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(mixed): int<0, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $setId of method Mage_Eav_Model_Resource_Entity_Attribute_Collection::setAttributeSetFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $typeId of method Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection::setEntityTypeFilter() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filePath of method Mage_ImportExport_Model_Import_Uploader::_setUploadFile() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Uploader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, array{Mage_Index_Model_Process, string} given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 ...$values of function sprintf expects bool|float|int|string|null, array<string, int|string>|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Lock.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $processId of method Mage_Index_Model_Event::addProcessId() expects int, int|string|null given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Process.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $processId of method Mage_Index_Model_Resource_Process::updateEventStatus() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Process.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $eventId of method Mage_Index_Model_Resource_Process::updateEventStatus() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Process.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $adapter of method Varien_Db_Adapter_Interface::setCacheAdapter() expects Zend_Cache_Backend_Interface, Zend_Cache_Core given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Resource/Lock/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_diff_assoc expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Resource/Lock/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $source of method Varien_Simplexml_Element::extend() expects Varien_Simplexml_Element, Mage_Core_Model_Config_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Resource/Lock/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_diff_assoc expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Resource/Lock/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $processId of method Mage_Index_Model_Resource_Process::_updateProcessData() expects int, int|string|null given.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Resource/Process.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Block/Begin.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Mage_Core_Model_Resource_Setup::setConfigData() expects string, int given.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|false given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function preg_replace expects array<float|int|string>|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer/Console.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_match expects string, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_Log_Model_Aggregation::_process() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Aggregation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $fieldName of method Mage_Log_Model_Resource_Visitor_Collection::_getFieldMap() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Visitor/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dst_image of function imagecopyresampled expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/File/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagedestroy expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/File/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagegif expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/File/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagejpeg expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/File/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagepng expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/File/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagewebp expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/File/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $src_image of function imagecopyresampled expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/File/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $field of method Mage_Newsletter_Model_Resource_Queue_Collection::_getIdsFromLink() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Resource/Queue/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Varien_Db_Select::where() expects array|float|int|string|Zend_Db_Expr|Zend_Db_Select|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Resource/Queue/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_Email_Template_Filter::setStoreId() expects int|Mage_Core_Model_Store, Mage_Core_Model_Store|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $name of method Zend_Mail::addTo() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Oauth_Model_Resource_Token_Collection::addFilterByCustomerId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Block/Customer/Token/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attrName of method Mage_Oauth_Model_Server::_isProtocolParameter() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Model/Server.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $consumerId of method Mage_Oauth_Model_Token::createRequestToken() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Model/Server.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function parse_str expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Model/Server.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of method Mage_Core_Model_Session_Abstract::addError() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/controllers/Adminhtml/Oauth/ConsumerController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Oauth_Model_Resource_Token_Collection::addFilterByCustomerId() expects int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/controllers/Customer/TokenController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #7 $before of method Mage_Page_Block_Html_Head::addItem() expects bool|string, bool|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Head.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $page of method Mage_Page_Block_Html_Pager::getPageUrl() expects int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Pager.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $offset of function array_splice expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Toplinks.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $params of method Mage_Page_Block_Template_Links::_prepareParams() expects array|string, array|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Template/Links.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $position of method Mage_Page_Block_Template_Links::_addIntoPosition() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Template/Links.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $layoutCode of method Mage_Page_Model_Config::getPageLayout() expects string, int|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Helper/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Block/Authorizenet/Form/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of static method Mage_Core_Helper_Data::currency() expects float, float|int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Block/Authorizenet/Info/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of class Mage_Payment_Model_Info_Exception constructor expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, bool|string given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be float by placeholder #1 ("%%.2F"), float|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cardId of method Mage_Paygate_Model_Authorizenet_Cards::getCard() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet/Cards.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%02d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Block/Info/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $alias of method Mage_Core_Block_Abstract::setChild() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Block/Info/Container.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name of method Mage_Core_Block_Abstract::getChild() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Block/Info/Container.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of method Exception::__construct() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Exception.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $haystack of function in_array expects array, array<string, class-string>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function uasort expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function array_key_exists expects array, array|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_Payment_Model_Recurring_Profile::setStore() expects Mage_Core_Model_Store, Mage_Core_Model_Store|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Payment_Model_Recurring_Profile::setMethodInstance() expects Mage_Payment_Model_Method_Abstract, Mage_Payment_Model_Method_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $groupConfig of method Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_PathDependent::hasActivePathDependencies() expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Fieldset/PathDependent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $customerId of method Mage_Paypal_Helper_Data::shouldAskToCreateBillingAgreement() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Express/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Express/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $quote of method Mage_Paypal_Block_Express_Shortcut::_getBmlShortcut() expects Mage_Sales_Model_Quote, Mage_Sales_Model_Quote|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Express/Shortcut.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $customerId of method Mage_Paypal_Helper_Data::shouldAskToCreateBillingAgreement() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Express/Shortcut.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $setToken of method Mage_Paypal_Controller_Express_Abstract::_initToken() expects string|null, false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $token of method Mage_Paypal_Model_Config::getExpressCheckoutEditUrl() expects string, $this(Mage_Paypal_Controller_Express_Abstract)|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $token of method Mage_Paypal_Model_Config::getExpressCheckoutStartUrl() expects string, $this(Mage_Paypal_Controller_Express_Abstract)|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $token of method Mage_Paypal_Model_Express_Checkout::place() expects string, $this(Mage_Paypal_Controller_Express_Abstract)|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $token of method Mage_Paypal_Model_Express_Checkout::prepareOrderReview() expects string|null, $this(Mage_Paypal_Controller_Express_Abstract)|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $token of method Mage_Paypal_Model_Express_Checkout::returnFromPaypal() expects string, $this(Mage_Paypal_Controller_Express_Abstract)|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func expects callable(): mixed, array{$this(Mage_Paypal_Model_Api_Abstract), mixed} given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be float by placeholder #1 ("%%.2F"), float|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $request of method Mage_Paypal_Model_Api_Nvp::_applyCountryWorkarounds() expects array, array|Varien_Object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_split expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $target of method Mage_Paypal_Model_Api_Abstract::_importStreetFromAddress() expects array<mixed>, array|Varien_Object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $target of method Mage_Paypal_Model_Api_Abstract::_importStreetFromAddress() expects array<mixed>, array|Varien_Object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of static method Mage::getStoreConfig() expects string, string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strtolower expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strtolower expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $orderTotal of method Mage_Paypal_Model_Config::_getDynamicImageUrl() expects float, float|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $pal of method Mage_Paypal_Model_Config::_getDynamicImageUrl() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Paypal_Model_Config::setStoreId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%02d"), int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%02d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%02d"), int|string given.',
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
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Paypal_Model_Config::setStoreId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $transaction of method Mage_Paypal_Model_Express::_isTransactionExpired() expects Mage_Sales_Model_Order_Payment_Transaction, Mage_Sales_Model_Order_Payment_Transaction|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Customer_Model_Session::loginById() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express/Checkout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express/Checkout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_Paypal_Model_Hostedpro::_getUrl() expects int, int|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Hostedpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be float by placeholder #1 ("%%.2F"), float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Hostedpro/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $source of static method Varien_Object_Mapper::accumulateByMap() expects array|Varien_Object, array|(callable) given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $target of static method Varien_Object_Mapper::accumulateByMap() expects array|Varien_Object, array|(callable)|Varien_Object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function end expects array|object, list<string>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $code of static method Mage_Paypal_Model_Info::explainPendingReason() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $code of static method Mage_Paypal_Model_Info::explainReasonCode() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $code of static method Mage_Paypal_Model_Info::isReversalDisputable() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ipnPaymentStatus of method Mage_Paypal_Model_Ipn::_filterPaymentStatus() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment of static method Mage_Paypal_Model_Info::isPaymentFailed() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment of static method Mage_Paypal_Model_Info::isPaymentReviewRequired() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment of static method Mage_Paypal_Model_Info::isPaymentSuccessful() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strtolower expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $payment of method Mage_Paypal_Model_Info::importToPayment() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $txnId of method Mage_Paypal_Helper_Data::getHtmlTransactionId() expects string, array|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $url of method Zend_Http_Client_Adapter_Interface::write() expects Zend_Uri_Http, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Paypal_Model_Config::setStoreId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $encodedValue of method Mage_Core_Helper_Data::jsonDecode() expects string, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment of method Mage_Paypal_Model_Payflowlink::_buildTokenRequest() expects Mage_Sales_Model_Order_Payment, Mage_Sales_Model_Order_Payment|Mage_Sales_Model_Quote_Payment given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowlink.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment of method Mage_Paypal_Model_Payflowlink::_getSecureSilentPostHash() expects Mage_Sales_Model_Order_Payment, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowlink.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment of method Mage_Paypal_Model_Payflowpro::fetchTransactionInfo() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowlink.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Paypal_Model_Config::setStoreId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowlink.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function implode expects array, array|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowlink.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment of method Mage_Paypal_Model_Payflowpro::_buildBasicRequest() expects Mage_Sales_Model_Order_Payment, Mage_Payment_Model_Info given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Paypal_Model_Config::setStoreId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function implode expects array, array|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $columns of method Mage_Paypal_Model_Resource_Payment_Transaction::_getLoadByUniqueKeySelect() expects array|string|Zend_Db_Expr, array|object|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Resource/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Helper_Data::encrypt() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/System/Config/Backend/Cert.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function substr expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/PaypalUk/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%02d"), int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/PaypalUk/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%02d"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/PaypalUk/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Customer_Model_Session::setCustomerId() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Persistent_Model_Persistent_Config::fireOne() expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Persistent_Model_Session::deleteByCustomerId() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Observer/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Persistent_Model_Session::loadByCustomerId() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Observer/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of class Varien_Simplexml_Element constructor expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Persistent/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, object|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Persistent/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $timestamp of function gmdate expects int|null, float|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_App_Emulation::startEnvironmentEmulation() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Model/Email.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_ProductAlert_Model_Price::deleteCustomer() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/controllers/UnsubscribeController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_ProductAlert_Model_Stock::deleteCustomer() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/controllers/UnsubscribeController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Rating_Model_Resource_Rating_Collection::addRatingPerStoreName() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Block/Entity/Detailed.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Rating_Model_Resource_Rating_Collection::setStoreFilter() expects array|int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Block/Entity/Detailed.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_Rating_Model_Resource_Rating_Collection::addEntitySummaryToItem() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Block/Entity/Detailed.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $rating of method Mage_Rating_Model_Resource_Rating_Option_Collection::addRatingFilter() expects array|int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Rating.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $optionId of method Mage_Rating_Model_Resource_Rating_Option::loadDataById() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Resource/Rating/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $visitorId of method Mage_Reports_Model_Event::updateCustomerType() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Event/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $customerId of method Mage_Reports_Model_Event::updateCustomerType() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Event/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customer of method Mage_Sales_Model_Quote::loadByCustomer() expects int|Mage_Customer_Model_Customer, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Customer/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $isFilter of method Mage_Reports_Model_Resource_Order_Collection::_calculateTotalsAggregated() expects int, bool|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $isFilter of method Mage_Reports_Model_Resource_Order_Collection::_calculateTotalsLive() expects int, bool|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $range of method Mage_Reports_Model_Resource_Order_Collection::_getRangeExpression() expects \'1m\'|\'1y\'|\'24h\'|\'2y\'|\'3m\'|\'6m\'|\'7d\'|\'custom\', string given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $range of method Mage_Reports_Model_Resource_Order_Collection::getDateRange() expects \'1m\'|\'1y\'|\'24h\'|\'2y\'|\'3m\'|\'6m\'|\'7d\'|\'custom\', string given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $interval of method Varien_Db_Adapter_Interface::getDateAddSql() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Varien_Data_Collection_Db<Mage_Catalog_Model_Product>::_getConditionSql() expects array|int|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Downloads/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dateFrom of method Mage_Reports_Model_Resource_Product_Viewed_Collection::_joinFields() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Viewed/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $dateTo of method Mage_Reports_Model_Resource_Product_Viewed_Collection::_joinFields() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Viewed/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Review_Model_Resource_Review_Product_Collection::addCustomerFilter() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Customer/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Review_Model_Resource_Review_Product_Collection::addCustomerFilter() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Customer/Recent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityPkValue of method Mage_Rating_Model_Rating::getEntitySummary() expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Customer/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityPkValue of method Mage_Review_Model_Review::getTotalReviews() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Customer/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Rating_Model_Resource_Rating_Option_Vote_Collection::setStoreFilter() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Customer/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Rating_Model_Resource_Rating_Collection::addRatingPerStoreName() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Rating_Model_Resource_Rating_Collection::setStoreFilter() expects array|int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId of method Mage_Review_Model_Review::getEntitySummary() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Helper.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Review_Model_Resource_Review_Collection::addStoreFilter() expects array|int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Product/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $pkValue of method Mage_Review_Model_Resource_Review_Collection::addEntityFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Product/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityPkValue of method Mage_Rating_Model_Rating::getEntitySummary() expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $entityPkValue of method Mage_Review_Model_Review::getTotalReviews() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Rating_Model_Resource_Rating_Option_Vote_Collection::setStoreFilter() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $storeId of method Mage_Review_Model_Review::getTotalReviews() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $reviewId of method Mage_Review_Model_Resource_Review::_loadVotedRatingIds() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $reviewId of method Mage_Rating_Model_Resource_Rating_Option_Vote_Collection::setReviewFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Rating_Model_Resource_Rating_Option_Vote_Collection::setStoreFilter() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $fieldName of method Varien_Data_Collection_Db<Mage_Catalog_Model_Product>::_getConditionSql() expects string, Mage_Eav_Model_Entity_Attribute_Abstract|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Rating_Model_Resource_Rating_Option_Vote_Collection::setStoreFilter() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Review_Model_Resource_Review_Product_Collection::setStoreFilter() expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Varien_Data_Collection_Db<Mage_Catalog_Model_Product>::_getConditionSql() expects array|int|string, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Review_Model_Resource_Review_Summary_Collection::addStoreFilter() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productTypeId of method Mage_Rss_Block_Catalog_Abstract::_getPriceBlock() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Block/Catalog/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productTypeId of method Mage_Rss_Block_Catalog_Abstract::_getPriceBlockTemplate() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Block/Catalog/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Collection_Abstract<Mage_Catalog_Model_Product>::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Block/Catalog/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Catalog_Model_Resource_Collection_Abstract<Mage_Catalog_Model_Product>::setStoreId() expects int|Mage_Core_Model_Store|string, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Block/Catalog/New.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributeHtml of method Mage_Catalog_Helper_Output::productAttribute() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Block/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 ...$arrays of function array_merge expects array, array|float|int|string|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function base64_encode expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Helper/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function stripos expects string, bool|float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $renderer of method Varien_Data_Form_Element_Abstract::setRenderer() expects Varien_Data_Form_Element_Renderer_Interface, object|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $str of function preg_quote expects string, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_match expects string, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $condition of method Mage_Rule_Model_Condition_Combine::addCondition() expects Mage_Rule_Model_Condition_Abstract, Mage_Rule_Model_Condition_Abstract|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Combine.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $xml of method Mage_Rule_Model_Condition_Abstract::loadXml() expects SimpleXMLElement|string, SimpleXMLElement|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Combine.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $time of static method Carbon\\Carbon::parse() expects DateTimeInterface|string|null, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $typeId of method Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection::setEntityTypeFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Mage_Rule_Model_Condition_Product_Abstract::bindArrayOfIds() expects array|int|string, array|float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $value of method Mage_Rule_Model_Resource_Rule_Condition_SqlBuilder::getOperatorCondition() expects array|string, array|float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Print.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Print/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Print/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Print/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ids of method Mage_Sales_Model_Resource_Order_Collection::addRecurringProfilesFilter() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Recurring/Profile/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_Payment_Model_Recurring_Profile::setStore() expects Mage_Core_Model_Store, Mage_Core_Model_Store|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Recurring/Profiles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ids of method Mage_Sales_Model_Resource_Order_Abstract::updateGridRecords() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $date of method Mage_Core_Model_Locale::storeDate() expects array|int|string|Zend_Date|null, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, int|list<array> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Api2/Order/Address/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $agreementId of method Mage_Sales_Model_Resource_Billing_Agreement::addOrderRelation() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Billing/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $orderId of method Mage_Sales_Model_Resource_Billing_Agreement::addOrderRelation() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Billing/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_App::loadCache() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Config/Ordered.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $id of method Mage_Core_Model_App::saveCache() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Config/Ordered.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $address of method Mage_Sales_Model_Convert_Order::addressToQuoteAddress() expects Mage_Sales_Model_Order_Address, Mage_Sales_Model_Order_Address|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Convert/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Creditmemo/Comment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Creditmemo/Comment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Creditmemo/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Creditmemo/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice/Comment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice/Comment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Shipment/Comment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Shipment/Comment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Shipment/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Shipment/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Shipment/Track.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Shipment/Track.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Status/History.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Status/History.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Address/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Address/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Address/Rate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Address/Rate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $read of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $write of method Mage_Eav_Model_Entity_Abstract::setConnection() expects string|Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|null, Varien_Db_Adapter_Interface|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key of method Varien_Object::getData() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Sale/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_merge expects array, array<int>|void given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item of method Mage_Sales_Model_Order_Item::setParentItem() expects Mage_Sales_Model_Order_Item, Mage_Sales_Model_Order_Item|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $rates of method Mage_Tax_Model_Calculation::reproduceProcess() expects array, int|list<array> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_App_Emulation::startEnvironmentEmulation() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_Email_Template_Mailer::setStoreId() expects int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Sales_Model_Api_Resource::_getAttributes() expects Mage_Core_Model_Abstract, Mage_Sales_Model_Order_Address|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Sales_Model_Api_Resource::_getAttributes() expects Mage_Core_Model_Abstract, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sourceData of method Mage_Core_Model_Config_Base::__construct() expects string|null, Mage_Core_Model_Config_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $state of method Mage_Sales_Model_Order_Config::_getState() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $state of method Mage_Sales_Model_Resource_Order_Status_Collection::addStateFilter() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $creditmemoId of method Mage_Sales_Model_Resource_Order_Creditmemo_Comment_Collection::setCreditmemoFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $creditmemoId of method Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection::setCreditmemoFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_App_Emulation::startEnvironmentEmulation() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_Email_Template_Mailer::setStoreId() expects int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $comment of method Mage_Sales_Model_Order_Creditmemo::sendEmail() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sourceData of method Mage_Core_Model_Config_Base::__construct() expects string|null, Mage_Core_Model_Config_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $invoiceId of method Mage_Sales_Model_Resource_Order_Invoice_Comment_Collection::setInvoiceFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $invoiceId of method Mage_Sales_Model_Resource_Order_Invoice_Item_Collection::setInvoiceFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_App_Emulation::startEnvironmentEmulation() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_Email_Template_Mailer::setStoreId() expects int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $comment of method Mage_Sales_Model_Order_Invoice::sendEmail() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $comment of method Mage_Sales_Model_Order_Invoice::sendEmail() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sourceData of method Mage_Core_Model_Config_Base::__construct() expects string|null, Mage_Core_Model_Config_Element|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $amount of method Mage_Sales_Model_Order_Payment::_formatPrice() expects float, float|string given.',
    'count' => 10,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $comment of method Mage_Sales_Model_Order::addStatusHistoryComment() expects string, Mage_Sales_Model_Order_Status_History|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $comment of method Mage_Sales_Model_Order::registerCancellation() expects string, Mage_Sales_Model_Order_Status_History|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment of method Mage_Sales_Model_Resource_Order_Payment_Transaction_Collection::addPaymentIdFilter() expects int|Mage_Sales_Model_Order_Payment, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $message of method Mage_Sales_Model_Order_Payment::_appendTransactionToMessage() expects string, Mage_Sales_Model_Order_Status_History|string given.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $salesDocument of method Mage_Sales_Model_Order_Payment::_addTransaction() expects Mage_Sales_Model_Abstract|null, Mage_Sales_Model_Order_Invoice|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $comment of method Mage_Sales_Model_Order::setState() expects string, Mage_Sales_Model_Order_Status_History|string given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $order of method Mage_Sales_Model_Order_Payment_Transaction::setOrder() expects bool|Mage_Sales_Model_Order_Payment|null, Mage_Sales_Model_Order given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $orderId of method Mage_Sales_Model_Resource_Order_Payment_Transaction::getOrderWebsiteId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $parentId of method Mage_Sales_Model_Resource_Order_Payment_Transaction_Collection::addParentIdFilter() expects int, int|string|null given.',
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
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, $this(Mage_Sales_Model_Order_Payment_Transaction)|array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $haystack of function in_array expects array, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $orderId of method Mage_Sales_Model_Resource_Order_Payment_Transaction::loadObjectByTxnId() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $paymentId of method Mage_Sales_Model_Resource_Order_Payment_Transaction::loadObjectByTxnId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $address of method Mage_Sales_Model_Order_Pdf_Abstract::_formatAddress() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function usort expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $str of method Mage_Core_Helper_String::str_split() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $text of method Zend_Pdf_Canvas_Abstract::drawText() expects string, (float|int) given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Shipment/Packaging.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $rates of method Mage_Tax_Model_Calculation::reproduceProcess() expects array, int|list<array> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Total/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $info of method Mage_Payment_Helper_Data::getInfoBlock() expects Mage_Payment_Model_Info, Mage_Sales_Model_Order_Payment|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shipmentId of method Mage_Sales_Model_Resource_Order_Shipment_Comment_Collection::setShipmentFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shipmentId of method Mage_Sales_Model_Resource_Order_Shipment_Item_Collection::setShipmentFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shipmentId of method Mage_Sales_Model_Resource_Order_Shipment_Track_Collection::setShipmentFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_App_Emulation::startEnvironmentEmulation() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_Email_Template_Mailer::setStoreId() expects int, int|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $comment of method Mage_Sales_Model_Order_Shipment::addComment() expects Mage_Sales_Model_Order_Shipment_Comment|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Api.php',
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
    'rawMessage' => 'Parameter #2 $comment of method Mage_Sales_Model_Order_Shipment::sendEmail() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $comment of method Mage_Sales_Model_Order_Shipment::addComment() expects Mage_Sales_Model_Order_Shipment_Comment|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Sales_Model_Order_Shipment_Api::_getCarriers() expects Mage_Eav_Model_Entity_Abstract, Mage_Sales_Model_Order given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $comment of method Mage_Sales_Model_Order_Shipment::sendEmail() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Sales_Model_Billing_Agreement::getAvailableCustomerBillingAgreements() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Payment/Method/Billing/AgreementAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $quote of method Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract::_isAvailable() expects Mage_Sales_Model_Quote, Mage_Sales_Model_Quote|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Payment/Method/Billing/AgreementAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Sales_Model_Quote::removeItem() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Sales_Model_Quote::removeItem() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $quoteId of method Mage_Sales_Model_Resource_Quote_Address_Collection::setQuoteFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $quoteId of method Mage_Sales_Model_Resource_Quote_Payment_Collection::setQuoteFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $customerId of method Mage_Sales_Model_Resource_Quote::loadByCustomerId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $addressId of method Mage_Sales_Model_Resource_Quote_Address_Item_Collection::setAddressFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $addressId of method Mage_Sales_Model_Resource_Quote_Address_Rate_Collection::setAddressFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Sales_Model_Quote::removeItem() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Subtotal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Sales_Model_Quote_Address::removeItem() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Subtotal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $option of method Mage_Sales_Model_Quote_Item::addOption() expects array|Varien_Object, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $haystack of function in_array expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Item/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item of method Mage_Sales_Model_Order::addItem() expects Mage_Sales_Model_Order_Item, Mage_Sales_Model_Order_Item|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $recurringProfileId of method Mage_Sales_Model_Resource_Recurring_Profile::addOrderRelation() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ids of method Mage_Sales_Model_Resource_Order_Abstract::updateGridRecords() expects array|int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Adapter_Pdo_Mysql::describeTable() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tableName of method Varien_Db_Select::insertFromSelect() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attribute of method Mage_Sales_Model_Resource_Collection_Abstract<Mage_Sales_Model_Order>::addAttributeToFilter() expects Mage_Eav_Model_Entity_Attribute|string, array|Mage_Eav_Model_Entity_Attribute|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Varien_Data_Collection_Db<Mage_Sales_Model_Order>::_getConditionSql() expects array|int|string, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item of method Mage_Sales_Model_Order_Item::setParentItem() expects Mage_Sales_Model_Order_Item, Mage_Sales_Model_Order_Item|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Item/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $columns of method Mage_Sales_Model_Resource_Order_Payment_Transaction::_getLoadByUniqueKeySelect() expects array|string|Zend_Db_Expr, array|object|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $parentItem of method Mage_Sales_Model_Quote_Item_Abstract::setParentItem() expects Mage_Sales_Model_Quote_Item_Abstract, Mage_Sales_Model_Quote_Address_Item|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Quote/Address/Item/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $optionProduct of method Mage_Catalog_Model_Product_Type_Abstract::assignProductToOption() expects Mage_Catalog_Model_Product, Mage_Catalog_Model_Product|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Quote/Item/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $parentItem of method Mage_Sales_Model_Quote_Item_Abstract::setParentItem() expects Mage_Sales_Model_Quote_Item_Abstract, Mage_Sales_Model_Quote_Item|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Quote/Item/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Varien_Db_Select::where() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Bestsellers.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Zend_Db_Select::having() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Bestsellers.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Varien_Db_Select::where() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Invoiced.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Zend_Db_Select::having() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Invoiced.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Varien_Db_Select::where() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Order/Createdat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Zend_Db_Select::having() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Order/Createdat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Varien_Db_Select::where() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Refunded.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Zend_Db_Select::having() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Refunded.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Varien_Db_Select::where() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Zend_Db_Select::having() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $entityTypeId of method Mage_Sales_Model_Resource_Setup::_addGridAttribute() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item of method Mage_Sales_Model_Order_Item::setParentItem() expects Mage_Sales_Model_Order_Item, Mage_Sales_Model_Order_Item|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Service/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, array given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Service/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $quoteItemId of method Mage_Sales_Model_Order::getItemByQuoteItemId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Service/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function implode expects array, array|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Service/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $haystack of function in_array expects array, array|int<min, -1>|int<1, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Status/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerIdSession of method Mage_Sales_Model_Billing_Agreement::canPerformAction() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/controllers/Billing/AgreementController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $min (0) of function random_int expects lower number than parameter #2 $max (int<-1, max>).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Coupon/Codegenerator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $min (0) of function random_int expects lower number than parameter #2 $max (int<-1, max>).',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Coupon/Massgenerator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $combine of method Mage_SalesRule_Model_Observer::_removeAttributeFromConditions() expects Mage_Rule_Model_Condition_Combine, Mage_Rule_Model_Action_Collection given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $websiteId of method Mage_SalesRule_Model_Resource_Rule::getActiveAttributes() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $couponId of method Mage_SalesRule_Model_Resource_Coupon_Usage::updateCustomerCouponTimesUsed() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Varien_Db_Select::where() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Report/Rule/Createdat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Zend_Db_Select::having() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Report/Rule/Createdat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_Rule_Model_Resource_Abstract::getCustomerGroupIds() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_Rule_Model_Resource_Abstract::getWebsiteIds() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_SalesRule_Model_Resource_Rule::saveStoreLabels() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleIds of method Mage_Rule_Model_Resource_Abstract::bindRuleToEntity() expects array|int|string, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $rule of method Mage_SalesRule_Model_Coupon::loadPrimaryByRule() expects int|Mage_SalesRule_Model_Rule, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_Rule_Model_Resource_Abstract::getCustomerGroupIds() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_SalesRule_Model_Resource_Rule::getStoreLabels() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function array_key_exists expects array, array|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Product/Combine.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_SalesRule_Model_Validator::getCartFixedRuleUsedForAddress() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_SalesRule_Model_Validator::setCartFixedRuleUsedForAddress() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $itemId of method Mage_SalesRule_Model_Validator::setCartFixedRuleUsedForAddress() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $ruleId of method Mage_SalesRule_Model_Rule_Customer::loadByCustomerRule() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $couponId of method Mage_SalesRule_Model_Resource_Coupon_Usage::loadByCustomerCoupon() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $model of method Mage_Shipping_Helper_Data::_getTrackingUrl() expects int|Mage_Sales_Model_Order|Mage_Sales_Model_Order_Shipment|Mage_Sales_Model_Order_Shipment_Track, Mage_Sales_Model_Order_Shipment_Track|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $model of method Mage_Shipping_Helper_Data::_getTrackingUrl() expects int|Mage_Sales_Model_Order|Mage_Sales_Model_Order_Shipment|Mage_Sales_Model_Order_Shipment_Track, Mage_Sales_Model_Order_Shipment|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $model of method Mage_Shipping_Helper_Data::_getTrackingUrl() expects int|Mage_Sales_Model_Order|Mage_Sales_Model_Order_Shipment|Mage_Sales_Model_Order_Shipment_Track, Mage_Sales_Model_Order|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cost of method Mage_Shipping_Model_Carrier_Abstract::getFinalPriceWithHandlingFee() expects float, float|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Flatrate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Resource/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $data of method Varien_Db_Adapter_Pdo_Mysql::insertArray() expects array<int, list>, non-empty-array<int, array<mixed>> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Resource/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #5 of function sprintf is expected to be float by placeholder #4 ("%%F"), float|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Resource/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Abstract::load() expects int|string|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, Mage_Shipping_Model_Rate_Result|Mage_Shipping_Model_Rate_Result_Error|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $result of method Mage_Shipping_Model_Rate_Result::append() expects Mage_Shipping_Model_Rate_Result|Mage_Shipping_Model_Rate_Result_Abstract, Mage_Shipping_Model_Rate_Result|Mage_Shipping_Model_Rate_Result_Error|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $lastmod of method Mage_Sitemap_Model_Sitemap::getSitemapRow() expects string|null, string|false given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Sitemap/Model/Sitemap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be float by placeholder #1 ("%%.1f"), string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sitemap/Model/Sitemap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Tag_Model_Resource_Popular_Collection::joinFields() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/All.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Tag_Model_Resource_Product_Collection::addCustomerFilter() expects array|int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Customer/Recent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Tag_Model_Resource_Tag_Collection::addCustomerFilter() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Customer/Tags.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Tag_Model_Resource_Product_Collection::addCustomerFilter() expects array|int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Customer/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Tag_Model_Resource_Popular_Collection::joinFields() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Popular.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Tag_Model_Resource_Tag_Collection::addProductFilter() expects int, true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Product/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Tag_Model_Resource_Tag_Collection::addStoreFilter() expects array|int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Product/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Product/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tagId of method Mage_Tag_Model_Resource_Product_Collection::addTagFilter() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Product/Result.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Tag_Model_Tag::saveRelation() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $customerId of method Mage_Tag_Model_Tag::saveRelation() expects int, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $flag of method Varien_Data_Collection<Mage_Customer_Model_Customer>::setFlag() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Customer/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Varien_Data_Collection_Db<Mage_Customer_Model_Customer>::_getConditionSql() expects array|int|string, array|int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Customer/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $flag of method Varien_Data_Collection<Mage_Catalog_Model_Product>::setFlag() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $flag of method Varien_Data_Collection<Mage_Core_Model_Abstract>::setFlag() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Tag/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Varien_Data_Collection_Db<Mage_Core_Model_Abstract>::_getConditionSql() expects array|int|string, array|int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Tag/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $tagId of method Mage_Tag_Model_Tag_Relation::loadByTagCustomer() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Tag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $customerId of method Mage_Tag_Model_Tag_Relation::loadByTagCustomer() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/controllers/CustomerController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $customerId of method Mage_Tag_Model_Tag::saveRelation() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $storeId of method Mage_Tag_Model_Tag::saveRelation() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $storeId of method Mage_Core_Model_Locale::emulate() expects int, bool|int|Mage_Core_Model_Store|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $store of method Mage_Tax_Helper_Data::_calculatePriceInclTax() expects Mage_Core_Model_Store, Mage_Core_Model_Store|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #7 $store of method Mage_Tax_Helper_Data::getPrice() expects Mage_Core_Model_Store|null, bool|int|Mage_Core_Model_Store|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_Tax_Model_Calculation::getDefaultCustomerTaxClass() expects Mage_Core_Model_Store|null, bool|int|Mage_Core_Model_Store|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Calculation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $rateId of method Mage_Tax_Model_Calculation_Rate_Title::deleteByRateId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Calculation/Rate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $rateId of method Mage_Tax_Model_Resource_Calculation_Rate::isInRule() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Calculation/Rate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $rateId of method Mage_Tax_Model_Resource_Calculation_Rate_Title_Collection::loadByRateId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Calculation/Rate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ruleId of method Mage_Tax_Model_Calculation::deleteByRuleId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Calculation/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Varien_Db_Select::where() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Resource/Report/Tax/Createdat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cond of method Zend_Db_Select::having() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Resource/Report/Tax/Createdat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $countryId of method Mage_Directory_Model_Resource_Region_Collection::addCountryFilter() expects array|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/System/Config/Source/Tax/Region.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strip_tags expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, object|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract/Backend/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Abstract::load() expects int|string|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $num of function floor expects float|int, array|float|int|string|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $response of method Mage_Usa_Model_Shipping_Carrier_Dhl::_parseXmlResponse() expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function mb_convert_encoding expects array|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function array_key_exists expects array, array|bool given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $response of method Mage_Usa_Model_Shipping_Carrier_Abstract::_setCachedQuotes() expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $response of method Mage_Usa_Model_Shipping_Carrier_Dhl::_parseXmlTrackingResponse() expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $value of function curl_setopt expects array|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $value of function curl_setopt expects non-empty-string, string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shippingDays of method Mage_Usa_Model_Shipping_Carrier_Dhl_Abstract::_determineShippingDay() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of class SimpleXMLElement constructor expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of class Varien_Simplexml_Element constructor expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $doc of method Mage_Usa_Model_Shipping_Carrier_Dhl_International::getDhlProducts() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Abstract::load() expects int|string|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $request of method Mage_Usa_Model_Shipping_Carrier_Dhl_International::_getQuotesFromServer() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $requestParams of method Mage_Usa_Model_Shipping_Carrier_Abstract::_getCachedQuotes() expects array|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $requestParams of method Mage_Usa_Model_Shipping_Carrier_Abstract::_setCachedQuotes() expects array|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $response of method Mage_Usa_Model_Shipping_Carrier_Dhl_International::_parseResponse() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function mb_convert_encoding expects array|string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $code of method Mage_Usa_Model_Shipping_Carrier_Dhl_International::getCode() expects string, string|true given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $rawRequest of method Mage_Usa_Model_Shipping_Carrier_Dhl_International::_shipmentDetails() expects Varien_Object, Varien_Object|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $response of method Mage_Usa_Model_Shipping_Carrier_Abstract::_setCachedQuotes() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $sourceWeightMeasure of method Mage_Usa_Helper_Data::convertMeasureWeight() expects \'kilogram\'|\'ounce\'|\'pounds\', array|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, string|false given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $toDimensionMeasure of method Mage_Usa_Helper_Data::convertMeasureDimension() expects \'centimeter\'|\'inch\'|\'meter\', array|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $toWeightMeasure of method Mage_Usa_Helper_Data::convertMeasureWeight() expects \'kilogram\'|\'ounce\'|\'pounds\', array|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Label/Pdf/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $x of method Zend_Pdf_Canvas_Abstract::drawText() expects float, float|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Label/Pdf/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $yAxis of method Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page::drawLines() expects int, float|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Label/Pdf/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $yAxis of method Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page::drawLines() expects int, float|int given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Label/Pdf/PageBuilder.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_diff_key expects array, array|bool given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $cost of method Mage_Shipping_Model_Carrier_Abstract::getMethodPrice() expects float, float|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $requestParams of method Mage_Usa_Model_Shipping_Carrier_Abstract::_getCachedQuotes() expects array|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $requestParams of method Mage_Usa_Model_Shipping_Carrier_Abstract::_setCachedQuotes() expects array|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $response of method Mage_Usa_Model_Shipping_Carrier_Abstract::_setCachedQuotes() expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, string|false given.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $value of function curl_setopt expects array|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $value of function curl_setopt expects non-empty-string, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $clientId of method Mage_Usa_Model_Shipping_Carrier_UpsAuth::getAccessToken() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of class SimpleXMLElement constructor expects string, string|true given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Core_Model_Abstract::load() expects int|string|null, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $json of function json_decode expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $rateResponse of method Mage_Usa_Model_Shipping_Carrier_Ups::_parseRestResponse() expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $rowRequest of method Mage_Usa_Model_Shipping_Carrier_Ups::setQuoteRequestData() expects Varien_Object, Varien_Object|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $clientSecret of method Mage_Usa_Model_Shipping_Carrier_UpsAuth::getAccessToken() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $code of method Mage_Usa_Model_Shipping_Carrier_Ups::getCode() expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $jsonResponse of method Mage_Usa_Model_Shipping_Carrier_Ups::_parseRestTrackingResponse() expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $response of method Mage_Usa_Model_Shipping_Carrier_Abstract::_setCachedQuotes() expects string, string|true given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method SimpleXMLElement::addChild() expects string|null, string|false given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $xmlResponse of method Mage_Usa_Model_Shipping_Carrier_Ups::_parseXmlTrackingResponse() expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $value of function curl_setopt expects array|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups/Source/OriginShipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups/Source/Unitofmeasure.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $json of function json_decode expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/UpsAuth.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $value of function curl_setopt expects non-empty-string, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/UpsAuth.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $json of function json_decode expects string, string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/UspsAuth.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $specificLifetime of method Zend_Cache_Core::save() expects int|false|null, float|int<1, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/UspsAuth.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function ucfirst expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/sql/usa_setup/upgrade-1.6.0.2-1.6.0.3.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $shipping of method Mage_Weee_Model_Tax::getProductWeeeAttributes() expects Varien_Object|null, Varien_Object|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $billing of method Mage_Weee_Model_Tax::getProductWeeeAttributes() expects Varien_Object|null, Varien_Object|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #5 $calculateTaxes of method Mage_Weee_Helper_Data::getProductWeeeAttributes() expects bool, bool|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Varien_Db_Select::where() expects array|float|int|string|Zend_Db_Expr|Zend_Db_Select|null, int|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Resource/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productId of method Mage_Weee_Model_Resource_Tax::getProductDiscountPercent() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_unshift expects array, array|float|int|string|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Block/Adminhtml/Widget/Instance/Edit/Chooser/Block.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, object|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Block/Adminhtml/Widget/Options.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Model/Widget/Instance.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, array|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/controllers/Adminhtml/Widget/InstanceController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $packageTheme of method Mage_Widget_Model_Widget_Instance::setPackageTheme() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/controllers/Adminhtml/Widget/InstanceController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $widgetInstance of method Mage_Widget_Adminhtml_Widget_InstanceController::_validatePostData() expects Mage_Widget_Model_Widget_Instance, Mage_Widget_Model_Widget_Instance|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/controllers/Adminhtml/Widget/InstanceController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productType of method Mage_Catalog_Block_Product_Abstract::_preparePriceRenderer() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productType of method Mage_Wishlist_Block_Customer_Wishlist::getOptionsRenderCfg() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Block/Customer/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $productType of method Mage_Wishlist_Block_Customer_Wishlist_Item_Options::getOptionsRenderCfg() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Block/Customer/Wishlist/Item/Options.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Wishlist_Model_Wishlist::isOwner() expects int, int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Controller/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $wishlistId of method Mage_Wishlist_Helper_Data::getListUrl() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Controller/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(mixed): int<0, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Controller/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item of method Mage_Wishlist_Helper_Data::_getUrlStore() expects Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item, Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customerId of method Mage_Wishlist_Model_Resource_Wishlist_Collection::filterByCustomerId() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Resource/Wishlist/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store of method Mage_Wishlist_Model_Wishlist::setStore() expects Mage_Core_Model_Store, Mage_Core_Model_Store|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $wishlistId of method Mage_Wishlist_Helper_Data::getListUrl() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/SharedController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/creditmemo/create/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/creditmemo/view/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/invoice/create/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/invoice/view/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/order/view/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/shipment/create/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/shipment/view/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $websiteId of method Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Websites::hasWebsite() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/edit/websites.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $productType of method Mage_Catalog_Helper_Product::getDefaultProductValue() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/tab/inventory.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Helper_Abstract::quoteEscape() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/currencysymbol/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/downloadable/sales/items/column/downloadable/creditmemo/name.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/downloadable/sales/items/column/downloadable/invoice/name.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/downloadable/sales/items/column/downloadable/name.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/payment/form/checkmo.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/payment/info/checkmo.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/payment/info/pdf/checkmo.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $element of method Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Global::getElementBackendConfig() expects Varien_Data_Form_Element_Abstract, Varien_Data_Form_Element_Abstract|false given.',
    'count' => 9,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/paypal/system/config/fieldset/global.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/items/column/name.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sku of method Mage_Catalog_Helper_Data::splitSku() expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/create/items/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $shippingMethod of method Mage_Usa_Helper_Data::displayGirthValue() expects string, string|Varien_Object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/shipment/packaging/popup.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/shipment/view/tracking.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $address of method Mage_Adminhtml_Block_Sales_Order_View_Info::getAddressEditLink() expects Mage_Sales_Model_Order_Address, Mage_Sales_Model_Order_Address|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/view/info.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/recurring/profile/view/info.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/system/shipping/ups.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row of method Mage_Adminhtml_Block_Widget_Grid_Column::getRowField() expects Varien_Object, string|Varien_Object given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/widget/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::stripTags() expects string, string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute of method Mage_Catalog_Block_Product_Compare_List::getProductAttributeValue() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributeHtml of method Mage_Catalog_Helper_Output::productAttribute() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributeHtml of method Mage_Catalog_Helper_Output::productAttribute() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/compare/sidebar.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::stripTags() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributeHtml of method Mage_Catalog_Helper_Output::productAttribute() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/list/related.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/new.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributeHtml of method Mage_Catalog_Helper_Output::productAttribute() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/view/addto.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/widget/new/content/new_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/widget/new/content/new_list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/cart/crosssell.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/cart/item/default.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Wishlist_Helper_Data::getMoveFromCartUrl() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/cart/item/default.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/multishipping/agreements.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/onepage/agreements.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/success.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/success.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/total/nominal.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $date of method Mage_Customer_Block_Widget_Dob::setDate() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/customer/form/edit.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/customer/form/register.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/downloadable/checkout/cart/item/default.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Wishlist_Helper_Data::getMoveFromCartUrl() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/downloadable/checkout/cart/item/default.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/page/print.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/payment/form/checkmo.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/payment/info/checkmo.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/persistent/customer/form/register.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/home_product_compared.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/home_product_viewed.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/widget/compared/content/compared_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/widget/compared/content/compared_list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/widget/viewed/content/viewed_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/widget/viewed/content/viewed_list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/review/customer/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Review_Block_Product_View_List::getReviewUrl() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/review/product/view/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/review/product/view/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/review/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/sales/order/items/renderer/default.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $creditmemo of method Mage_Sales_Block_Order_Print_Creditmemo::getTotalsHtml() expects Mage_Sales_Model_Order_Creditmemo, Mage_Sales_Model_Order_Creditmemo|Mage_Sales_Model_Order_Invoice given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/sales/order/print/creditmemo.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/sales/recurring/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/sales/recurring/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Core_Block_Template::getObjectData() expects Varien_Object, array<Mage_Core_Block_Abstract>|Mage_Core_Block_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/sales/recurring/profile/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/sales/recurring/profile/view/info.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/tag/customer/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Helper_Abstract::quoteEscape() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/wishlist/email/rss.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/wishlist/item/column/remove.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/wishlist/item/configure/addto.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/wishlist/shared.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/wishlist/sidebar.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $wishlistId of method Mage_Wishlist_Helper_Data::getRssUrl() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/wishlist/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, array<Mage_Core_Block_Abstract>|Mage_Core_Block_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/layer/state.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::stripTags() expects string, string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute of method Mage_Catalog_Block_Product_Compare_List::getProductAttributeValue() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributeHtml of method Mage_Catalog_Helper_Output::productAttribute() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributeHtml of method Mage_Catalog_Helper_Output::productAttribute() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/compare/sidebar.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::stripTags() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributeHtml of method Mage_Catalog_Helper_Output::productAttribute() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/list/related.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributeHtml of method Mage_Catalog_Helper_Output::productAttribute() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/view/addto.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributeHtml of method Mage_Catalog_Helper_Output::productAttribute() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/view/sharing.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object_or_class of function method_exists expects object|string, array<Mage_Core_Block_Abstract>|Mage_Core_Block_Abstract|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/view/type/options/configurable.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/widget/new/content/new_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/checkout/cart/crosssell.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/checkout/cart/item/default.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $itemId of method Mage_Wishlist_Helper_Data::getMoveFromCartUrl() expects int, int|string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/checkout/cart/item/default.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/configurableswatches/catalog/product/list/swatches.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/configurableswatches/catalog/product/view/type/options/configurable/swatches.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $date of method Mage_Customer_Block_Widget_Dob::setDate() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/customer/form/edit.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/email/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::stripTags() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/email/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attributeHtml of method Mage_Catalog_Helper_Output::productAttribute() expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/email/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/page/print.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/persistent/customer/form/register.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/reports/widget/compared/content/compared_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/reports/widget/viewed/content/viewed_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/review/customer/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Review_Block_Product_View_List::getReviewUrl() expects int, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/review/product/view/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/review/product/view/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/review/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function nl2br expects string, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/sales/order/items/renderer/default.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/sales/recurring/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 ...$values of function sprintf expects bool|float|int|string|null, array<string|null>|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/sales/recurring/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of method Mage_Core_Block_Template::getObjectData() expects Varien_Object, array<Mage_Core_Block_Abstract>|Mage_Core_Block_Abstract given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/sales/recurring/profile/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/wishlist/shared.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Core_Block_Abstract::escapeHtml() expects array<string>|string|null, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/wishlist/sidebar.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $wishlistId of method Mage_Wishlist_Helper_Data::getRssUrl() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/wishlist/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strtolower expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../cron.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function array_map expects array, list<string>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../cron.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of function unserialize expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../errors/processor.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_match expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../errors/processor.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $json of function json_decode expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../get.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function fclose expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../get.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function flock expects resource, resource|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../get.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function ftruncate expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../get.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function fwrite expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../get.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $data of function fwrite expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../get.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $haystack of function in_array expects array, list<string>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_split expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Archive_Helper_File::write() expects string, bool|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Archive/Bz.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Archive_Helper_File::write() expects string, bool|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Archive/Gz.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function fclose expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function feof expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function fread expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function fwrite expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $length of function fread expects int<1, max>, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $bz of function bzclose expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File/Bz.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $bz of function bzread expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File/Bz.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $bz of function bzwrite expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File/Bz.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function gzclose expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File/Gz.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function gzeof expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File/Gz.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function gzread expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File/Gz.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function gzwrite expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Helper/File/Gz.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of method Mage_Archive_Helper_File::write() expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $file of method Mage_Archive_Tar::_setCurrentFile() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $group_id of function posix_getgrgid expects int, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of method Mage_Archive_Tar::_setCurrentPath() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, bool|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function substr expects string, bool|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function substr expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function substr_replace expects array|string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function substr_replace expects array|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $user_id of function posix_getpwuid expects int, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $length of function fread expects int<1, max>, int given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function unpack expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function unpack expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%011o"), int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, float|int given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, float|int<1, max> given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of function is_writable expects string, array|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Mage_Cache_Backend_File::_tagFile() expects string, resource|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $specificLifetime of method Zend_Cache_Backend::getLifetime() expects int|false, bool|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $offset of function substr expects int, float|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $length of function substr expects int|null, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $fld of method Mage_DB_Mysqli::escapeFieldName() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/DB/Mysqli.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $message of class Mage_DB_Exception constructor expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/DB/Mysqli.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function implode expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/DB/Mysqli.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/HTTP/Client/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/HTTP/Client/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strlen expects string, string|null given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/HTTP/Client/Socket.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/HTTP/Client/Socket.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/HTTP/Client/Socket.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dir_handle of function closedir expects resource|null, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Dirs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dir_handle of function readdir expects resource|null, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Dirs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_sum expects an array of values castable to number, list<string> given.',
    'count' => 3,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_cdup expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_chdir expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 3,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_chmod expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_delete expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_fput expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_get expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_login expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_mkdir expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_nlist expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_pasv expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_put expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_raw expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_rawlist expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $mode of function ftp_fput expects 1|2, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strtoupper expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Magento_Db_Adapter_Pdo_Mysql::_convertFloat() expects float, array|float|int|string|Zend_Db_Expr|Zend_Db_Select|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $key of static method Magento_Profiler::fetch() expects \'avg\'|\'count\'|\'emalloc\'|\'realmem\'|\'sum\', (int|string) given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Profiler/OutputAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $key of static method Magento_Profiler::fetch() expects \'avg\'|\'count\'|\'emalloc\'|\'realmem\'|\'sum\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Profiler/OutputAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $specificLifetime of method Zend_Cache_Backend::getLifetime() expects int|false, bool|int|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Backend/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $chunks of method Varien_Cache_Backend_Memcached::_cleanTheMess() expects int, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Backend/Memcached.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Varien_Cache_Core::_cleanTheMess() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Core.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id of method Varien_Cache_Core::_getChunkId() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Core.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tags of method Varien_Cache_Core::_tags() expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Core.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $specificLifetime of method Zend_Cache_Core::save() expects int|false|null, bool|int given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Cache/Core.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $container of method Varien_Convert_Action_Abstract::setContainer() expects Varien_Convert_Container_Abstract, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Convert/Action/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_split expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Convert/Adapter/Http/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function fread expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Convert/Adapter/Std.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $item of method Varien_Convert_Container_Collection::addItem() expects Varien_Convert_Container_Interface, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Convert/Container/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Convert/Parser/Xml/Excel.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Convert/Parser/Xml/Excel.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_replace expects array<string>|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Convert/Parser/Xml/Excel.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $container of method Varien_Convert_Profile_Collection::addContainer() expects Varien_Convert_Container_Interface, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Convert/Profile/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, array{T of Varien_Object, string}|(callable(): mixed)|non-falsy-string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_filter expects array, list<string>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $field of method Varien_Data_Collection_Db<T of Varien_Object>::_getMappedField() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $select of method Varien_Data_Collection_Db<T of Varien_Object>::_loadCache() expects Zend_Db_Select, string|Zend_Db_Select given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $str of function preg_quote expects string, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(mixed): array<int<0, max>, non-empty-string> given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition of method Varien_Data_Collection_Db<T of Varien_Object>::_translateCondition() expects array|int|string, array|int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $select of method Varien_Data_Collection_Db<T of Varien_Object>::_saveCache() expects Zend_Db_Select, string|Zend_Db_Select given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_match expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $callback of function usort expects callable(array, array): int, Closure(array, array): (int|null) given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Filesystem.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $element of method Varien_Data_Form_Abstract::addElement() expects Varien_Data_Form_Element_Abstract, object given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $after of method Varien_Data_Form_Abstract::addElement() expects string|false|null, bool|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $after of method Varien_Data_Form_Element_Collection::add() expects string|false, string|false|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, int<min, -1>|int<1, max>|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_match expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $index of method Varien_Data_Form_Element_Abstract::getEscapedValue() expects string|null, int<0, max> given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Multiline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $haystack of function in_array expects array, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Zend_Filter_NormalizedToLocalized::filter() expects string, array|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Filter/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of method Zend_Filter_NormalizedToLocalized::filter() expects string, array|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Filter/Datetime.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $parent of method Varien_Data_Tree_Node::setParent() expects Varien_Data_Tree_Node, Varien_Data_Tree_Node|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $parent of method Varien_Data_Tree_Node::setParent() expects Varien_Data_Tree_Node, Varien_Data_Tree_Node|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree/Node.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $parentNode of method Varien_Data_Tree::appendChild() expects Varien_Data_Tree_Node, Varien_Data_Tree_Node|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree/Node.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $timestamp of static method Carbon\\Carbon::createFromTimestamp() expects float|int|string, int|string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function preg_replace expects array<float|int|string>|string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function array_map expects (callable(int|string): mixed)|null, Closure(array|string|Zend_Db_Expr, bool=): string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $interval of method Varien_Db_Adapter_Pdo_Mysql::_getIntervalUnitSql() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $text of method Varien_Db_Adapter_Pdo_Mysql::_prepareQuotedSqlCondition() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%d"), float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $fieldName of method Varien_Db_Adapter_Pdo_Mysql::_prepareQuotedSqlCondition() expects string, array|string given.',
    'count' => 4,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $type of method Zend_Db_Adapter_Abstract::quoteInto() expects string|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%d"), float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
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
    'rawMessage' => 'Parameter #2 $subject of function preg_match expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Ddl/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $type of method Zend_Db_Select::where() expects int|null, int|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $params of method Varien_Db_Statement_Pdo_Mysql::_executeWithBinding() expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Statement/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function strpos expects string, int|list<mixed>|object|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Debug.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object of function spl_object_hash expects object, int|list<mixed>|object|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Debug.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function substr expects string, int|list<mixed>|object|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Debug.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$values of function sprintf expects bool|float|int|string|null, int|list<mixed>|object|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Debug.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 ...$values of function sprintf expects bool|float|int|string|null, int|list<mixed>|object|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Debug.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 ...$values of function sprintf expects bool|float|int|string|null, int|list<mixed>|object|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Debug.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func expects callable(): mixed, non-empty-array given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Event/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value of function count expects array|Countable, list<string>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Event/Observer/Cron.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function fclose expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $stream of function fgetcsv expects resource, resource|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value of method Zend_Controller_Response_Abstract::setHeader() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Transfer/Adapter/Http.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of function pathinfo expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Uploader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $code of class Exception constructor expects int, float|int|string|true given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Uploader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func expects callable(): mixed, non-empty-array|Closure given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, array{Varien_Object, mixed} given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $handle of function curl_errno expects CurlHandle, CurlHandle|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $handle of function curl_error expects CurlHandle, CurlHandle|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $handle of function curl_exec expects CurlHandle, CurlHandle|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $handle of function curl_getinfo expects CurlHandle, CurlHandle|resource given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $handle of function curl_setopt expects CurlHandle, CurlHandle|resource given.',
    'count' => 3,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $handle of function curl_setopt_array expects CurlHandle, CurlHandle|resource given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $haystack of function stripos expects string, bool|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $response_str of static method Zend_Http_Response::extractCode() expects string, bool|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $options of function curl_setopt_array expects array{10002: non-empty-string, 19913: bool, 42: bool, 84: int, 47: bool, 80: bool, 10023?: array<int, string>, 10015?: array|string}, array{10002: string, 19913: true, 42: mixed, 84: 2, 47: false, 80: bool, 10023?: array<mixed, mixed>}|array{10002: string, 19913: true, 42: mixed, 84: 2, 47: true, 80: false, 10015: string, 10023?: array<mixed, mixed>} given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_split expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $subject of function str_ireplace expects array<string>|string, bool|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func expects callable(): mixed, string given.',
    'count' => 3,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $callback of function call_user_func_array expects callable(): mixed, string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $character of function ord expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dst_image of function imagecopymerge expects GdImage, GdImage|resource given.',
    'count' => 7,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagecolorsforindex expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagecolorstotal expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagedestroy expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imageinterlace expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagepalettetotruecolor expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagerotate expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagesx expects GdImage, GdImage|resource given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $image of function imagesy expects GdImage, GdImage|resource given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $width of function imagecreate expects int<1, max>, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $width of function imagecreate expects int<1, max>, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $width of function imagecreatetruecolor expects int<1, max>, (float|int) given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $width of function imagecreatetruecolor expects int<1, max>, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $width of function imagecreatetruecolor expects int<1, max>, int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #10 $src_height of function imagecopyresampled expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $color of function imagecolortransparent expects int|null, int<0, max>|false given.',
    'count' => 3,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $height of function imagecreate expects int<1, max>, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $height of function imagecreate expects int<1, max>, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $height of function imagecreatetruecolor expects int<1, max>, (float|int) given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $height of function imagecreatetruecolor expects int<1, max>, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $height of function imagecreatetruecolor expects int<1, max>, int|string given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $red of function imagecolorallocate expects int<0, 255>, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $src_image of function imagecopy expects GdImage, GdImage|resource given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $src_image of function imagecopyresampled expects GdImage, GdImage|resource given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $dst_x of function imagecopyresampled expects int, float|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $green of function imagecolorallocate expects int<0, 255>, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $blue of function imagecolorallocate expects int<0, 255>, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $color of function imagefill expects int, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $dst_y of function imagecopyresampled expects int, float|int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $x2 of function imagefilledrectangle expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #5 $y2 of function imagefilledrectangle expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #6 $color of function imagefilledrectangle expects int, int<0, max>|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #7 $dst_width of function imagecopyresampled expects int, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #7 $dst_width of function imagecopyresampled expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #7 $src_width of function imagecopy expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #8 $dst_height of function imagecopyresampled expects int, float|int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #8 $dst_height of function imagecopyresampled expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #8 $src_height of function imagecopy expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #9 $src_width of function imagecopyresampled expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function trim expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $string of function explode expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $mode of method Varien_Io_File::_parsePermissions() expects int, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of function realpath expects string, resource|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $src of method Varien_Io_File::_checkSrcIsFile() expects string, resource|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $src of method Varien_Io_File::cp() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $timestamp of static method Carbon\\Carbon::createFromTimestamp() expects float|int|string, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $user_id of function posix_getpwuid expects int, int<0, max>|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $length of function fgets expects int<0, max>|null, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $to of function copy expects string, resource|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_chdir expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_chmod expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_close expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_delete expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_fget expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_fput expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_get expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_mkdir expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_nlist expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_put expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_pwd expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_rename expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $ftp of function ftp_rmdir expects FTP\\Connection, FTP\\Connection|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array of function array_keys expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key of method Varien_Object::getData() expects string, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of function strtolower expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function array_key_exists expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 ...$arrays of function array_intersect_key expects array, array|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $array of function array_key_exists expects array, array|object given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object/Mapper.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data of function simplexml_load_string expects string, true given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $text of method Varien_Simplexml_Config::processFileData() expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename of method Varien_Simplexml_Element::asNiceXml() expects string, int given.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Simplexml/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $subject of function preg_match_all expects string, string|false given.',
    'count' => 2,
    'path' => __DIR__ . '/../shell/abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $timestamp of static method Carbon\\Carbon::createFromTimestamp() expects float|int|string, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Base/CarbonTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $timestamp of function date expects int|null, int|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Base/CarbonTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $string of method Mage_Catalog_Helper_Product_Url::format() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Catalog/Helper/Product/UrlTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $path of static method Mage_Cms_Helper_Page::getConfigLabelFromConfigPath() expects \'web/default/cms…\'|\'web/default/cms_no…\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Cms/Helper/PageTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $scope of static method Mage_Cms_Helper_Page::getScopeInfoFromConfigScope() expects \'default\'|\'env\'|\'stores\'|\'websites\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Cms/Helper/PageTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $envStorage of method Mage_Core_Helper_EnvironmentConfigLoader::setEnvStore() expects array<string, int|string>, array<int|string, int> given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Helper/EnvironmentConfigLoaderTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $wantedPath of method Mage_Core_Helper_EnvironmentConfigLoader::hasPath() expects string, bool|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Helper/EnvironmentConfigLoaderTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $wantedStore of method Mage_Core_Helper_EnvironmentConfigLoader::getAsArray() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Helper/EnvironmentConfigLoaderTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $content of method Mage_Core_Helper_Purifier::purify() expects string, array|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Helper/PurifierTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $groupId of method Mage_Core_Model_App::getGroup() expects int|Mage_Core_Model_Store_Group|string|null, bool|int|Mage_Core_Model_Store_Group|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/AppTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $websiteId of method Mage_Core_Model_App::getWebsite() expects int|Mage_Core_Model_Website|string|true|null, bool|int|Mage_Core_Model_Website|string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/AppTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $backendObject of method Zend_Cache_Core::setBackend() expects Zend_Cache_Backend, Zend_Cache_Backend|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $expectedCount of static method PHPUnit\\Framework\\Assert::assertCount() expects int, int|string given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Reports/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $version1 of function version_compare expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Reports/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $periodType of method Mage_Reports_Helper_Data::prepareIntervalsCollection() expects \'day\'|\'month\'|\'year\', string given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Reports/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dateFrom of method Mage_Reports_Model_Resource_Report_Collection::setInterval() expects Zend_Date, string|Zend_Date|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Reports/Model/Resource/Report/CollectionTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $dateTo of method Mage_Reports_Model_Resource_Report_Collection::setInterval() expects Zend_Date, string|Zend_Date|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Reports/Model/Resource/Report/CollectionTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $version1 of function version_compare expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Rule/Model/AbstractTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $sourceDimensionMeasure of method Mage_Usa_Helper_Data::convertMeasureDimension() expects \'centimeter\'|\'inch\'|\'meter\', string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Usa/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $sourceWeightMeasure of method Mage_Usa_Helper_Data::convertMeasureWeight() expects \'kilogram\'|\'ounce\'|\'pounds\', string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Usa/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $toDimensionMeasure of method Mage_Usa_Helper_Data::convertMeasureDimension() expects \'centimeter\'|\'inch\'|\'meter\', string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Usa/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $toWeightMeasure of method Mage_Usa_Helper_Data::convertMeasureWeight() expects \'kilogram\'|\'ounce\'|\'pounds\', string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Usa/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $json of function json_decode expects string, string|false given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Usa/Model/Shipping/Carrier/UspsSecurityTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key of method Varien_Object::getData() expects string, string|null given.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Varien/ObjectTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
