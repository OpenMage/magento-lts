<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Model_Abstract::addError().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Mage.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 10,
	'path' => __DIR__ . '/app/Mage.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Right side of && is always false.',
	'identifier' => 'booleanAnd.rightAlwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/Mage.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Admin/Helper/Rules/Fallback.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Admin/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Admin/Model/Redirectpolicy.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method getClassName() on SimpleXMLElement|false.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Admin/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Admin/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Admin/Model/Roles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Admin_Model_Resource_Rules::update().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Admin/Model/Rules.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Admin/Model/Session.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Admin/Model/User.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/AdminNotification/Model/Resource/Inbox.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Api/Role/Grid/User.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Api/Tab/Rolesedit.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Api/Tab/Rolesedit.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Api_Model_Resource_Roles_User_Collection::setUserFilter().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Api/Tab/Userroles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Api/Tab/Userroles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Api/User/Edit/Tab/Roles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $collection of method Mage_Adminhtml_Block_Widget_Grid::setCollection() expects Varien_Data_Collection_Db, Varien_Data_Collection given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Cache/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Category/Checkboxes/Tree.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Category/Edit/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Category/Tab/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Category/Tree.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method addAttributeToSelect() on Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Category/Widget/Chooser.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Form/Renderer/Fieldset/Element.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Product/Composite/Fieldset/Options.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Categories.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Crosssell.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Related.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Super/Config/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 2 parameters, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Super/Config/Simple.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Super/Group.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Model_Product_Type_Configurable::canUseAttribute() invoked with 2 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Super/Settings.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Upsell.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Catalog/Product/Widget/Chooser.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Customer/Edit/Renderer/Attribute/Group.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Customer/Edit/Renderer/Region.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Mage_Core_Model_Factory of property Mage_Adminhtml_Block_Customer_Edit_Renderer_Region::$_factory is not the same as PHPDoc type Mage_Core_Model_Factory|null of overridden property Mage_Core_Block_Abstract::$_factory.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Customer/Edit/Renderer/Region.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Account.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Eav_Model_Entity_Attribute is not subtype of type Mage_Customer_Model_Attribute.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Account.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Addresses.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Eav_Model_Entity_Attribute is not subtype of type Mage_Customer_Model_Attribute.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Addresses.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist::$_defaultSort is not the same as PHPDoc type mixed of overridden property Mage_Adminhtml_Block_Widget_Grid::$_defaultSort.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Wishlist.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Customer/Grid/Renderer/Multiaction.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Customer/Online/Grid/Renderer/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 14,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Page/Menu.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to method getCollection() on an unknown class Mage_Permissions_Model_Users.',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Permissions/Grid/User.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Permissions/Role/Grid/User.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Permissions/Tab/Rolesedit.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Permissions/Tab/Rolesedit.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Admin_Model_Resource_Roles_User_Collection::setUserFilter().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Permissions/Tab/Userroles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Permissions/Tab/Userroles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Permissions/User/Edit/Tab/Roles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to method getCollection() on an unknown class Mage_Permissions_Model_Roles.',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Permissions/Usernroles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to method getCollection() on an unknown class Mage_Permissions_Model_Users.',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Permissions/Usernroles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Promo/Quote/Edit/Tab/Coupons/Grid/Column/Renderer/Used.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Promo/Widget/Chooser/Daterange.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Promo/Widget/Chooser/Sku.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Varien_Object is not subtype of type Varien_Data_Form_Element_Abstract|null.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Filter/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $collection of method Mage_Adminhtml_Block_Widget_Grid::setCollection() expects Varien_Data_Collection_Db, Mage_Reports_Model_Resource_Report_Collection given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $grid of method Mage_Reports_Model_Totals::countTotals() expects Mage_Adminhtml_Block_Report_Product_Grid, $this(Mage_Adminhtml_Block_Report_Grid) given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Grid/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Adminhtml_Block_Report_Grid_Abstract::getCollection() should return Mage_Core_Model_Resource_Db_Collection_Abstract|Mage_Reports_Model_Grouped_Collection but returns Varien_Data_Collection_Db|null.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Grid/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $collection of method Mage_Adminhtml_Block_Widget_Grid::setCollection() expects Varien_Data_Collection_Db, Mage_Reports_Model_Grouped_Collection given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Grid/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Reports_Model_Totals::countTotals() invoked with 1 parameter, 3 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Product/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type int of property Mage_Adminhtml_Block_Report_Product_Sold_Grid::$_subReportSize is not the same as PHPDoc type mixed of overridden property Mage_Adminhtml_Block_Report_Grid::$_subReportSize.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Product/Sold/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Adminhtml_Block_Report_Product_Viewed_Grid::$_resourceCollectionName is not the same as PHPDoc type mixed of overridden property Mage_Adminhtml_Block_Report_Grid_Abstract::$_resourceCollectionName.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Product/Viewed/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $collection of method Mage_Adminhtml_Block_Widget_Grid::setCollection() expects Varien_Data_Collection_Db, Varien_Data_Collection given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Refresh/Statistics/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Report/Shopcart/Abandoned/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Items/Column/Default.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Form/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Form/Address.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Search/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Shipping/Method/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Shipping_Model_Carrier_Abstract::isGirthAllowed().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Order/Shipment/Packaging.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Order/Shipment/Packaging.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Order/Status/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Info.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $array of function implode expects array<string>, array<int, array<string>|string|null> given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Info.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Tab/History.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Reorder/Renderer/Action.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $collection of method Mage_Adminhtml_Block_Widget_Grid::setCollection() expects Varien_Data_Collection_Db, Varien_Data_Collection given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Sales/Transactions/Detail/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Store/Switcher.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Varien_Data_Form_Element_Abstract of property Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset_Element::$_element is not the same as PHPDoc type mixed of overridden property Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element::$_element.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Store/Switcher/Form/Renderer/Fieldset/Element.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method initForm() on Mage_Core_Block_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Edit.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Edit.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Core_Model_Config_Element is not subtype of type *NEVER*.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $array (array{non-empty-string, non-empty-string}) of array_values is already a list, call has no effect.',
	'identifier' => 'arrayValues.list',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Form/Field/Array/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Form/Field/Csp/Hosts.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Form/Field/Image.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Form/Fieldset.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Form/Fieldset/Modules/DisableOutput.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Form/Fieldset/Order/Statuses.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Config/Tabs.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Core_Model_Email_Template::getProcessedTemplate() invoked with 2 parameters, 0-1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Email/Template/Preview.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Varien_Data_Form is not subtype of type Varien_Data_Form_Element_Fieldset.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/System/Store/Edit/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Tag/Assigned/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Tag_Model_Resource_Tag_Collection::addAttributeToFilter().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Tag/Grid/All.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Binary operation "*" between string and 1 results in an error.',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Tax/Rate/Grid/Renderer/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Urlrewrite/Category/Tree.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Breadcrumbs.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Container.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 2 parameters, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Form/Container.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Date.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Datetime.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Select.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Theme.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Action.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Country.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Options.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $array of function implode expects array<string>, list<array<string>|string|null> given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Options.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Store.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Theme.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Massaction/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Grid/Serializer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Adminhtml_Block_Widget_Tab_Interface::getSkipGenerateContent().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Tabs.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Adminhtml_Block_Widget_Tab_Interface::getTabId().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Tabs.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Adminhtml_Block_Widget_Tab_Interface::toHtml().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Tabs.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Block/Widget/Tabs.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Controller/Action.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Controller/Report/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Controller/Sales/Creditmemo.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Controller/Sales/Invoice.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Controller/Sales/Shipment.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Helper/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Helper/Sales.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 6,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $cache of method Varien_Simplexml_Config::setCache() expects Varien_Simplexml_Config_Cache_Abstract, Zend_Cache_Core given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property Mage_Adminhtml_Model_Config::$_config (Mage_Core_Model_Config_Base) does not accept Varien_Simplexml_Config.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Adminhtml_Model_Config_Data::__().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Config/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Config/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $field of method Mage_Adminhtml_Model_Config_Data::_isValidField() expects Mage_Core_Model_Config_Element, Varien_Simplexml_Element|false given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Config/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Varien_Simplexml_Element of property Mage_Adminhtml_Model_LayoutUpdate_Validator::$_value is not the same as PHPDoc type mixed of overridden property Mage_Core_Helper_Validate_Abstract::$_value.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/LayoutUpdate/Validator.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 14,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $billingAddress might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Sales_Model_Order::createFromQuoteAddress().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Sales/Order/Random.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Sales_Model_Order::validate().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Sales/Order/Random.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Sales_Model_Quote::addCatalogProduct().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Sales/Order/Random.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Admin/Custom.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Admin/Usecustom.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Catalog/Inventory/Managestock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Design/Package.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Encrypted.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/System/Config/Backend/File.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Serialized.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/System/Config/Source/Admin/Page.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Varien_Data_Collection::toOptionArray() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/System/Config/Source/Country.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/System/Store.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/Model/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Api/UserController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/CacheController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Catalog/CategoryController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Catalog/Product/Action/AttributeController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Catalog/Product/AttributeController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Admin_Model_Session is not subtype of type Mage_Adminhtml_Model_Session.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Catalog/Product/AttributeController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Model_Entity_Attribute is not subtype of type Mage_Catalog_Model_Resource_Eav_Attribute.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Catalog/Product/AttributeController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Ternary operator condition is always true.',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Catalog/Product/SetController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Catalog/ProductController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Catalog/SearchController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Checkout/AgreementController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Cms/BlockController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Cms/PageController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Customer/GroupController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $content of method Zend_Controller_Response_Abstract::setBody() expects string, int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Customer/System/Config/ValidatevatController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/CustomerController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/IndexController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/JsonController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Newsletter/QueueController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Newsletter/TemplateController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Permissions/BlockController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Permissions/UserController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Permissions/VariableController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Promo/CatalogController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 6,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Promo/QuoteController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method aggregate() on Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Report/StatisticsController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Report/StatisticsController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Sales/Order/CreateController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Adminhtml_Model_Sales_Order_Create::moveQuoteItem() invoked with 2 parameters, 3 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Sales/Order/CreateController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Sales_Model_Order_Creditmemo::void().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Sales/Order/CreditmemoController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 8,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Sales/Order/CreditmemoController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Right side of && is always true.',
	'identifier' => 'booleanAnd.rightAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Sales/Order/CreditmemoController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 11,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Sales/Order/InvoiceController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 16,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always false.',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(array|bool|float|int|resource|string|null, int=): int given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Sales/OrderController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Dead catch - Exception is never thrown in the try block.',
	'identifier' => 'catch.neverThrown',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Sales/Recurring/ProfileController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/SitemapController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/System/CacheController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/System/Config/System/StorageController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/System/ConfigController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/System/Convert/GuiController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/System/Convert/ProfileController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Model_Convert_Adapter_Product is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/System/Convert/ProfileController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Directory_Model_Currency_Import_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/System/CurrencyController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/System/DesignController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/System/StoreController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/TagController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Tax/Class/CustomerController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Tax/Class/ProductController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Tax/RateController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Directory_Model_Country::loadByCode() invoked with 2 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Tax/RateController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Tax/RuleController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Adminhtml_Model_Session is not subtype of type Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Tax/RuleController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $ruleModel of method Mage_Adminhtml_Tax_RuleController::_isValidRuleRequest() expects Mage_Tax_Model_Calculation_Rule, Mage_Core_Model_Abstract given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Adminhtml/controllers/Tax/RuleController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Mage_Api_Model_Acl_Role_Registry of property Mage_Api_Model_Acl::$_roleRegistry is not the same as PHPDoc type Zend_Acl_Role_Registry of overridden property Zend_Acl::$_roleRegistry.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Acl.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $resource of method Mage_Api_Model_Config::loadAclResources() expects Mage_Core_Model_Config_Element|null, Varien_Simplexml_Element given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #4 $lifeTime of method Mage_Core_Model_App::saveCache() expects int|false|null, bool given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method getClassName() on bool|SimpleXMLElement.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Core_Model_Resource_Db_Abstract::load() invoked with 1 parameter, 2-3 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Resource/Roles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Api_Model_Resource_Roles::update().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Roles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Roles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Api_Model_Resource_Rules::update().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Rules.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Server.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Api_Model_Server_Adapter_Interface is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Server.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $code of class SoapFault constructor expects array|string|null, int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Server/Adapter/Soap.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Server/Handler/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Server/Wsi/Handler.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method SimpleXMLElement::extendChild().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $source of static method Mage_Api_Model_Wsdl_Config_Element::_getChildren() expects Varien_Simplexml_Element, SimpleXMLElement given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $collection of method Mage_Adminhtml_Block_Widget_Grid::setCollection() expects Varien_Data_Collection_Db, Varien_Data_Collection given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Block/Adminhtml/Attribute/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Block/Adminhtml/Permissions/User/Edit/Tab/Roles.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Block/Adminhtml/Roles/Tab/Users.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $adapters might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Acl.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Acl/Filter/Attribute/ResourcePermission.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Api2_Model_Resource is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Acl/Filter/Attribute/ResourcePermission.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $role of method Zend_Acl::hasRole() expects string|Zend_Acl_Role_Interface, int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Acl/Global.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $role of method Zend_Acl::isAllowed() expects string|Zend_Acl_Role_Interface|null, int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Acl/Global.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Acl/Global/Rule/ResourcePermission.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 8,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Acl/Global/Rule/Tree.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $operationName might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Acl/Global/Rule/Tree.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Api2_Model_Auth_User_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Auth.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Api2_Model_Auth_User_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Auth/User.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Api2_Model_Config::getResourceGroup() should return bool|Mage_Core_Model_Config_Element but returns Varien_Simplexml_Element.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $cache of method Varien_Simplexml_Config::setCache() expects Varien_Simplexml_Config_Cache_Abstract, Zend_Cache_Core given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Dispatcher.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Api2_Model_Resource is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Dispatcher.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $lowerOrEqualsTo of method Mage_Api2_Model_Config::getResourceLastVersion() expects int|null, bool|string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Dispatcher.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Api2_Model_Config::getMainRoute().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Multicall.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Multicall.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Api2_Model_Request_Interpreter_Interface is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Request.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Request/Interpreter.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Data_Collection_Db::addAttributeToFilter().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Core_Model_Resource_Db_Abstract is not subtype of type Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $renderer of method Mage_Api2_Model_Resource::setRenderer() expects Mage_Api2_Model_Renderer_Interface, Mage_Core_Model_Abstract|false given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $code of method Mage_Api2_Model_Response::addMessage() expects string, int given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Resource/Validator/Eav.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Resource/Validator/Eav.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Api2_Model_Renderer_Interface is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Api2/Model/Server.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Object::encrypt().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Authorizenet/Model/Directpost.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Authorizenet/Model/Directpost.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $amount of method Mage_Authorizenet_Model_Directpost::_refund() expects string, float given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Authorizenet/Model/Directpost.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Payment_Model_Method_Abstract::generateRequestFromOrder().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Authorizenet/Model/Directpost/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Authorizenet/Model/Directpost/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Authorizenet/Model/Directpost/Request.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Authorizenet/Model/Directpost/Response.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Payment_Model_Method_Abstract::generateRequestFromOrder().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Authorizenet/controllers/Adminhtml/Authorizenet/Directpost/PaymentController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Authorizenet/controllers/Adminhtml/Authorizenet/Directpost/PaymentController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Authorizenet/controllers/Directpost/PaymentController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Authorizenet_Directpost_PaymentController::_getIframeBlock() should return Mage_Authorizenet_Block_Directpost_Iframe but returns Mage_Core_Block_Abstract|false.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Authorizenet/controllers/Directpost/PaymentController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Block/Adminhtml/Sales/Order/Items/Renderer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Block/Adminhtml/Sales/Order/View/Items/Renderer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Block/Catalog/Product/View/Type/Bundle.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Block/Catalog/Product/View/Type/Bundle/Option.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Catalog_Model_Product_Configuration_Item_Interface::getQty().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Helper/Catalog/Product/Configuration.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Helper/Catalog/Product/Configuration.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Bundle_Model_CatalogIndex_Data_Bundle::_addAttributeFilter().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/CatalogIndex/Data/Bundle.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Bundle_Model_CatalogIndex_Data_Bundle::_getLinkSelect().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/CatalogIndex/Data/Bundle.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Bundle_Model_CatalogIndex_Data_Bundle::$_haveChildren is not the same as PHPDoc type array|false of overridden property Mage_CatalogIndex_Model_Data_Simple::$_haveChildren.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/CatalogIndex/Data/Bundle.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Bundle_Model_Resource_Price_Index::reindexProduct().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Price/Index.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Bundle_Model_Product_Price::getPricesTierPrice().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Bundle_Model_Product_Price::getOptions() should return Mage_Bundle_Model_Resource_Option_Collection but returns array.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $selectionProduct of method Mage_Bundle_Model_Product_Price::getSelectionFinalTotalPrice() expects Mage_Catalog_Model_Product, Mage_Bundle_Model_Selection given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Left side of && is always true.',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Bundle_Model_Selection is not subtype of type Mage_Catalog_Model_Product.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(array|bool|float|int|resource|string|null, int=): int given.',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Resource/Bundle.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Resource/Indexer/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $selection of method Mage_Bundle_Model_Option::addSelection() expects Mage_Bundle_Model_Selection, Mage_Catalog_Model_Product given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Resource/Option/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Resource/Selection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Resource/Selection/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Bundle/Model/Sales/Order/Pdf/Items/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Captcha/Model/Config/Form/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Captcha/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Argument of an invalid type string supplied for foreach, only iterables are supported.',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Block/Navigation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method count() on string.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Block/Navigation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Block/Navigation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Block/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $productId might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Block/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Block/Product/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Block/Product/List/Toolbar.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Block/Product/View/Options/Type/File.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Block/Product/View/Type/Configurable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Block/Seo/Sitemap/Tree/Category.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $store might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Block/Widget/Link.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%d"), bool|string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Category.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #4 of function sprintf is expected to be int by placeholder #3 ("%%d"), bool given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Category.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #5 of function sprintf is expected to be int by placeholder #4 ("%%d"), bool given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Category.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Catalog_Helper_Category_Flat::$_indexerCode is not the same as PHPDoc type string|null of overridden property Mage_Catalog_Helper_Flat_Abstract::$_indexerCode.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Category/Flat.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Category/Url/Rewrite.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Varien_Filter_Template is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Entity_Attribute_Frontend_Abstract::getUrl().',
	'identifier' => 'method.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Ternary operator condition is always true.',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Helper_Product_Compare::getItemCollection() should return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item_Collection but returns Mage_Catalog_Model_Resource_Product_Compare_Item_Collection.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Product/Compare.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Catalog_Helper_Product_Flat::$_indexerCode is not the same as PHPDoc type string|null of overridden property Mage_Catalog_Helper_Flat_Abstract::$_indexerCode.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Product/Flat.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Product/Type/Composite.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Helper/Product/Url/Rewrite.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Model_Resource_Db_Collection_Abstract::setStoreId().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Core_Model_Website::getStoreIds() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Catalog_Model_Api_Resource::_checkAttributeAcl().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Api/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Api2/Product/Image/Rest.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Api2/Product/Image/Rest/Admin/V1.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Api2/Product/Image/Validator/Image.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Elseif condition is always false.',
	'identifier' => 'elseif.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Api2/Product/Rest.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always false.',
	'identifier' => 'if.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Api2/Product/Rest.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Model_Abstract::toOptionArray().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::formatDate().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Strict comparison using !== between mixed and 0 will always evaluate to true.',
	'identifier' => 'notIdentical.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Attribute/Backend/Customlayoutupdate.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Object::formatUrlKey().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Attribute/Backend/Urlkey/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method getRootCategoryId() on int|string.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Category/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Category/Attribute/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Category/Attribute/Backend/Image.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Category/Indexer/Flat.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Catalog_Model_Category_Indexer_Flat::$_matchedEntities is not the same as PHPDoc type mixed of overridden property Mage_Index_Model_Indexer_Abstract::$_matchedEntities.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Category/Indexer/Flat.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Category/Indexer/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Catalog_Model_Category_Indexer_Product::$_matchedEntities is not the same as PHPDoc type mixed of overridden property Mage_Index_Model_Indexer_Abstract::$_matchedEntities.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Category/Indexer/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Category/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Catalog_Model_Config::_init().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Model_Config::getAttributeSetId() invoked with 1 parameter, 2 required.',
	'identifier' => 'arguments.count',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method setStoreId() on Mage_Eav_Model_Entity_Collection|false.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property Mage_Catalog_Model_Convert_Adapter_Product::$_galleryBackendModel (Mage_Catalog_Model_Product_Attribute_Backend_Media) does not accept Mage_Eav_Model_Entity_Attribute_Backend_Abstract.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Catalog_Model_Resource_Product_Flat|Mage_Eav_Model_Entity_Abstract::setStore().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Convert/Parser/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 6,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Convert/Parser/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Model_Category::getParentDesignCategory() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Design.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property Mage_Catalog_Model_Entity_Product_Attribute_Design_Options_Container::$_configNodePath (Mage_Core_Model_Config_Element) does not accept string.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Entity/Product/Attribute/Design/Options/Container.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Helper_Category_Url_Rewrite_Interface is not subtype of type Mage_Core_Helper_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Factory.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Helper_Product_Url_Rewrite_Interface is not subtype of type Mage_Core_Helper_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Factory.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Model_Category_Url is not subtype of type bool|Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Factory.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Model_Product_Url is not subtype of type bool|Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Factory.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Indexer/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Catalog_Model_Indexer_Url::$_matchedEntities is not the same as PHPDoc type mixed of overridden property Mage_Index_Model_Indexer_Abstract::$_matchedEntities.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Indexer/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Layer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $attribute of method Mage_Catalog_Model_Layer::_filterFilterableAttributes() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract|false given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Layer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Layer/Filter/Attribute.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #3 $index of method Mage_Catalog_Model_Resource_Layer_Filter_Decimal::applyFilterToCollection() expects int, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Layer/Filter/Decimal.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Model_Resource_Category_Flat::move() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::addImage().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Model_Resource_Product_Collection is not subtype of type Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Api/V2.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $attributes of method Mage_Catalog_Model_Api_Resource::_isAllowedAttribute() expects array|null, stdClass|null given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Api/V2.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Attribute/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Attribute/Api/V2.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 6,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Msrp.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method getBackend() on bool|Mage_Catalog_Model_Resource_Attribute.',
	'identifier' => 'method.nonObject',
	'count' => 11,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Attribute/Media/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Attribute/Set/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $skeletonId of method Mage_Eav_Model_Entity_Attribute_Set::initFromSkeleton() expects int, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Attribute/Set/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Attribute/Tierprice/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Compare_Item::clean() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Compare/Item.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Catalog_Model_Product_Compare_Item::$_cacheTag is not the same as PHPDoc type array|bool|string of overridden property Mage_Core_Model_Abstract::$_cacheTag.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Compare/Item.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Catalog_Model_Product_Flat_Flag::$_flagCode is not the same as PHPDoc type string|null of overridden property Mage_Core_Model_Flag::$_flagCode.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Flat/Flag.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Catalog_Model_Resource_Product_Flat_Indexer::prepareFlatTables().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Flat/Indexer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Image.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $heigth of method Varien_Image::setWatermarkHeigth() expects int, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Image.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method isIndexable() on Mage_Eav_Model_Entity_Attribute_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Indexer/Eav.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Indexer/Eav.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Catalog_Model_Product_Indexer_Eav::$_matchedEntities is not the same as PHPDoc type mixed of overridden property Mage_Index_Model_Indexer_Abstract::$_matchedEntities.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Indexer/Eav.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Indexer/Flat.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Catalog_Model_Product_Indexer_Flat::$_matchedEntities is not the same as PHPDoc type mixed of overridden property Mage_Index_Model_Indexer_Abstract::$_matchedEntities.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Indexer/Flat.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Indexer/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Catalog_Model_Product_Indexer_Price::$_matchedEntities is not the same as PHPDoc type mixed of overridden property Mage_Index_Model_Indexer_Abstract::$_matchedEntities.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Indexer/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Option.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Model_Product_Option_Type_Default is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Option.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Option/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Catalog_Model_Product_Configuration_Item_Option_Interface::getId().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Catalog_Model_Product_Configuration_Item_Option_Interface::setValue().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Option/Type/Select.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Elseif condition is always true.',
	'identifier' => 'elseif.alwaysTrue',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Option/Type/Select.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Option/Value/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Entity_Collection_Abstract::getStoreId().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Status.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Type.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 6,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Model_Product_Type_Configurable::getProductByAttributes() should return Mage_Catalog_Model_Product|null but returns Varien_Object.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Model_Product_Configuration_Item_Option|Mage_Sales_Model_Quote_Item_Option is not subtype of type Mage_Sales_Model_Quote_Item_Option.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Model_Product_Configuration_Item_Option|Mage_Sales_Model_Quote_Item_Option is not subtype of type Mage_Sales_Model_Quote_Item_Option|null.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(array|bool|float|int|resource|string|null, int=): int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Type/Grouped.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(array|bool|float|int|resource|string|null, int=): int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Type/Grouped.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Product/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 8,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Core_Model_Website::getStoreIds() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Attribute.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $object of method Mage_Catalog_Model_Resource_Attribute::_clearUselessAttributeValues() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Attribute.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $condition might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Catalog_Model_Resource_Category_Collection::addSortedField().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $options might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Model_Resource_Db_Abstract::getMainStoreTable().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category/Flat/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category/Flat/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $resource of method Mage_Core_Model_Resource_Db_Collection_Abstract::__construct() expects Mage_Core_Model_Resource_Db_Abstract|null, Mage_Core_Model_Resource_Abstract|null given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category/Flat/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category/Indexer/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Model_Resource_Category_Tree::_getItemIsActive() invoked with 2 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Collection/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Eav/Attribute.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Catalog_Model_Resource_Category::refreshProductIndex().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to function is_array() with non-empty-array will always evaluate to true.',
	'identifier' => 'function.alreadyNarrowedType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 12,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Image.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Model_Url::refreshProductRewrites() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Urlkey.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Collection::_addUrlRewrite() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Catalog_Model_Resource_Product_Collection::$_map is not the same as PHPDoc type array|null of overridden property Varien_Data_Collection_Db::$_map.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $websiteId might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $object of method Mage_Eav_Model_Entity_Collection_Abstract::setObject() expects Varien_Object|null, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Compare/Item/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Indexer_Abstract::_getAttribute() should return Mage_Catalog_Model_Resource_Eav_Attribute but returns Mage_Eav_Model_Entity_Attribute_Abstract|false.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Eav.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Eav/Source.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 10,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Model_Resource_Product_Indexer_Price_Default is not subtype of type Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Link.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Link/Product/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Left side of && is always true.',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Link/Product/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $newOptionId of method Mage_Catalog_Model_Product_Option_Value::duplicate() expects int, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Option.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Option/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Option/Value/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Relation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Status.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 6,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Product/Type/Configurable/Attribute.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Resource/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Loose comparison using == between true and true will always evaluate to true.',
	'identifier' => 'equal.alwaysTrue',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Core_Model_Url is not subtype of type bool|Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Catalog/data/catalog_setup/data-upgrade-1.6.0.0.4-1.6.0.0.5.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Aggregation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_CatalogIndex_Model_Resource_Attribute::checkCount().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Attribute.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Model_Product_Type_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Data/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #8 $productId of method Mage_Catalog_Model_Product_Type_Price::calculatePrice() expects int|null, array given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Data/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $idField might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Data/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $whereField might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Data/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array<bool|int> of property Mage_CatalogIndex_Model_Data_Configurable::$_haveChildren is not the same as PHPDoc type array|false of overridden property Mage_CatalogIndex_Model_Data_Abstract::$_haveChildren.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Data/Configurable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Resource/Aggregation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $aggregationId of method Mage_CatalogIndex_Model_Resource_Aggregation::_saveTagRelations() expects int, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Resource/Aggregation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Resource/Indexer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #3 $storeIds of method Mage_CatalogIndex_Model_Resource_Setup::_setWebsiteInfo() expects array, Mage_Core_Model_Website given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogIndex/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Helper/Minsaleqty.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Indexer/Stock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_CatalogInventory_Model_Indexer_Stock::$_matchedEntities is not the same as PHPDoc type mixed of overridden property Mage_Index_Model_Indexer_Abstract::$_matchedEntities.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Indexer/Stock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Resource/Indexer/Stock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_CatalogInventory_Model_Resource_Indexer_Stock_Default is not subtype of type Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Resource/Indexer/Stock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Resource/Indexer/Stock/Default.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Resource/Stock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%2$d"), bool given.',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Resource/Stock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #5 of function sprintf is expected to be int by placeholder #4 ("%%4$d"), bool given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Resource/Stock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Resource/Stock/Item/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_CatalogInventory_Model_Resource_Stock_Item_Collection::_initSelect() should return $this(Mage_CatalogInventory_Model_Resource_Stock_Item_Collection) but returns Varien_Db_Select.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Resource/Stock/Item/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Stock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to function is_numeric() with int will always evaluate to true.',
	'identifier' => 'function.alreadyNarrowedType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogInventory/Model/Stock/Status.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Action/Index/Refresh.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Catalog_Model_Product_Condition is not subtype of type bool|Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Action/Index/Refresh.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Core_Model_Date is not subtype of type bool|Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Action/Index/Refresh.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Eav_Model_Config is not subtype of type Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Action/Index/Refresh.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_CatalogRule_Model_Flag::$_flagCode is not the same as PHPDoc type string|null of overridden property Mage_Core_Model_Flag::$_flagCode.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Flag.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $combine of method Mage_CatalogRule_Model_Observer::_removeAttributeFromConditions() expects Mage_CatalogRule_Model_Rule_Condition_Combine, Mage_Rule_Model_Condition_Combine given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 6,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_CatalogRule_Model_Action_Index_Refresh is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Varien_Db_Adapter_Interface is not subtype of type Magento_Db_Adapter_Pdo_Mysql.',
	'identifier' => 'varTag.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Resource/Rule/Product/Price.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Rule_Model_Condition_Combine::collectValidatedAttributes().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $product of method Mage_CatalogRule_Model_Resource_Rule::applyAllRules() expects int|Mage_Catalog_Model_Product|null, Mage_Core_Model_Abstract given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $product of method Mage_CatalogRule_Model_Resource_Rule::applyToProduct() expects Mage_Catalog_Model_Product, Mage_Core_Model_Abstract given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogRule/Model/Rule.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Block/Advanced/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Block/Term.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $array of function implode expects array<string>, array<array<string>|string|null> given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Model/Advanced.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_CatalogSearch_Model_Advanced::getProductCollection() should return Mage_CatalogSearch_Model_Resource_Advanced_Collection but returns array|float|int|string|false|null.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Model/Advanced.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 8,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Model/Indexer/Fulltext.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_CatalogSearch_Model_Indexer_Fulltext::$_matchedEntities is not the same as PHPDoc type mixed of overridden property Mage_Index_Model_Indexer_Abstract::$_matchedEntities.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Model/Indexer/Fulltext.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 6,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Model/Resource/Advanced.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $typeId of method Mage_CatalogSearch_Model_Resource_Fulltext::_getProductTypeInstance() expects string, int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $typeId of method Mage_CatalogSearch_Model_Resource_Fulltext::_getProductChildrenIds() expects int, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Model/Resource/Search/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/CatalogSearch/Model/Resource/Search/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method setMethod() on object|false.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Centinel/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Centinel_Model_StateAbstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Centinel/Model/Service.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method setItem() on array.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Block/Cart/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Block/Cart/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Block/Cart/Shipping.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Block/Cart/Sidebar.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Block/Onepage/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Block/Onepage/Billing.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Block/Onepage/Progress.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Block/Onepage/Shipping/Method/Available.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Helper/Cart.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Api/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Sales_Model_Quote_Item is not subtype of native type null.',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Api/Resource/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $message of method Mage_Checkout_Model_Session::addQuoteItemMessage() expects Mage_Core_Model_Message, Mage_Core_Model_Message_Error|Mage_Core_Model_Message_Notice|Mage_Core_Model_Message_Success|Mage_Core_Model_Message_Warning given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $product might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $productId might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always false.',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart/Coupon/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart/Customer/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $customer might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart/Customer/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart/Payment/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 13,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart/Product/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Cart/Product/Api/V2.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Model_Session::getQuoteId() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Sales_Model_Order::createFromQuoteAddress().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Type/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Type/Multishipping.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Sales_Model_Quote::getBillingAddress() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Type/Multishipping.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 10,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Type/Onepage.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always true.',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/Model/Type/Onepage.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Dead catch - Exception is never thrown in the try block.',
	'identifier' => 'catch.neverThrown',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $qty of method Mage_Sales_Model_Quote_Item::setQty() expects float, array|string|null given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/controllers/MultishippingController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Checkout/controllers/OnepageController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $attr of method Mage_ConfigurableSwatches_Helper_Data::attrIsSwatchType() expects int|Mage_Eav_Model_Attribute, Mage_Catalog_Model_Resource_Eav_Attribute given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ConfigurableSwatches/Block/Catalog/Layer/State/Swatch.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ConfigurableSwatches/Block/Catalog/Media/Js/List.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ConfigurableSwatches/Block/Catalog/Media/Js/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/ConfigurableSwatches/Helper/Mediafallback.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/ConfigurableSwatches/Helper/Productimg.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 2 parameters, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ConfigurableSwatches/Helper/Productimg.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $attr of method Mage_ConfigurableSwatches_Helper_Data::attrIsSwatchType() expects int|Mage_Eav_Model_Attribute, Mage_Catalog_Model_Resource_Eav_Attribute given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ConfigurableSwatches/Helper/Productlist.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ConfigurableSwatches/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ConfigurableSwatches/Model/Resource/Catalog/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 8,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Core_Block_Abstract::getChildData() should return mixed but return statement is missing.',
	'identifier' => 'return.missing',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Core_Model_Url is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Html/Link.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Html/Select.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Ternary operator condition is always true.',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Messages.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Template/Facade.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $key might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Template/Facade.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Text/List/Item.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Text/List/Link.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Text/Tag/Css.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Text/Tag/Css/Admin.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Block/Text/Tag/Js.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Front/Action.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $value of method Zend_Controller_Response_Abstract::setHeader() expects string, int|false given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Front/Action.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Request/Http.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 19,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $value of method Zend_Controller_Response_Abstract::setHeader() expects string, int|false given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property Mage_Core_Controller_Varien_Action::$_request (Mage_Core_Controller_Request_Http) does not accept Zend_Controller_Request_Abstract.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property Mage_Core_Controller_Varien_Action::$_response (Mage_Core_Controller_Response_Http) does not accept Zend_Controller_Response_Abstract.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Argument of an invalid type string supplied for foreach, only iterables are supported.',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Front.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Comparison operation ">" between int<0, 100> and 100 is always false.',
	'identifier' => 'greater.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Front.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Front.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Core_Model_Url_Rewrite_Request is not subtype of type bool|Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Front.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Router/Admin.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $useRouterName of method Mage_Core_Controller_Varien_Router_Standard::collectRoutes() expects string, bool given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Router/Admin.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always true.',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Varien_Simplexml_Element is not subtype of type Mage_Core_Model_Config_Element.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Helper/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Helper/Cookie.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $str might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Helper/EnvironmentConfigLoader.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(string): bool)|null, Closure(string, string=): string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Helper/EnvironmentConfigLoader.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Helper/Http.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Helper/Js.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $arguments of static method Mage::getResourceModel() expects array, object given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 10,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/App/Emulation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Cache.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 16,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Varien_Simplexml_Element is not subtype of type Mage_Core_Model_Config_Element.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Mage_Core_Model_Config_Element of property Mage_Core_Model_Config::$_xml is not the same as PHPDoc type SimpleXMLElement of overridden property Varien_Simplexml_Config::$_xml.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $constructArguments of method Mage_Core_Model_Config::getModelInstance() expects array|object, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Config/Element.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Config/Options.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Cookie.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Binary operation "+" between string and string results in an error.',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Date.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Binary operation "-" between string and string results in an error.',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Date.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $regRule might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Date.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Model_Resource_Design::validate().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Design.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Design.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $cache of method Varien_Simplexml_Config::setCache() expects Varien_Simplexml_Config_Cache_Abstract, Zend_Cache_Core given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Design/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $value of method Varien_Simplexml_Config::setNode() expects string, null given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Design/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 20,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $policyCode of method Mage_Core_Model_Domainpolicy::_getDomainPolicyByCode() expects string, int given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Domainpolicy.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Email.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Email/Queue.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Email/Template.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Core_Model_Email_Template::getProcessedTemplate() invoked with 2 parameters, 0-1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Email/Template.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $value of method Mage_Core_Model_Email_Template::setId() expects int, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Email/Template.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Core_Model_Email_Template_Abstract::loadByConfigPath() invoked with 2 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Email/Template/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Email/Template/Filter.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Email/Template/Mailer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Crypt_Abstract::init().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Encryption.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Factory.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/File/Storage/Database.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $params of method Mage_Core_Model_File_Storage_Database_Abstract::__construct() expects array, string|null given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/File/Storage/Database.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/File/Storage/Database/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/File/Storage/Directory/Database.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $params of method Mage_Core_Model_File_Storage_Database_Abstract::__construct() expects array, string|null given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/File/Storage/Directory/Database.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/File/Storage/File.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Core_Model_File_Storage_Flag::$_flagCode is not the same as PHPDoc type string|null of overridden property Mage_Core_Model_Flag::$_flagCode.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/File/Storage/Flag.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/File/Uploader.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Core_Model_File_Validator_AvailablePath::$_value is not the same as PHPDoc type mixed of overridden property Mage_Core_Helper_Validate_Abstract::$_value.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/File/Validator/AvailablePath.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Core_Model_File_Validator_NotProtectedExtension::$_value is not the same as PHPDoc type mixed of overridden property Mage_Core_Helper_Validate_Abstract::$_value.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/File/Validator/NotProtectedExtension.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Input/Filter.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Input/Filter/MaliciousCode.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method SimpleXMLElement::getAttribute().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Simplexml_Element::getBlockName().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 13,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $node of method Varien_Simplexml_Config::setXml() expects Varien_Simplexml_Element, SimpleXMLElement given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $node of method Varien_Simplexml_Config::setXml() expects Varien_Simplexml_Element, SimpleXMLElement|false given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $sourceData of method Varien_Simplexml_Config::__construct() expects string|Varien_Simplexml_Element|null, array given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Layout/Element.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Layout/Update.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Varien_Simplexml_Element of property Mage_Core_Model_Layout_Validator::$_value is not the same as PHPDoc type mixed of overridden property Mage_Core_Helper_Validate_Abstract::$_value.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Layout/Validator.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Message/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Model_Resource_Type_Abstract::getConnection().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method SimpleXMLElement::getClassName().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $adapter of method Varien_Db_Adapter_Interface::setCacheAdapter() expects Zend_Cache_Backend_Interface, Zend_Cache_Core given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $value of method Mage_Core_Model_Config_Element::is() expects string|true, 1 given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Db/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Db/Collection/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property Mage_Core_Model_Resource_Db_Collection_Abstract::$_resource (Mage_Core_Model_Resource_Db_Abstract) does not accept Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Db/Collection/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $alias might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Db/Collection/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 15,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Design.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Helper/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Iterator.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #6 $gc of function session_set_save_handler expects callable(string): bool, array{$this(Mage_Core_Model_Resource_Session), \'gc\'} given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Session.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 6,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property Mage_Core_Model_Resource_Setup::$_resourceConfig (Mage_Core_Model_Config_Element) does not accept SimpleXMLElement.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always false.',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Setup/Query/Modifier.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Transaction.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Translate.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Type/Db/Mysqli.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Right side of && is always true.',
	'identifier' => 'booleanAnd.rightAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Type/Db/Mysqli.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Type/Db/Pdo/Mysql.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Website/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Core_Model_Resource_Website_Collection::$_map is not the same as PHPDoc type array|null of overridden property Varien_Data_Collection_Db::$_map.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Resource/Website/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Session/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $host of method Mage_Core_Model_Session_Abstract::addHost() expects string, true given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Session/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Session/Abstract/Varien.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Access to an undefined property Zend_Session_Namespace::$data.',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Session/Abstract/Zend.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string|true of property Mage_Core_Model_Store::$_cacheTag is not the same as PHPDoc type array|bool|string of overridden property Mage_Core_Model_Abstract::$_cacheTag.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Template.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Translate.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $scope of method Mage_Core_Model_Translate::_addData() expects string, false given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Translate.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Translate/Inline.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #3 $storeId of method Mage_Core_Model_Resource_Translate_String::deleteTranslate() expects int|null, false given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Translate/Inline.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Core_Model_Url::getRequest() should return Mage_Core_Controller_Request_Http but returns Zend_Controller_Request_Http.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Url/Rewrite.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always true.',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Url/Rewrite.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Unreachable statement - code above always terminates.',
	'identifier' => 'deadCode.unreachable',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Url/Rewrite.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 12,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Url/Rewrite/Request.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always true.',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Url/Rewrite/Request.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Unreachable statement - code above always terminates.',
	'identifier' => 'deadCode.unreachable',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Url/Rewrite/Request.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Variable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Core/Model/Website.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Cron/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Cron/Model/Schedule.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Csp/Block/Meta.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Csp/Model/Observer/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/CurrencySymbol/Model/System/Currencysymbol.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Block/Account/Dashboard.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Wishlist_Model_Resource_Item_Collection::addAttributeToSelect().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Block/Account/Dashboard/Sidebar.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Block/Account/Navigation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Block/Address/Book.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Block/Widget/Name.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Helper/Address.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Address.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Address/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Access to an undefined property Varien_Object::$is_default_billing.',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Address/Api/V2.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Access to an undefined property Varien_Object::$is_default_shipping.',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Address/Api/V2.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Customer_Model_Api2_Customer_Address::_getDefaultAddressesInfo().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Api2/Customer/Address.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Api2/Customer/Address/Validator.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Attribute/Backend/Data/Boolean.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 10,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Convert/Adapter/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Convert/Adapter/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Entity_Abstract::setStore().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Convert/Parser/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Convert/Parser/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $regionId might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Convert/Parser/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Access to an undefined property Mage_Customer_Model_Customer::$_isSubscribed.',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 15,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Customer_Model_Customer::$_cacheTag is not the same as PHPDoc type array|bool|string of overridden property Mage_Core_Model_Abstract::$_cacheTag.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Entity/Address/Attribute/Source/Country.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Group.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Resource/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $entityId of method Mage_Eav_Model_Entity_Abstract::load() expects int, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Resource/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 10,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/controllers/AccountController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/sql/customer_setup/mysql4-data-upgrade-1.4.0.0.13-1.4.0.0.14.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Customer/sql/customer_setup/mysql4-data-upgrade-1.4.0.0.7-1.4.0.0.8.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Dataflow_Model_Batch_Export is not subtype of type string|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Batch.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Dataflow_Model_Batch_Import is not subtype of type string|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Batch.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Dataflow_Model_Convert_Action::getData().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Dataflow_Model_Convert_Action_Abstract::getClassNameByType().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Action/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Dataflow_Model_Convert_Container_Interface::setAction().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Action/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Dataflow_Model_Convert_Container_Interface::setProfile().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Action/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Action/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property Mage_Dataflow_Model_Convert_Action_Abstract::$_container (Mage_Dataflow_Model_Convert_Container_Abstract) does not accept Mage_Dataflow_Model_Convert_Container_Interface.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Action/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Undefined variable: $action',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Action/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Adapter/Http.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always false.',
	'identifier' => 'if.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Adapter/Io.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always true.',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Adapter/Io.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Container/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Dataflow_Model_Convert_Container_Interface::getName().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Container/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Parser/Xml/Excel.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Undefined variable: $profile',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Convert/Profile/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Right side of && is always true.',
	'identifier' => 'booleanAnd.rightAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $fields might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Dataflow/Model/Session/Parser/Csv.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Directory/Block/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Directory/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Directory/Model/Country.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Directory/Model/Currency/Import/Currencyconverterapi.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Directory/Model/Currency/Import/Fixerio.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Directory_Model_Currency_Import_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Directory/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Directory/Model/Resource/Country/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Directory/Model/Resource/Region/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type array<Mage_Downloadable_Model_Sample> is not subtype of type Mage_Downloadable_Model_Resource_Sample_Collection.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/Block/Adminhtml/Catalog/Product/Edit/Tab/Downloadable/Samples.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/Block/Customer/Products/List.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/Helper/Download.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/Model/Link/Api/Validator.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(array|bool|float|int|resource|string|null, int=): int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/Model/Product/Type.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/Model/Resource/Link.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/Model/Resource/Link/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/Model/Resource/Sample.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/Model/Resource/Sample/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Dead catch - Mage_Core_Exception is never thrown in the try block.',
	'identifier' => 'catch.neverThrown',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/controllers/DownloadController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Customer_Model_Session::setBeforeAuthUrl() invoked with 2 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Downloadable/controllers/DownloadController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $attr of method Mage_ConfigurableSwatches_Helper_Data::attrIsSwatchType() expects int|Mage_Eav_Model_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Block/Adminhtml/Attribute/Edit/Options/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Resource_Entity_Attribute::getUsedInForms().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Eav_Model_Attribute_Data_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute/Data/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property Mage_Eav_Model_Attribute_Data_Abstract::$_attribite (Mage_Eav_Model_Attribute) does not accept Mage_Eav_Model_Entity_Attribute_Abstract.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute/Data/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute/Data/Date.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Entity_Abstract::getEntityTypeCode().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute/Data/File.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute/Data/File.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute/Data/Image.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute/Data/Multiline.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute/Data/Select.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Attribute/Data/Text.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Config::getAttribute() should return Mage_Eav_Model_Entity_Attribute_Abstract|false but returns Mage_Eav_Model_Entity_Attribute_Interface.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $defBind might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Convert/Adapter/Entity.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $entityIds might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Convert/Adapter/Entity.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Binary operation "." between Mage_Eav_Model_Entity_Interface and \'_collection\' results in an error.',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Convert/Adapter/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method addData() on Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Convert/Adapter/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method getId() on Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Convert/Adapter/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Convert/Adapter/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $modelClass of static method Mage::getResourceModel() expects string, Mage_Eav_Model_Entity_Interface given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Convert/Adapter/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $entity might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Convert/Adapter/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Convert/Parser/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method lastInsertId() on Varien_Db_Adapter_Interface|false.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 18,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Abstract::getAttribute() should return Mage_Catalog_Model_Resource_Eav_Attribute|false but returns Mage_Eav_Model_Entity_Attribute_Abstract.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Model_Resource_Db_Abstract::getIdByCode().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Attribute/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Attribute/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property Mage_Eav_Model_Entity_Attribute_Abstract::$_entity (Mage_Eav_Model_Entity_Abstract) does not accept Mage_Eav_Model_Entity_Type.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Attribute/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Datetime.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Attribute/Frontend/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Entity_Collection_Abstract::getStoreId().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $attribute of method Mage_Eav_Model_Resource_Entity_Attribute_Option::addOptionValueToCollection() expects Mage_Eav_Model_Entity_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $resource of method Mage_Eav_Model_Entity_Collection_Abstract::__construct() expects Mage_Core_Model_Resource_Abstract|null, Varien_Db_Adapter_Interface|false given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Entity_Attribute_Interface::getAttributeCode().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Collection/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 12,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Collection/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $className of method Varien_Data_Collection::setItemObjectClass() expects string, null given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Collection/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Increment/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Increment/Numeric.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 10,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $code of method Mage_Eav_Model_Entity_Setup::updateEntityType() expects string, int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #3 $value of method Mage_Eav_Model_Entity_Setup::updateEntityType() expects string|null, int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Entity/Type.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $attribute of static method Mage_Eav_Model_Attribute_Data::factory() expects Mage_Eav_Model_Attribute, Mage_Eav_Model_Entity_Attribute given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Form.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Form/Type.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Resource/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Resource/Entity/Attribute.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Resource/Entity/Attribute/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Entity_Collection_Abstract::getStoreId().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Resource/Entity/Attribute/Option.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Resource/Form/Attribute/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Resource/Form/Fieldset.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Eav/Model/Resource/Form/Type.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/GiftMessage/Helper/Message.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/GiftMessage/Model/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/GiftMessage/Model/Api/V2.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_GiftMessage_IndexController::_getMappedType().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/GiftMessage/controllers/IndexController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/GoogleAnalytics/Block/Ga.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/GoogleAnalytics/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_GoogleCheckout_Model_Payment::$_code is not the same as PHPDoc type mixed of overridden property Mage_Payment_Model_Method_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/GoogleCheckout/Model/Payment.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Block/Adminhtml/Export/Filter.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Mage_Eav_Model_Resource_Entity_Attribute_Collection|null of property Mage_ImportExport_Block_Adminhtml_Export_Filter::$_collection is not the same as PHPDoc type Varien_Data_Collection_Db|null of overridden property Mage_Adminhtml_Block_Widget_Grid::$_collection.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Block/Adminhtml/Export/Filter.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_ImportExport_Model_Export_Adapter_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_ImportExport_Model_Export_Entity_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export/Adapter/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export/Entity/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export/Entity/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export/Entity/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export/Entity/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 29,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Export/Entity/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_ImportExport_Model_Import_Entity_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Adapter/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Customer_Model_Attribute is not subtype of type Mage_Customer_Model_Customer|null.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $str of method Mage_Core_Helper_UnserializeArray::unserialize() expects string, array given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Customer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Customer/Address.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 30,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always true.',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Varien_Db_Adapter_Interface is not subtype of type Varien_Db_Adapter_Pdo_Mysql.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(mixed): int<0, max> given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Result of || is always true.',
	'identifier' => 'booleanOr.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Grouped.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_ImportExport_Model_Resource_Import_Data::getIterator() should return IteratorIterator but returns Traversable<mixed, mixed>.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/Model/Resource/Import/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ImportExport/controllers/Adminhtml/ImportController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Event.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Indexer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Indexer/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Index_Model_Lock_Storage_Interface is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Lock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #3 ...$values of function sprintf expects bool|float|int|string|null, array<string, int|string>|null given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Lock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Strict comparison using === between mixed and null will always evaluate to false.',
	'identifier' => 'identical.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Lock.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Process.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Resource/Event.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Resource/Event/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $adapter of method Varien_Db_Adapter_Interface::setCacheAdapter() expects Zend_Cache_Backend_Interface, Zend_Cache_Core given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Resource/Lock/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Index/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Index/controllers/Adminhtml/ProcessController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Block/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Block/Db/Main.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Block/Db/Type.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 10,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Model/Installer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $value of method Mage_Core_Model_Resource_Setup::setConfigData() expects string, int given.',
	'identifier' => 'argument.type',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Model/Installer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Model/Installer/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Model/Installer/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Model/Installer/Console.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always false.',
	'identifier' => 'if.alwaysFalse',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Model/Installer/Console.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Install_Model_Installer_Db_Abstract::getVersion().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Model/Installer/Db.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Model/Installer/Db.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Install_Model_Installer_Db_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Model/Installer/Db.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Install/Model/Installer/Env.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Install/controllers/WizardController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Log/Model/Adminhtml/System/Config/Source/Loglevel.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Binary operation "+" between non-falsy-string and 1 results in an error.',
	'identifier' => 'binaryOp.invalid',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Log/Model/Resource/Log.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Eav_Model_Entity_Attribute_Abstract is not subtype of type Mage_Customer_Model_Customer|null.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Log/Model/Resource/Visitor/Online/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Log/Model/Visitor.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Newsletter/Model/Subscriber.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to function is_null() with Mage_Newsletter_Model_Queue will always evaluate to false.',
	'identifier' => 'function.impossibleType',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Newsletter/Model/Template.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Oauth/Model/Server.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Oauth/controllers/Adminhtml/Oauth/AuthorizeController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Admin_Model_Session is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Oauth/controllers/Adminhtml/Oauth/AuthorizeController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Customer_Model_Session is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Oauth/controllers/AuthorizeController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Customer_Model_Session is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Oauth/controllers/Customer/TokenController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Page/Block/Html.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 19,
	'path' => __DIR__ . '/app/code/core/Mage/Page/Block/Html/Head.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Page/Block/Html/Header.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Page/Block/Html/Toplinks.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Page/Block/Html/Topmenu.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Page/Block/Html/Welcome.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Page/Block/Html/Wrapper.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Page/Block/Js/Cookie.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Page/Block/Template/Links.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Page/Helper/Layout.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Payment_Model_Method_Abstract::isPartialAuthorization().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paygate/Block/Authorizenet/Form/Cc.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $block of method Mage_Core_Block_Abstract::setChild() expects Mage_Core_Block_Abstract, string given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Paygate/Block/Authorizenet/Form/Cc.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Payment_Model_Method_Abstract::cancelPartialAuthorization().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paygate/controllers/Adminhtml/Paygate/Authorizenet/PaymentController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Payment_Model_Method_Abstract::cancelPartialAuthorization().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paygate/controllers/Authorizenet/PaymentController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Block/Form/Container.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Block/Info.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Block/Info/Banktransfer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Block/Info/Cc.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%02d"), string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Block/Info/Cc.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Payment_Model_Method_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Payment_Model_Method_Abstract|false is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Billing/AgreementAbstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Payment_Model_Method_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Info.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Method/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Payment_Model_Method_Banktransfer::$_code is not the same as PHPDoc type mixed of overridden property Mage_Payment_Model_Method_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Method/Banktransfer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Payment_Model_Method_Banktransfer::$_formBlockType is not the same as PHPDoc type mixed of overridden property Mage_Payment_Model_Method_Abstract::$_formBlockType.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Method/Banktransfer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Payment_Model_Method_Cashondelivery::$_code is not the same as PHPDoc type mixed of overridden property Mage_Payment_Model_Method_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Method/Cashondelivery.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Payment_Model_Method_Cashondelivery::$_formBlockType is not the same as PHPDoc type mixed of overridden property Mage_Payment_Model_Method_Abstract::$_formBlockType.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Method/Cashondelivery.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Loose comparison using != between \'\'|\'OT\' and \'SS\' will always evaluate to true.',
	'identifier' => 'notEqual.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Method/Cc.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Method/Checkmo.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Method/Free.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type bool of property Mage_Payment_Model_Method_Free::$_canAuthorize is not the same as PHPDoc type mixed of overridden property Mage_Payment_Model_Method_Abstract::$_canAuthorize.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Method/Free.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Payment_Model_Method_Free::$_code is not the same as PHPDoc type mixed of overridden property Mage_Payment_Model_Method_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Method/Free.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Dead catch - Mage_Core_Exception is never thrown in the try block.',
	'identifier' => 'catch.neverThrown',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Payment_Model_Recurring_Profile::exportStartDatetime() should return string|Zend_Date but empty return statement found.',
	'identifier' => 'return.empty',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, array given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type bool of property Mage_Paypal_Block_Adminhtml_Settlement_Report_Grid::$_saveParametersInSession is not the same as PHPDoc type mixed of overridden property Mage_Adminhtml_Block_Widget_Grid::$_saveParametersInSession.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Block/Adminhtml/Settlement/Report/Grid.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Fieldset/Deprecated.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $value of method Mage_Core_Model_Config::saveConfig() expects string, int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Fieldset/Deprecated.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Fieldset/Group.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Fieldset/PathDependent.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Fieldset/Payment.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Block/Express/Review/Details.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Block/Express/Shortcut.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Paypal_Model_Config is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Paypal_Model_Express_Checkout is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $setToken of method Mage_Paypal_Controller_Express_Abstract::_initToken() expects string|null, false given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Api/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Paypal_Model_Api_Nvp::_export().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Paypal_Model_Api_Nvp::_import().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Paypal_Model_Api_Nvp::$_lineItemTotalExportMap is not the same as PHPDoc type mixed of overridden property Mage_Paypal_Model_Api_Abstract::$_lineItemTotalExportMap.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $array (non-empty-list) of array_values is already a list, call has no effect.',
	'identifier' => 'arrayValues.list',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Paypal_Model_Api_Standard::$_lineItemTotalExportMap is not the same as PHPDoc type mixed of overridden property Mage_Paypal_Model_Api_Abstract::$_lineItemTotalExportMap.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Api/Standard.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Paypal_Model_Bml::$_code is not the same as PHPDoc type mixed of overridden property Mage_Paypal_Model_Express::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Bml.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Paypal_Model_Bml::$_formBlockType is not the same as PHPDoc type mixed of overridden property Mage_Paypal_Model_Express::$_formBlockType.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Bml.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Object::isNominal().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Cart.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Empty array passed to foreach.',
	'identifier' => 'foreach.emptyArray',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Cart.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Left side of && is always true.',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Cart.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always true.',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Paypal_Model_Pro is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%02d"), string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always false.',
	'identifier' => 'if.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Paypal_Model_Pro is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $amount of method Mage_Paypal_Model_Express::_callDoAuthorize() expects int, float given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $datetime of class DateTime constructor expects string, null given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Express/Checkout.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, array given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Express/Checkout.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Hostedpro/Request.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Info.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Binary operation "-" between array|string and array|string results in an error.',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $amount of method Mage_Sales_Model_Order_Payment::registerCaptureNotification() expects float, array|string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $url of method Zend_Http_Client_Adapter_Interface::write() expects Zend_Uri_Http, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Paypal_Model_Method_Agreement::$_code is not the same as PHPDoc type mixed of overridden property Mage_Payment_Model_Method_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Paypal_Model_Payflowadvanced::$_code is not the same as PHPDoc type mixed of overridden property Mage_Paypal_Model_Payflowlink::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Payflowadvanced.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Paypal_Model_Payflowadvanced::$_formBlockType is not the same as PHPDoc type mixed of overridden property Mage_Paypal_Model_Payflowlink::$_formBlockType.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Payflowadvanced.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Paypal_Model_Payflowadvanced::$_infoBlockType is not the same as PHPDoc type mixed of overridden property Mage_Paypal_Model_Payflowlink::$_infoBlockType.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Payflowadvanced.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Payflowlink.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $payment of method Mage_Paypal_Model_Payflowpro::_buildBasicRequest() expects Mage_Sales_Model_Order_Payment, Mage_Payment_Model_Info given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $storeId of method Mage_Payment_Model_Method_Abstract::getConfigData() expects int|Mage_Core_Model_Store|string|null, false given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Paypal_Model_Config is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Pro.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Report/Settlement/Row.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Resource/Payment/Transaction.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/Resource/Report/Settlement.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/System/Config/Backend/Cert.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/System/Config/Backend/MerchantCountry.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Varien_Data_Collection::toOptionArray() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/System/Config/Source/BuyerCountry.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Varien_Data_Collection::toOptionArray() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/Model/System/Config/Source/MerchantCountry.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Paypal/controllers/Adminhtml/Paypal/ReportsController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/PaypalUk/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 of function sprintf is expected to be int by placeholder #1 ("%%02d"), string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/PaypalUk/Model/Direct.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_ProductAlert_Helper_Data::createBlock() should return Mage_ProductAlert_Block_Email_Price|Mage_ProductAlert_Block_Email_Stock but returns Mage_Core_Block_Abstract.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ProductAlert/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_ProductAlert_Model_Observer::process() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/ProductAlert/controllers/AddController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Rating/Model/Resource/Rating.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Rating/Model/Resource/Rating/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Rating_Model_Resource_Rating_Collection::getItemById() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Rating/Model/Resource/Rating/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method addAttributeToSelect() on Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Block/Product/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Block/Product/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Reports_Block_Product_Compared::$_indexName is not the same as PHPDoc type string|null of overridden property Mage_Reports_Block_Product_Abstract::$_indexName.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Block/Product/Compared.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Block/Product/Viewed.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Reports_Block_Product_Viewed::$_indexName is not the same as PHPDoc type string|null of overridden property Mage_Reports_Block_Product_Abstract::$_indexName.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Block/Product/Viewed.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Reports_Model_Resource_Product_Index_Abstract::clean() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Product/Index/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Reports_Model_Report is not subtype of type Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Report.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Customer_Model_Customer::remove().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Customer/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Sales_Model_Resource_Quote_Item_Collection::setQuoteFilter().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Customer/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Customer/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Data_Collection_Db::getResource().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Event.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Reports_Model_Resource_Order_Collection::_prepareSummaryAggregated() invoked with 4 parameters, 3 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $interval of method Varien_Db_Adapter_Interface::getDateAddSql() expects int, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Product/Index/Collection/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Product/Lowstock/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Quote/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Reports_Model_Resource_Quote_Collection::$_map is not the same as PHPDoc type array|null of overridden property Varien_Data_Collection_Db::$_map.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Quote/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Report/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Reports_Model_Resource_Review_Collection::_joinFields().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Review/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Eav_Model_Entity_Attribute is not subtype of type Mage_Catalog_Model_Resource_Eav_Attribute|false.',
	'identifier' => 'varTag.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Review/Customer/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Tag/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Catalog_Model_Resource_Product_Flat|Mage_Eav_Model_Entity_Abstract::setStore().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Resource/Wishlist/Product/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property DOMNode::$nodeValue (string|null) does not accept int<min, -1>|int<1, max>.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Test.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Argument of an invalid type Mage_Reports_Model_Report supplied for foreach, only iterables are supported.',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Reports/Model/Totals.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Review/Block/Helper.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Review/Model/Resource/Review.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Comparison operation "==" between array|null and 1 results in an error.',
	'identifier' => 'equal.invalid',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Comparison operation "==" between array|null and 2 results in an error.',
	'identifier' => 'equal.invalid',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Review/controllers/ProductController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Rss/Block/List.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method getId() on true.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Rss/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Rss/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Action/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Rule_Model_Action_Interface::getId().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Action/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Rule_Model_Action_Interface::setId().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Action/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Rule_Model_Action_Interface::setRule().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Action/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Action/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Rule_Model_Action_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Action/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Condition/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Condition/Combine.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Rule_Model_Condition_Abstract::loadArray() invoked with 2 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Condition/Combine.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Rule_Model_Condition_Product_Abstract::getAttributeObject() should return Mage_Catalog_Model_Resource_Eav_Attribute but returns Mage_Eav_Model_Entity_Attribute_Abstract|Varien_Object|false.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 8,
	'path' => __DIR__ . '/app/code/core/Mage/Rule/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Sales_Block_Adminhtml_Recurring_Profile::$_blockGroup is not the same as PHPDoc type mixed of overridden property Mage_Adminhtml_Block_Widget_Grid_Container::$_blockGroup.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Block/Adminhtml/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Block/Order/Info.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Block/Order/Print/Shipment.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Block/Recurring/Profile/View.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Dead catch - Exception is never thrown in the try block.',
	'identifier' => 'catch.neverThrown',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Controller/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Helper/Guest.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Api/Resource.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Billing/Agreement.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Config/Ordered.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Sales_Model_Resource_Quote_Collection::addAttributeToSelect().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Entity/Quote.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Entity/Quote/Item/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Entity/Sale/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Creditmemo.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Creditmemo/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Sales_Model_Order_Invoice_Item is not subtype of type Mage_Sales_Model_Order_Creditmemo_Item.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Creditmemo/Total/Discount.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Invoice.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Catalog_Model_Product_Type_Abstract::getForceApplyDiscountToParentItem().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Item.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always false.',
	'identifier' => 'if.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $order of method Mage_Sales_Model_Order_Payment_Transaction::setOrder() expects bool|Mage_Sales_Model_Order_Payment|null, Mage_Sales_Model_Order given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $shouldSave of method Mage_Sales_Model_Order_Payment_Transaction::closeAuthorization() expects bool, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $txnId of method Mage_Sales_Model_Order_Payment_Transaction::_beforeLoadByTxnId() expects int, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 11,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Pdf/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Pdf/Creditmemo.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Pdf/Invoice.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Pdf/Shipment.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $text of method Zend_Pdf_Canvas_Abstract::drawText() expects string, (float|int) given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Pdf/Shipment/Packaging.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Pdf/Total/Default.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Entity_Abstract::getStoreId().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Shipment/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Shipment/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $object of method Mage_Sales_Model_Order_Shipment_Api::_getCarriers() expects Mage_Eav_Model_Entity_Abstract, Mage_Sales_Model_Order given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Shipment/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $object of method Mage_Sales_Model_Order_Shipment_Api::_getCarriers() expects Mage_Eav_Model_Entity_Abstract, Mage_Sales_Model_Order_Shipment given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Shipment/Api.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $object of method Mage_Sales_Model_Order_Shipment_Api::_getCarriers() expects Mage_Eav_Model_Entity_Abstract, Mage_Sales_Model_Order given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Shipment/Api/V2.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Total/Config/Base.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Sales_Model_Order_Total_Config_Base::$_collectorsCacheKey is not the same as PHPDoc type string|null of overridden property Mage_Sales_Model_Config_Ordered::$_collectorsCacheKey.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Total/Config/Base.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Sales_Model_Order_Total_Config_Base::$_totalsConfigNode is not the same as PHPDoc type string|null of overridden property Mage_Sales_Model_Config_Ordered::$_totalsConfigNode.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Order/Total/Config/Base.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Sales_Model_Quote_Item_Abstract::compare().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string|true of property Mage_Sales_Model_Quote::$_cacheTag is not the same as PHPDoc type array|bool|string of overridden property Mage_Core_Model_Abstract::$_cacheTag.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $item of method Mage_Sales_Model_Quote::addItem() expects Mage_Sales_Model_Quote_Item, Mage_Sales_Model_Quote_Item_Abstract given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Sales_Model_Quote_Address::getItemsCollection() should return Mage_Eav_Model_Entity_Collection_Abstract but returns Mage_Sales_Model_Resource_Quote_Address_Item_Collection.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $parentItem of method Mage_Sales_Model_Quote_Item_Abstract::setParentItem() expects Mage_Sales_Model_Quote_Item, Mage_Sales_Model_Quote_Address_Item given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $quoteItem of method Mage_Sales_Model_Quote_Address_Item::importQuoteItem() expects Mage_Sales_Model_Quote_Item, Mage_Sales_Model_Quote_Item_Abstract given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Right side of && is always true.',
	'identifier' => 'booleanAnd.rightAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote/Address/Total/Collector.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Sales_Model_Quote_Address_Total_Collector::$_collectorsCacheKey is not the same as PHPDoc type string|null of overridden property Mage_Sales_Model_Config_Ordered::$_collectorsCacheKey.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote/Address/Total/Collector.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Sales_Model_Quote_Address_Total_Collector::$_totalsConfigNode is not the same as PHPDoc type string|null of overridden property Mage_Sales_Model_Config_Ordered::$_totalsConfigNode.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote/Address/Total/Collector.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote/Address/Total/Nominal/RecurringAbstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always true.',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote/Address/Total/Shipping.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Instanceof between Mage_Sales_Model_Quote_Item_Option and Mage_Sales_Model_Quote_Item_Option will always evaluate to true.',
	'identifier' => 'instanceof.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Quote/Item.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to Exception::__construct() on a separate line has no effect.',
	'identifier' => 'new.resultUnused',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Payment_Model_Method_Abstract::canGetRecurringProfileDetails().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Payment_Model_Method_Abstract::submitRecurringProfile().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Payment_Model_Method_Abstract::updateRecurringProfileStatus().',
	'identifier' => 'method.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Sales_Model_Recurring_Profile::getInfoValue() should return mixed but return statement is missing.',
	'identifier' => 'return.missing',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Sales_Model_Resource_Billing_Agreement_Collection::$_map is not the same as PHPDoc type array|null of overridden property Varien_Data_Collection_Db::$_map.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Billing/Agreement/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Reports_Model_Resource_Helper_Interface is not subtype of type Mage_Core_Model_Resource_Helper_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Order/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Sales_Model_Resource_Order_Abstract is not subtype of type Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Order/Address.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $attribute of method Mage_Sales_Model_Resource_Collection_Abstract::addAttributeToFilter() expects Mage_Eav_Model_Entity_Attribute|string, array given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Order/Item/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Order/Payment.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Model_Abstract::isFailsafe().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Order/Status.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Quote/Item/Option/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Quote/Payment.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Recurring/Profile.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Service/Order.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Payment_Model_Recurring_Profile::submit().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Service/Quote.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always true.',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Service/Quote.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $message of static method Mage::throwException() expects string, array given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Service/Quote.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always false.',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sales/Model/Status/List.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/SalesRule/Model/Coupon/Massgenerator.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/SalesRule/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/SalesRule/Model/Resource/Coupon.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always false.',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/SalesRule/Model/Resource/Coupon.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/SalesRule/Model/Resource/Report/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/SalesRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Rule_Model_Action_Collection::validate().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/SalesRule/Model/Validator.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/SalesRule/Model/Validator.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Tax_Model_Config is not subtype of type Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/SalesRule/Model/Validator.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Block/Tracking/Popup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Shipping_Model_Carrier_Abstract::_getQuotes().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Shipping_Model_Carrier_Abstract::_setFreeMethodRequest().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Left side of && is always true.',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always false.',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Shipping_Model_Carrier_Freeshipping::$_code is not the same as PHPDoc type string|null of overridden property Mage_Shipping_Model_Carrier_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Freeshipping.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Pickup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $rate in empty() is never defined.',
	'identifier' => 'empty.variable',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Pickup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Shipping_Model_Rate_Result is not subtype of type Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Shipping_Model_Rate_Result_Error is not subtype of type Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Shipping_Model_Rate_Result_Method is not subtype of type Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Shipping_Model_Carrier_Tablerate::$_code is not the same as PHPDoc type string|null of overridden property Mage_Shipping_Model_Carrier_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Shipping_Model_Carrier_Abstract is not subtype of type Mage_Core_Model_Abstract|false.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Info.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $result might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Rate/Result.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $tmp might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Rate/Result.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Resource/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Shipping.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Shipping_Model_Carrier_Abstract is not subtype of type bool|Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Shipping.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Shipping_Model_Rate_Result::getAllTrackings().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Shipping/Model/Tracking/Result.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sitemap/Model/Resource/Catalog/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sitemap/Model/Resource/Catalog/Category.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sitemap/Model/Resource/Catalog/Product.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 of function sprintf is expected to be float by placeholder #1 ("%%.1f"), string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Sitemap/Model/Sitemap.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Block/All.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Block/Customer/Tags.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Block/Popular.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $productId of method Mage_Tag_Model_Resource_Tag_Collection::addProductFilter() expects int, true given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Block/Product/List.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Model/Entity/Customer/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Tag_Model_Indexer_Summary::$_matchedEntities is not the same as PHPDoc type mixed of overridden property Mage_Index_Model_Indexer_Abstract::$_matchedEntities.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Model/Indexer/Summary.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Model/Resource/Customer/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 8,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Model/Resource/Indexer/Summary.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Model/Resource/Tag.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Tag_Model_Resource_Tag_Collection::$_map is not the same as PHPDoc type array|null of overridden property Varien_Data_Collection_Db::$_map.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Model/Resource/Tag/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Tag/Model/Resource/Tag/Relation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Block/Adminhtml/Notifications.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Admin_Model_Session is not subtype of type Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Block/Adminhtml/Notifications.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Tax_Model_Config is not subtype of type Mage_Core_Model_Abstract.',
	'identifier' => 'varTag.type',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Block/Adminhtml/Notifications.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Mage_Core_Model_App of property Mage_Tax_Block_Adminhtml_Notifications::$_app is not the same as PHPDoc type Mage_Core_Model_App|null of overridden property Mage_Core_Block_Abstract::$_app.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Block/Adminhtml/Notifications.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Mage_Core_Model_Factory of property Mage_Tax_Block_Adminhtml_Notifications::$_factory is not the same as PHPDoc type Mage_Core_Model_Factory|null of overridden property Mage_Core_Block_Abstract::$_factory.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Block/Adminhtml/Notifications.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Block/Checkout/Subtotal.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Mage_Core_Model_Factory of property Mage_Tax_Block_Checkout_Subtotal::$_factory is not the same as PHPDoc type Mage_Core_Model_Factory|null of overridden property Mage_Core_Block_Abstract::$_factory.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Block/Checkout/Subtotal.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Block/Checkout/Tax.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Mage_Core_Model_Factory of property Mage_Tax_Block_Checkout_Tax::$_factory is not the same as PHPDoc type Mage_Core_Model_Factory|null of overridden property Mage_Core_Block_Abstract::$_factory.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Block/Checkout/Tax.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Model_App::getOrder().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always false.',
	'identifier' => 'if.alwaysFalse',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Right side of && is always false.',
	'identifier' => 'booleanAnd.rightAlwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Model/Calculation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Model/Config/Notification.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined static method Mage_Core_Model_Config_Data::afterSave().',
	'identifier' => 'staticMethod.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Model/Config/Price/Include.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Model/Resource/Calculation.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Model/Resource/Calculation/Rule/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Strict comparison using === between non-empty-list and array{} will always evaluate to false.',
	'identifier' => 'identical.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Tax/Model/System/Config/Source/Tax/Region.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $result might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Mage_Shipping_Model_Rate_Result|null of property Mage_Usa_Model_Shipping_Carrier_Dhl::$_result is not the same as PHPDoc type Mage_Shipping_Model_Rate_Result|Mage_Shipping_Model_Tracking_Result|null of overridden property Mage_Shipping_Model_Carrier_Abstract::$_result.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Usa_Model_Shipping_Carrier_Dhl::$_code is not the same as PHPDoc type string|null of overridden property Mage_Shipping_Model_Carrier_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Property Mage_Usa_Model_Shipping_Carrier_Dhl::$_result (Mage_Shipping_Model_Rate_Result|null) does not accept Mage_Shipping_Model_Tracking_Result.',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Access to an undefined property Varien_Object::$domestic.',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Access to an undefined property Varien_Object::$name.',
	'identifier' => 'property.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Access to an undefined property Varien_Object::$region.',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 16,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Usa_Model_Shipping_Carrier_Dhl_International::$_rates is not the same as PHPDoc type array|null of overridden property Mage_Shipping_Model_Carrier_Abstract::$_rates.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Usa_Model_Shipping_Carrier_Dhl_International::$_code is not the same as PHPDoc type string|null of overridden property Mage_Shipping_Model_Carrier_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Label/Pdf/PageBuilder.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Usa_Model_Shipping_Carrier_Fedex::$_code is not the same as PHPDoc type string|null of overridden property Mage_Shipping_Model_Carrier_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Binary operation "*" between float and string results in an error.',
	'identifier' => 'binaryOp.invalid',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 10,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Mage_Shipping_Model_Rate_Result|null of property Mage_Usa_Model_Shipping_Carrier_Ups::$_result is not the same as PHPDoc type Mage_Shipping_Model_Rate_Result|Mage_Shipping_Model_Tracking_Result|null of overridden property Mage_Shipping_Model_Carrier_Abstract::$_result.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Usa_Model_Shipping_Carrier_Ups::$_code is not the same as PHPDoc type string|null of overridden property Mage_Shipping_Model_Carrier_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $value of method SimpleXMLElement::addChild() expects string|null, float given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 6,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Usa_Model_Shipping_Carrier_Usps::$_code is not the same as PHPDoc type string|null of overridden property Mage_Shipping_Model_Carrier_Abstract::$_code.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $serviceType of method Mage_Usa_Model_Shipping_Carrier_Usps::_formUsSignatureConfirmationShipmentRequest() expects string, array|bool given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/sql/usa_setup/upgrade-1.6.0.0-1.6.0.1.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Usa/sql/usa_setup/upgrade-1.6.0.1-1.6.0.2.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Weee/Helper/Data.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 6,
	'path' => __DIR__ . '/app/code/core/Mage/Weee/Model/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Eav_Model_Entity_Attribute_Abstract::isScopeGlobal().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Weee/Model/Resource/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Right side of && is always true.',
	'identifier' => 'booleanAnd.rightAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Weee/Model/Resource/Tax.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Weee/Model/Tax.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always false.',
	'identifier' => 'if.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Weee/Model/Total/Quote/Weee.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type Mage_Weee_Helper_Data of property Mage_Weee_Model_Total_Quote_Weee::$_helper is not the same as PHPDoc type Mage_Tax_Helper_Data of overridden property Mage_Tax_Model_Sales_Total_Quote_Tax::$_helper.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Weee/Model/Total/Quote/Weee.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Widget/Block/Adminhtml/Widget/Instance/Edit/Chooser/Block.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Widget/Block/Adminhtml/Widget/Instance/Edit/Chooser/Layout.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array of property Mage_Widget_Model_Resource_Widget_Instance_Collection::$_map is not the same as PHPDoc type array|null of overridden property Varien_Data_Collection_Db::$_map.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Widget/Model/Resource/Widget/Instance/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/code/core/Mage/Widget/Model/Template/Filter.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Widget/controllers/Adminhtml/Widget/InstanceController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Wishlist/Block/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/code/core/Mage/Wishlist/Block/Customer/Wishlist/Item/Options.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(mixed): int<0, max> given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Wishlist/Controller/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 5,
	'path' => __DIR__ . '/app/code/core/Mage/Wishlist/Model/Item.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 4,
	'path' => __DIR__ . '/app/code/core/Mage/Wishlist/Model/Observer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Wishlist/Model/Resource/Item/Option/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type string of property Mage_Wishlist_Model_Wishlist::$_cacheTag is not the same as PHPDoc type array|bool|string of overridden property Mage_Core_Model_Abstract::$_cacheTag.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Wishlist/Model/Wishlist.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc type array<string> of property Mage_Wishlist_IndexController::$_cookieCheckActions is not the same as PHPDoc type array of overridden property Mage_Core_Controller_Varien_Action::$_cookieCheckActions.',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Wishlist/controllers/IndexController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $wishlist might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Wishlist/controllers/IndexController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/code/core/Mage/Wishlist/controllers/SharedController.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Block_Abstract::_getUsers().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/api/role_users_grid_js.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Block_Abstract::_getSelectedRoles().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/api/user_roles_grid_js.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/catalog/product/edit/price/group.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/catalog/product/edit/price/tier.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $store of method Mage_Core_Helper_Data::currencyByStore() expects int|Mage_Core_Model_Store|null, true given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/catalog/product/price.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_GiftMessage_Block_Message_Helper::prepareAsIs().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/giftmessage/helper.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $value of method Mage_GiftMessage_Block_Message_Helper::setScriptIncluded() expects string, true given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/giftmessage/helper.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/notification/toolbar.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'PHPDoc tag @var with type Mage_Core_Block_Template is not subtype of type Mage_Oauth_Block_Authorize.',
	'identifier' => 'varTag.type',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/oauth/authorize/reject.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/page/header.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Block_Abstract::_getUsers().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/permissions/role_users_grid_js.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Core_Block_Abstract::_getSelectedRoles().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/permissions/user_roles_grid_js.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Comparison operation ">" between int<1, max> and 0 is always true.',
	'identifier' => 'greater.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/report/grid.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/sales/order/create/items/grid.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Abstract::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 18,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/sales/order/create/items/grid.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/sales/order/shipment/packaging/popup.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always true.',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 2,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/system/store/tree.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/tax/notifications.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/weee/renderer/tax.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/widget/breadcrumbs.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid::getEmptyCellColspan() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/widget/grid.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid::getMultipleRowColumns() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/widget/grid.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/widget/grid/serializer.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/widget/tabs.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/widget/tabshoriz.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/base/default/template/widget/tabsleft.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/adminhtml/openmage/default/template/page/header.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Catalog_Block_Product_Abstract::getTierPrices() invoked with 2 parameters, 0-1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/bundle/catalog/product/view/option_tierprices.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/catalog/layer/state.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $attribute of method Mage_Catalog_Block_Product_Compare_List::getProductAttributeValue() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Left side of && is always true.',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/catalog/product/price.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Ternary operator condition is always true.',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/catalog/product/price.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 12,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/cart/item/default.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 6,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/cart/sidebar/default.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Left side of && is always true.',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/multishipping/address/select.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $totals of method Mage_Checkout_Block_Multishipping_Overview::renderTotals() expects Mage_Sales_Model_Order_Total, array<Mage_Sales_Model_Quote_Address_Total> given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/multishipping/overview.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 12,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/multishipping/overview/item.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Block_Multishipping_Shipping::getItemsEditUrl() invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/multishipping/shipping.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/onepage.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method isEnabled() on Mage_Core_Block_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 5,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/onepage/billing.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always false.',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/onepage/payment/methods.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/onepage/progress/shipping.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Ternary operator condition is always true.',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/onepage/progress/shipping.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 12,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/onepage/review/item.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/checkout/onepage/shipping_method/available.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/customer/address.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method isEnabled() on Mage_Core_Block_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/customer/form/edit.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method isEnabled() on Mage_Core_Block_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/customer/form/register.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/directory/js/optional_zip_countries.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 12,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/downloadable/checkout/cart/item/default.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 12,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/downloadable/checkout/onepage/review/item.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method getMessage() on string.',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/giftmessage/inline.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method getRecipient() on string.',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/giftmessage/inline.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method getSender() on string.',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/giftmessage/inline.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Class Mage_Paypal_Block_Express_Form referenced with incorrect case: Mage_PayPal_Block_Express_Form.',
	'identifier' => 'class.nameCase',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/paypal/payment/redirect.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method isEnabled() on Mage_Core_Block_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/persistent/checkout/onepage/billing.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always false.',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method isEnabled() on Mage_Core_Block_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/persistent/customer/form/register.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/rating/detailed.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 (non-empty-array) of echo cannot be converted to string.',
	'identifier' => 'echo.nonString',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/review/helper/summary.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 (non-empty-array) of echo cannot be converted to string.',
	'identifier' => 'echo.nonString',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/review/helper/summary_short.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/review/product/view/count.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Access to an undefined property Mage_Core_Block_Template::$trackingInfo.',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/sales/order/trackinginfo.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/base/default/template/wishlist/item/column/cart.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/catalog/layer/state.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $attribute of method Mage_Catalog_Block_Product_Compare_List::getProductAttributeValue() expects Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Eav_Model_Entity_Attribute_Abstract given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Left side of && is always true.',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/catalog/product/price.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Ternary operator condition is always true.',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/catalog/product/price.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 12,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/checkout/cart/item/default.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/checkout/cart/minicart.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 6,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/checkout/cart/minicart/default.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Mage_Checkout_Block_Cart_Minicart::isPossibleOnepageCheckout().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/checkout/cart/minicart/items.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/checkout/cart/minicart/items.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 6,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/checkout/cart/sidebar/default.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $totals of method Mage_Checkout_Block_Multishipping_Overview::renderTotals() expects Mage_Sales_Model_Order_Total, array<Mage_Sales_Model_Quote_Address_Total> given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/checkout/multishipping/overview.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 12,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/checkout/multishipping/overview/item.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/checkout/onepage.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 12,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/checkout/onepage/review/item.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/configurableswatches/catalog/layer/filter/swatches.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/configurableswatches/catalog/product/list/swatches.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/configurableswatches/catalog/product/view/type/options/configurable/swatches.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method isEnabled() on Mage_Core_Block_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/customer/form/edit.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 12,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/downloadable/checkout/cart/item/default.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() invoked with 3 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 12,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/downloadable/checkout/onepage/review/item.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 3,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/page/html/topmenu/renderer.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method isEnabled() on Mage_Core_Block_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/persistent/checkout/onepage/billing.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'If condition is always true.',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always false.',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method isEnabled() on Mage_Core_Block_Abstract|false.',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/persistent/customer/form/register.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/rating/detailed.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/app/design/frontend/rwd/default/template/wishlist/item/column/price.phtml',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 7,
	'path' => __DIR__ . '/errors/processor.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/get.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Comparison operation ">=" between int<0, max> and 0 is always true.',
	'identifier' => 'greaterOrEqual.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/lib/Mage/Archive.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/lib/Mage/Archive/Helper/File/Gz.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Negated boolean expression is always true.',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/lib/Mage/DB/Mysqli.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Function mysqli_connect_errno invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/lib/Mage/DB/Mysqli.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Function mysqli_connect_error invoked with 1 parameter, 0 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/lib/Mage/DB/Mysqli.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Variable $out might not be defined.',
	'identifier' => 'variable.undefined',
	'count' => 2,
	'path' => __DIR__ . '/lib/Mage/DB/Mysqli.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/lib/Mage/HTTP/Client/Curl.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 9,
	'path' => __DIR__ . '/lib/Mage/HTTP/Client/Socket.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Empty array passed to foreach.',
	'identifier' => 'foreach.emptyArray',
	'count' => 1,
	'path' => __DIR__ . '/lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $array of function array_sum expects an array of values castable to number, list<string> given.',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #4 $mode of function ftp_fput expects 1|2, int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Argument of an invalid type Zend_Db_Statement_Interface supplied for foreach, only iterables are supported.',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/lib/Magento/Db/Object/Trigger.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/lib/Magento/Db/Sql/Trigger.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 2,
	'path' => __DIR__ . '/lib/Magento/Profiler.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Left side of && is always true.',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 2,
	'path' => __DIR__ . '/lib/Varien/Cache/Backend/Database.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $chunks of method Varien_Cache_Backend_Memcached::_cleanTheMess() expects int, string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Cache/Backend/Memcached.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Convert_Action::getData().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Convert.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Convert_Action_Abstract::addException().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/lib/Varien/Convert/Action/Abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Convert_Container_Interface::getName().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/lib/Varien/Convert/Container/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Left side of && is always true.',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Convert/Mapper/Column.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot access property $_items on array.',
	'identifier' => 'property.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/lib/Varien/Data/Collection.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Data_Collection_Db::_initSelect().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #2 $callback of function array_filter expects (callable(mixed): bool)|null, Closure(mixed): array<int<0, max>, non-empty-string> given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Access to an undefined property Varien_Data_Form_Element_Color::$original_data.',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Data/Form/Element/Color.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $index of method Varien_Data_Form_Element_Abstract::getEscapedValue() expects string|null, int<0, max> given.',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/lib/Varien/Data/Form/Element/Multiline.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Varien_Data_Tree::load() invoked with 2 parameters, 0-1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Data/Tree/Node.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Access to an undefined property Throwable::$errorInfo.',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot access offset \'Engine\' on bool.',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method query() on object|resource.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method setAttribute() on object|resource.',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #3 of function sprintf is expected to be int by placeholder #2 ("%%d"), string given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #4 $result of method Varien_Db_Adapter_Pdo_Mysql::_debugStat() expects Zend_Db_Statement_Pdo|null, PDOStatement|Zend_Db_Statement_Interface given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method bindParam() on object|resource.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Db/Statement/Pdo/Mysql.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Cannot call method execute() on object|resource.',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Db/Statement/Pdo/Mysql.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Varien_Filter_Email::filter() should return mixed but return statement is missing.',
	'identifier' => 'return.missing',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Filter/Email.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Right side of && is always false.',
	'identifier' => 'booleanAnd.rightAlwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Filter/Template/Tokenizer/Parameter.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Left side of || is always false.',
	'identifier' => 'booleanOr.leftAlwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Filter/Template/Tokenizer/Variable.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method SimpleXMLElement::setNode().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Simplexml_Config_Cache_Abstract::load().',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Simplexml_Config_Cache_Abstract::remove().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Call to an undefined method Varien_Simplexml_Config_Cache_Abstract::save().',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Varien_Simplexml_Config::getXpath() should return array<Varien_Simplexml_Element>|false but returns non-empty-array<SimpleXMLElement>.',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Method Varien_Simplexml_Config::loadString() invoked with 2 parameters, 1 required.',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $data of function simplexml_load_string expects string, true given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Result of && is always false.',
	'identifier' => 'booleanAnd.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Strict comparison using === between mixed and false will always evaluate to false.',
	'identifier' => 'identical.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Parameter #1 $filename of method Varien_Simplexml_Element::asNiceXml() expects string, int given.',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/lib/Varien/Simplexml/Element.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/shell/abstract.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Construct empty() is not allowed. Use more strict comparison.',
	'identifier' => 'empty.notAllowed',
	'count' => 1,
	'path' => __DIR__ . '/shell/indexer.php',
];
$ignoreErrors[] = [
	'rawMessage' => 'Dead catch - PhpUnitsOfMeasure\\Exception\\UnknownUnitOfMeasure is never thrown in the try block.',
	'identifier' => 'catch.neverThrown',
	'count' => 2,
	'path' => __DIR__ . '/tests/unit/Mage/Usa/Helper/DataTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
