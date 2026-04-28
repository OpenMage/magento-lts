<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Admin_Model_Roles) of method Mage_Admin_Model_Resource_Roles::_afterDelete() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterDelete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Admin_Model_Roles) of method Mage_Admin_Model_Resource_Roles::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Admin_Model_Roles) of method Mage_Admin_Model_Resource_Roles::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Admin_Model_User) of method Mage_Admin_Model_Resource_User::_afterLoad() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Admin_Model_User) of method Mage_Admin_Model_Resource_User::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Admin_Model_User) of method Mage_Admin_Model_Resource_User::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Admin_Model_User) of method Mage_Admin_Model_Resource_User::delete() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::delete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Eav_Model_Entity_Attribute_Set) of method Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Attribute/Set/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $node (Varien_Data_Tree_Node) of method Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories::_getNodeJson() should be contravariant with parameter $node (array|Varien_Data_Tree_Node) of method Mage_Adminhtml_Block_Catalog_Category_Tree::_getNodeJson()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Categories.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $node (Varien_Data_Tree_Node) of method Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories::_isParentSelectedCategory() should be contravariant with parameter $node (mixed) of method Mage_Adminhtml_Block_Catalog_Category_Tree::_isParentSelectedCategory()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Categories.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $parentNodeCategory (Mage_Catalog_Model_Category|null) of method Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories::getRoot() should be contravariant with parameter $parentNodeCategory (mixed) of method Mage_Adminhtml_Block_Catalog_Category_Abstract::getRoot()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Categories.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $recursionLevel (int) of method Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories::getRoot() should be contravariant with parameter $recursionLevel (mixed) of method Mage_Adminhtml_Block_Catalog_Category_Abstract::getRoot()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Categories.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Catalog_Product_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $index (null) of method Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Price::getEscapedValue() should be contravariant with parameter $index (string|null) of method Varien_Data_Form_Element_Abstract::getEscapedValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Helper/Form/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_CatalogSearch_Model_Query) of method Mage_Adminhtml_Block_Catalog_Search_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Search/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Checkout_Model_Agreement) of method Mage_Adminhtml_Block_Checkout_Agreement_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Checkout/Agreement/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Cms_Model_Block) of method Mage_Adminhtml_Block_Cms_Block_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Cms/Block/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Cms_Model_Page) of method Mage_Adminhtml_Block_Cms_Page_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Cms/Page/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Quote_Item) of method Mage_Adminhtml_Block_Customer_Edit_Tab_Cart::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order) of method Mage_Adminhtml_Block_Customer_Edit_Tab_Orders::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Orders.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Customer_Model_Customer) of method Mage_Adminhtml_Block_Customer_Edit_Tab_Tag::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Tag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Quote_Item) of method Mage_Adminhtml_Block_Customer_Edit_Tab_View_Cart::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/View/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Wishlist_Model_Item) of method Mage_Adminhtml_Block_Customer_Edit_Tab_View_Wishlist::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/View/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Wishlist_Model_Item) of method Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Customer_Model_Customer) of method Mage_Adminhtml_Block_Customer_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Customer_Model_Group) of method Mage_Adminhtml_Block_Customer_Group_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Group/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Log_Model_Visitor_Online) of method Mage_Adminhtml_Block_Customer_Online_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Online/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order) of method Mage_Adminhtml_Block_Dashboard_Orders_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Orders/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_CatalogSearch_Model_Query) of method Mage_Adminhtml_Block_Dashboard_Searches_Last::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Searches/Last.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_CatalogSearch_Model_Query) of method Mage_Adminhtml_Block_Dashboard_Searches_Top::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Searches/Top.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order) of method Mage_Adminhtml_Block_Dashboard_Tab_Customers_Most::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Tab/Customers/Most.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Customer_Model_Customer) of method Mage_Adminhtml_Block_Dashboard_Tab_Customers_Newest::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Tab/Customers/Newest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Dashboard_Tab_Products_Ordered::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Tab/Products/Ordered.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Dashboard_Tab_Products_Viewed::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Tab/Products/Viewed.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Newsletter_Model_Queue) of method Mage_Adminhtml_Block_Newsletter_Queue_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Queue/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Newsletter_Model_Queue) of method Mage_Adminhtml_Block_Newsletter_Queue_Grid_Renderer_Action::render() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Queue/Grid/Renderer/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Newsletter_Model_Queue) of method Mage_Adminhtml_Block_Newsletter_Queue_Grid_Renderer_Action::render() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Queue/Grid/Renderer/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Newsletter_Model_Template) of method Mage_Adminhtml_Block_Newsletter_Template_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Template/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Newsletter_Model_Template) of method Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Action::render() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Template/Grid/Renderer/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Newsletter_Model_Template) of method Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Action::render() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Template/Grid/Renderer/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Admin_Model_Block) of method Mage_Adminhtml_Block_Permissions_Block_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/Block/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Admin_Model_Role) of method Mage_Adminhtml_Block_Permissions_Grid_Role::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/Grid/Role.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Admin_Model_User) of method Mage_Adminhtml_Block_Permissions_User_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/User/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Admin_Model_Variable) of method Mage_Adminhtml_Block_Permissions_Variable_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/Variable/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_CatalogRule_Model_Rule) of method Mage_Adminhtml_Block_Promo_Catalog_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Promo/Catalog/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_SalesRule_Model_Coupon) of method Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Coupons_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Promo/Quote/Edit/Tab/Coupons/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_SalesRule_Model_Rule) of method Mage_Adminhtml_Block_Promo_Quote_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Promo/Quote/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Rating_Model_Rating) of method Mage_Adminhtml_Block_Rating_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Rating/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Review_Model_Review) of method Mage_Adminhtml_Block_Report_Review_Customer_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Review/Customer/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Report_Review_Product_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Review/Product/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $collection (Mage_SalesRule_Model_Resource_Report_Collection) of method Mage_Adminhtml_Block_Report_Sales_Coupons_Grid::_addCustomFilter() should be contravariant with parameter $collection (Mage_Sales_Model_Resource_Report_Collection_Abstract) of method Mage_Adminhtml_Block_Report_Grid_Abstract::_addCustomFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Sales/Coupons/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_CatalogSearch_Model_Query) of method Mage_Adminhtml_Block_Report_Search_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Search/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Quote) of method Mage_Adminhtml_Block_Report_Shopcart_Abandoned_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Shopcart/Abandoned/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Quote) of method Mage_Adminhtml_Block_Report_Shopcart_Product_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Shopcart/Product/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Customer_Model_Customer) of method Mage_Adminhtml_Block_Report_Tag_Customer_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Tag/Customer/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Tag_Model_Tag) of method Mage_Adminhtml_Block_Report_Tag_Popular_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Tag/Popular/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Report_Tag_Product_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Tag/Product/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Review_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Review/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Review_Grid_Renderer_Type::render() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Review/Grid/Renderer/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Review_Grid_Renderer_Type::render() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Review/Grid/Renderer/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order_Creditmemo) of method Mage_Adminhtml_Block_Sales_Creditmemo_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Creditmemo/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order_Invoice) of method Mage_Adminhtml_Block_Sales_Invoice_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Invoice/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Customer_Model_Customer) of method Mage_Adminhtml_Block_Sales_Order_Create_Customer_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Customer/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Product::render() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Search/Grid/Renderer/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Product::render() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Search/Grid/Renderer/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item (Mage_Sales_Model_Quote_Item) of method Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Cart::getProductId() should be contravariant with parameter $item (mixed) of method Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract::getProductId()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Sidebar/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Pcompared::getIdentifierId() should be contravariant with parameter $item (Varien_Object) of method Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract::getIdentifierId()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Sidebar/Pcompared.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item (Mage_Wishlist_Model_Item) of method Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Wishlist::getProductId() should be contravariant with parameter $item (mixed) of method Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract::getProductId()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Sidebar/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order) of method Mage_Adminhtml_Block_Sales_Order_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $price (float) of method Mage_Adminhtml_Block_Sales_Order_Invoice_View_Items::formatPrice() should be contravariant with parameter $price (mixed) of method Mage_Adminhtml_Block_Sales_Items_Abstract::formatPrice()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Invoice/View/Items.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $price (float) of method Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Items::formatPrice() should be contravariant with parameter $price (mixed) of method Mage_Adminhtml_Block_Sales_Items_Abstract::formatPrice()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Shipment/Create/Items.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order_Status) of method Mage_Adminhtml_Block_Sales_Order_Status_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Status/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item (Mage_Sales_Model_Order_Item) of method Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default::displaySubtotalInclTax() should be contravariant with parameter $item (Varien_Object) of method Mage_Adminhtml_Block_Sales_Items_Abstract::displaySubtotalInclTax()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Items/Renderer/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order_Creditmemo) of method Mage_Adminhtml_Block_Sales_Order_View_Tab_Creditmemos::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Tab/Creditmemos.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order_Invoice) of method Mage_Adminhtml_Block_Sales_Order_View_Tab_Invoices::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Tab/Invoices.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order_Shipment) of method Mage_Adminhtml_Block_Sales_Order_View_Tab_Shipments::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Tab/Shipments.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order) of method Mage_Adminhtml_Block_Sales_Reorder_Renderer_Action::render() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Reorder/Renderer/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order) of method Mage_Adminhtml_Block_Sales_Reorder_Renderer_Action::render() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Reorder/Renderer/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Order_Shipment) of method Mage_Adminhtml_Block_Sales_Shipment_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Shipment/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sitemap_Model_Sitemap) of method Mage_Adminhtml_Block_Sitemap_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sitemap/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Core_Model_Design) of method Mage_Adminhtml_Block_System_Design_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Design/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Core_Model_Email_Template) of method Mage_Adminhtml_Block_System_Email_Template_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Email/Template/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Core_Model_Variable) of method Mage_Adminhtml_Block_System_Variable_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Variable/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Customer_Model_Customer) of method Mage_Adminhtml_Block_Tag_Customer_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tag/Customer/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Product) of method Mage_Adminhtml_Block_Tag_Product_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tag/Product/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Tag_Model_Tag) of method Mage_Adminhtml_Block_Tag_Tag_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tag/Tag/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Tax_Model_Class) of method Mage_Adminhtml_Block_Tax_Class_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tax/Class/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Tax_Model_Calculation_Rate) of method Mage_Adminhtml_Block_Tax_Rate_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tax/Rate/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Tax_Model_Calculation_Rule) of method Mage_Adminhtml_Block_Tax_Rule_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tax/Rule/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Core_Model_Url_Rewrite) of method Mage_Adminhtml_Block_Urlrewrite_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Urlrewrite/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $column (Mage_Adminhtml_Block_Widget_Grid_Column) of method Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract::setColumn() should be contravariant with parameter $column (mixed) of method Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Interface::setColumn()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $index (string) of method Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Datetime::getEscapedValue() should be contravariant with parameter $index (mixed) of method Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Date::getEscapedValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Datetime.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (string|Varien_Simplexml_Element) of method Mage_Adminhtml_Model_LayoutUpdate_Validator::isValid() should be contravariant with parameter $value (mixed) of method Mage_Core_Model_Validate_Interface::isValid()',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/LayoutUpdate/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $config (array|float|Varien_Object) of method Mage_Adminhtml_Model_Sales_Order_Create::addProduct() should be contravariant with parameter $requestInfo (mixed) of method Mage_Checkout_Model_Cart_Interface::addProduct()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $routePath (string) of method Mage_Adminhtml_Model_Url::getUrl() should be contravariant with parameter $routePath (string|null) of method Mage_Core_Model_Url::getUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $routeParams (array) of method Mage_Adminhtml_Model_Url::getUrl() should be contravariant with parameter $routeParams (array|null) of method Mage_Core_Model_Url::getUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $lifetime (int|false|null) of method Mage_Api_Model_Config::_saveCache() should be contravariant with parameter $lifetime (bool|int) of method Varien_Simplexml_Config::_saveCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Api_Model_Roles) of method Mage_Api_Model_Resource_Roles::_afterDelete() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterDelete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Api_Model_Roles) of method Mage_Api_Model_Resource_Roles::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Api_Model_Roles) of method Mage_Api_Model_Resource_Roles::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Api_Model_User) of method Mage_Api_Model_Resource_User::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Api_Model_User) of method Mage_Api_Model_Resource_User::delete() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::delete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $lifetime (int|false|null) of method Mage_Api_Model_Wsdl_Config::_saveCache() should be contravariant with parameter $lifetime (bool|int) of method Varien_Simplexml_Config::_saveCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $name (string) of method Mage_Api_Model_Wsdl_Config_Element::getAttribute() should be contravariant with parameter $name (mixed) of method Varien_Simplexml_Element::getAttribute()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Api2_Model_Acl_Global_Role) of method Mage_Api2_Block_Adminhtml_Roles_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Block/Adminhtml/Roles/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Api2_Model_Acl_Global_Role) of method Mage_Api2_Block_Adminhtml_Roles_Tab_Users::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Block/Adminhtml/Roles/Tab/Users.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $roleId (int) of method Mage_Api2_Model_Acl::addRole() should be compatible with parameter $role (string|Zend_Acl_Role_Interface) of method Zend_Acl::addRole()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $userType (string) of method Mage_Api2_Model_Acl_Filter_Attribute_ResourcePermission::setFilterValue() should be contravariant with parameter $filterValue (mixed) of method Mage_Api2_Model_Acl_PermissionInterface::setFilterValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Filter/Attribute/ResourcePermission.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $role (Mage_Api2_Model_Acl_Global_Role) of method Mage_Api2_Model_Acl_Global_Rule_ResourcePermission::setFilterValue() should be contravariant with parameter $filterValue (mixed) of method Mage_Api2_Model_Acl_PermissionInterface::setFilterValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global/Rule/ResourcePermission.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $request (Mage_Api2_Model_Request) of method Mage_Api2_Model_Route_Abstract::match() should be compatible with parameter $path (string) of method Zend_Controller_Router_Route::match()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Route/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $request (Mage_Api2_Model_Request) of method Mage_Api2_Model_Route_Abstract::match() should be contravariant with parameter $path (mixed) of method Zend_Controller_Router_Route_Interface::match()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Route/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $qty (float) of method Mage_Bundle_Model_Product_Price::_applyTierPrice() should be contravariant with parameter $qty (float|null) of method Mage_Catalog_Model_Product_Type_Price::_applyTierPrice()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product (Mage_Catalog_Model_Product) of method Mage_Bundle_Model_Product_Type::getForceChildItemQtyChanges() should be contravariant with parameter $product (Mage_Catalog_Model_Product|null) of method Mage_Catalog_Model_Product_Type_Abstract::getForceChildItemQtyChanges()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $product (Mage_Catalog_Model_Product) of method Mage_Bundle_Model_Product_Type::prepareQuoteItemQty() should be contravariant with parameter $product (Mage_Catalog_Model_Product|null) of method Mage_Catalog_Model_Product_Type_Abstract::prepareQuoteItemQty()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $collection (Mage_Core_Model_Resource_Db_Collection_Abstract) of method Mage_Catalog_Block_Product_Widget_Html_Pager::setCollection() should be contravariant with parameter $collection (Varien_Data_Collection) of method Mage_Page_Block_Html_Pager::setCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Widget/Html/Pager.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $category (Mage_Catalog_Model_Category) of method Mage_Catalog_Block_Seo_Sitemap_Category::getItemUrl() should be compatible with parameter $item (Mage_Catalog_Block_Seo_Sitemap_Abstract) of method Mage_Catalog_Block_Seo_Sitemap_Abstract::getItemUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Seo/Sitemap/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product (Mage_Catalog_Model_Product) of method Mage_Catalog_Block_Seo_Sitemap_Product::getItemUrl() should be compatible with parameter $item (Mage_Catalog_Block_Seo_Sitemap_Abstract) of method Mage_Catalog_Block_Seo_Sitemap_Abstract::getItemUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Seo/Sitemap/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $skipAdminCheck (bool) of method Mage_Catalog_Helper_Category_Flat::isEnabled() should be contravariant with parameter $deprecatedParam (mixed) of method Mage_Catalog_Helper_Flat_Abstract::isEnabled()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store (int|Mage_Core_Model_Store|string|null) of method Mage_Catalog_Helper_Product_Flat::isEnabled() should be contravariant with parameter $deprecatedParam (mixed) of method Mage_Catalog_Helper_Flat_Abstract::isEnabled()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $website (Mage_Core_Model_Website) of method Mage_Catalog_Model_Api2_Product_Website_Rest::_getLocation() should be contravariant with parameter $resource (Mage_Core_Model_Abstract) of method Mage_Api2_Model_Resource::_getLocation()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Website/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Catalog_Model_Attribute_Backend_Urlkey_Abstract::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Attribute/Backend/Urlkey/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Catalog_Model_Attribute_Backend_Urlkey_Abstract::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Attribute/Backend/Urlkey/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Catalog_Model_Category_Attribute_Backend_Image::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Attribute/Backend/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Catalog_Model_Category_Attribute_Backend_Sortby::afterLoad() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Attribute/Backend/Sortby.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Catalog_Model_Category_Attribute_Backend_Sortby::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Attribute/Backend/Sortby.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $filterBlock (Mage_Catalog_Block_Layer_Filter_Decimal) of method Mage_Catalog_Model_Layer_Filter_Decimal::apply() should be contravariant with parameter $filterBlock (Varien_Object) of method Mage_Catalog_Model_Layer_Filter_Abstract::apply()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Decimal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $attributes (stdClass) of method Mage_Catalog_Model_Product_Api_V2::info() should be compatible with parameter $attributes (array) of method Mage_Catalog_Model_Product_Api::info()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $store (int|string) of method Mage_Catalog_Model_Product_Api_V2::update() should be contravariant with parameter $store (int|string|null) of method Mage_Catalog_Model_Product_Api::update()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Catalog_Model_Product_Attribute_Backend_Boolean::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Boolean.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract::afterLoad() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract::afterLoad() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract::validate() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::validate()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Media::afterLoad() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Media::afterLoad() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Media::afterSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Media::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Media::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Media::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Media::validate() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::validate()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Msrp::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Catalog_Model_Product_Attribute_Backend_Boolean::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Msrp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Msrp::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Msrp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attribute (Mage_Catalog_Model_Resource_Eav_Attribute) of method Mage_Catalog_Model_Product_Attribute_Backend_Price::setAttribute() should be contravariant with parameter $attribute (Mage_Eav_Model_Entity_Attribute_Abstract) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::setAttribute()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Price::afterSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Price::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Recurring::_unserialize() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Serialized::_unserialize()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Recurring.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Recurring::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Serialized::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Recurring.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Recurring::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Recurring.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Sku::validate() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::validate()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Sku.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Product_Attribute_Backend_Startdate::validate() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::validate()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Startdate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Catalog_Model_Product_Attribute_Backend_Startdate::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Startdate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Catalog_Model_Product_Attribute_Backend_Startdate_Specialprice::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Startdate/Specialprice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data (stdClass) of method Mage_Catalog_Model_Product_Attribute_Media_Api_V2::_prepareImageData() should be compatible with parameter $data (array) of method Mage_Catalog_Model_Product_Attribute_Media_Api::_prepareImageData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Media/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $values (array<string, mixed>) of method Mage_Catalog_Model_Product_Option_Type_File::validateUserValue() should be contravariant with parameter $values (array) of method Mage_Catalog_Model_Product_Option_Type_Default::validateUserValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $productOptionValues (array<void>) of method Mage_Catalog_Model_Product_Option_Type_File::parseOptionValue() should be contravariant with parameter $productOptionValues (array) of method Mage_Catalog_Model_Product_Option_Type_Default::parseOptionValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attribute (Mage_Catalog_Model_Resource_Eav_Attribute) of method Mage_Catalog_Model_Resource_Abstract::_canUpdateAttribute() should be contravariant with parameter $attribute (Mage_Eav_Model_Entity_Attribute_Abstract) of method Mage_Eav_Model_Entity_Abstract::_canUpdateAttribute()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Abstract) of method Mage_Catalog_Model_Resource_Abstract::_setAttributeValue() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Abstract::_setAttributeValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute (Mage_Catalog_Model_Resource_Eav_Attribute) of method Mage_Catalog_Model_Resource_Abstract::_insertAttribute() should be contravariant with parameter $attribute (Mage_Eav_Model_Entity_Attribute_Abstract) of method Mage_Eav_Model_Entity_Abstract::_insertAttribute()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute (Mage_Catalog_Model_Resource_Eav_Attribute) of method Mage_Catalog_Model_Resource_Abstract::_isApplicableAttribute() should be contravariant with parameter $attribute (Mage_Eav_Model_Entity_Attribute_Abstract) of method Mage_Eav_Model_Entity_Abstract::_isApplicableAttribute()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $attribute (Mage_Catalog_Model_Resource_Eav_Attribute) of method Mage_Catalog_Model_Resource_Abstract::_updateAttribute() should be contravariant with parameter $attribute (Mage_Eav_Model_Entity_Attribute_Abstract) of method Mage_Eav_Model_Entity_Abstract::_updateAttribute()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Category) of method Mage_Catalog_Model_Resource_Category::_afterSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $typeId (int) of method Mage_Catalog_Model_Resource_Category_Attribute_Collection::setEntityTypeFilter() should be contravariant with parameter $type (int|Mage_Eav_Model_Entity_Type) of method Mage_Eav_Model_Resource_Entity_Attribute_Collection::setEntityTypeFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $node (Mage_Catalog_Model_Category) of method Mage_Catalog_Model_Resource_Category_Tree::move() should be contravariant with parameter $node (Varien_Object) of method Varien_Data_Tree_Dbp::move()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Resource_Product::_afterSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Resource_Product::_beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Image::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey::afterSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Urlkey.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Urlkey.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Urlkey.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Urlkey::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Urlkey.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $typeId (int) of method Mage_Catalog_Model_Resource_Product_Attribute_Collection::setEntityTypeFilter() should be contravariant with parameter $type (int|Mage_Eav_Model_Entity_Type) of method Mage_Eav_Model_Resource_Entity_Attribute_Collection::setEntityTypeFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_CatalogInventory_Model_Stock_Item) of method Mage_CatalogInventory_Model_Resource_Stock_Item::_prepareDataForTable() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Abstract::_prepareDataForTable()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Resource/Stock/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_CatalogInventory_Model_Stock_Item) of method Mage_CatalogInventory_Model_Resource_Stock_Item::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Resource/Stock/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $collection (Mage_CatalogSearch_Model_Resource_Fulltext_Collection) of method Mage_CatalogSearch_Model_Layer::prepareProductCollection() should be contravariant with parameter $collection (Mage_Catalog_Model_Resource_Product_Collection) of method Mage_Catalog_Model_Layer::prepareProductCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Layer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value (int|string) of method Mage_CatalogSearch_Model_Resource_Query::load() should be contravariant with parameter $value (mixed) of method Mage_Core_Model_Resource_Db_Abstract::load()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Query.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data (object) of method Mage_Checkout_Model_Cart_Customer_Api_V2::_prepareCustomerAddressData() should be compatible with parameter $data (array) of method Mage_Checkout_Model_Cart_Customer_Api::_prepareCustomerAddressData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Customer/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data (object) of method Mage_Checkout_Model_Cart_Customer_Api_V2::_prepareCustomerData() should be compatible with parameter $data (array) of method Mage_Checkout_Model_Cart_Customer_Api::_prepareCustomerData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Customer/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data (object) of method Mage_Checkout_Model_Cart_Payment_Api_V2::_preparePaymentData() should be compatible with parameter $data (array) of method Mage_Checkout_Model_Cart_Payment_Api::_preparePaymentData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Payment/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data (array|object) of method Mage_Checkout_Model_Cart_Product_Api_V2::_prepareProductsData() should be contravariant with parameter $data (mixed) of method Mage_Checkout_Model_Cart_Product_Api::_prepareProductsData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Product/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Core_Model_Abstract) of method Mage_Checkout_Model_Resource_Agreement::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Resource/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Cms_Model_Block) of method Mage_Cms_Model_Resource_Block::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Block.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Cms_Model_Page) of method Mage_Cms_Model_Resource_Page::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Cms_Model_Page) of method Mage_Cms_Model_Resource_Page::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Cms_Model_Page) of method Mage_Cms_Model_Resource_Page::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $coreRoute (string) of method Mage_Cms_IndexController::norouteAction() should be contravariant with parameter $coreRoute (mixed) of method Mage_Core_Controller_Varien_Action::norouteAction()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id (string) of method Mage_Core_Block_Html_Select::setId() should be contravariant with parameter $value (mixed) of method Varien_Object::setId()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Html/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value (array|string|null) of method Mage_Core_Block_Template_Zend::assign() should be contravariant with parameter $value (mixed) of method Mage_Core_Block_Template::assign()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Template/Zend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $raw (bool) of method Mage_Core_Controller_Request_Http::getBaseUrl() should be contravariant with parameter $raw (mixed) of method Zend_Controller_Request_Http::getBaseUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Request/Http.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $useRouterName (bool) of method Mage_Core_Controller_Varien_Router_Admin::collectRoutes() should be compatible with parameter $useRouterName (string) of method Mage_Core_Controller_Varien_Router_Standard::collectRoutes()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Admin.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $request (Mage_Core_Controller_Request_Http) of method Mage_Core_Controller_Varien_Router_Standard::match() should be contravariant with parameter $request (Zend_Controller_Request_Http) of method Mage_Core_Controller_Varien_Router_Abstract::match()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $lifetime (int|false) of method Mage_Core_Model_Config::_saveCache() should be contravariant with parameter $lifetime (bool|int) of method Varien_Simplexml_Config::_saveCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $type (string) of method Mage_Core_Model_Convert::getClassNameByType() should be contravariant with parameter $type (mixed) of method Mage_Dataflow_Model_Convert_Profile_Collection::getClassNameByType()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Convert.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (int) of method Mage_Core_Model_Email_Template::setId() should be contravariant with parameter $value (mixed) of method Mage_Core_Model_Abstract::setId()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $construction (array) of method Mage_Core_Model_Email_Template_Filter::varDirective() should be contravariant with parameter $construction (mixed) of method Varien_Filter_Template::varDirective()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (string) of method Mage_Core_Model_Email_Template_Filter::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter_Interface::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $default (string) of method Mage_Core_Model_Email_Template_Filter::_getVariable() should be contravariant with parameter $default (string|null) of method Varien_Filter_Template::_getVariable()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (string) of method Mage_Core_Model_File_Validator_AvailablePath::isValid() should be contravariant with parameter $value (mixed) of method Mage_Core_Model_Validate_Interface::isValid()',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Validator/AvailablePath.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (string) of method Mage_Core_Model_File_Validator_NotProtectedExtension::isValid() should be contravariant with parameter $value (mixed) of method Mage_Core_Model_Validate_Interface::isValid()',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Validator/NotProtectedExtension.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data (array) of method Mage_Core_Model_Input_Filter::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter_Interface::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Input/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (array|string|null) of method Mage_Core_Model_Input_Filter_MaliciousCode::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter_Interface::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Input/Filter/MaliciousCode.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (string|Varien_Simplexml_Element) of method Mage_Core_Model_Layout_Validator::isValid() should be contravariant with parameter $value (mixed) of method Mage_Core_Model_Validate_Interface::isValid()',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Config_Data) of method Mage_Core_Model_Resource_Config_Data::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Config/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Config_Data) of method Mage_Core_Model_Resource_Config_Data::_checkUnique() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_checkUnique()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Config/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Design) of method Mage_Core_Model_Resource_Design::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Design.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Email_Queue) of method Mage_Core_Model_Resource_Email_Queue::_afterLoad() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Email/Queue.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Email_Queue) of method Mage_Core_Model_Resource_Email_Queue::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Email/Queue.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Email_Template) of method Mage_Core_Model_Resource_Email_Template::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Email/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $model (Mage_Core_Model_Store) of method Mage_Core_Model_Resource_Store::_afterDelete() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterDelete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $model (Mage_Core_Model_Store) of method Mage_Core_Model_Resource_Store::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Store) of method Mage_Core_Model_Resource_Store::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Store::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $model (Mage_Core_Model_Store_Group) of method Mage_Core_Model_Resource_Store_Group::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Translate_String) of method Mage_Core_Model_Resource_Translate_String::_afterLoad() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Translate/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Translate_String) of method Mage_Core_Model_Resource_Translate_String::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Translate/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Translate_String) of method Mage_Core_Model_Resource_Translate_String::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Translate/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Translate_String) of method Mage_Core_Model_Resource_Translate_String::load() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::load()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Translate/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value (string) of method Mage_Core_Model_Resource_Translate_String::_getLoadSelect() should be contravariant with parameter $value (mixed) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Translate/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Translate_String::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Translate/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Core_Model_Url_Rewrite) of method Mage_Core_Model_Resource_Url_Rewrite::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Url/Rewrite.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Variable) of method Mage_Core_Model_Resource_Variable::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Variable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Core_Model_Variable) of method Mage_Core_Model_Resource_Variable::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Variable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $model (Mage_Core_Model_Website) of method Mage_Core_Model_Resource_Website::_afterDelete() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterDelete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Website) of method Mage_Core_Model_Resource_Website::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Website) of method Mage_Core_Model_Resource_Website::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $clear (bool) of method Mage_Core_Model_Session_Abstract_Varien::getData() should be compatible with parameter $index (int|string) of method Varien_Object::getData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Varien.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key (string) of method Mage_Core_Model_Session_Abstract_Zend::setData() should be contravariant with parameter $key (array|string) of method Varien_Object::setData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Zend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $clear (bool) of method Mage_Core_Model_Session_Abstract_Zend::getData() should be compatible with parameter $index (int|string) of method Varien_Object::getData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Zend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (string) of method Mage_Core_Model_Url_Validator::isValid() should be contravariant with parameter $value (mixed) of method Mage_Core_Model_Validate_Interface::isValid()',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $addressData (Varien_Object) of method Mage_Customer_Model_Address_Api_V2::create() should be contravariant with parameter $addressData (array|Varien_Object) of method Mage_Customer_Model_Address_Api::create()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $addressData (Varien_Object) of method Mage_Customer_Model_Address_Api_V2::update() should be contravariant with parameter $addressData (array|Varien_Object) of method Mage_Customer_Model_Address_Api::update()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customer (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Attribute_Backend_Data_Boolean::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Attribute/Backend/Data/Boolean.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $customer (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Attribute_Backend_Data_Boolean::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Attribute/Backend/Data/Boolean.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (array|string) of method Mage_Customer_Model_Attribute_Data_Postcode::validateValue() should be contravariant with parameter $value (bool|string|null) of method Mage_Eav_Model_Attribute_Data_Text::validateValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Attribute/Data/Postcode.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data (stdClass) of method Mage_Customer_Model_Customer_Api_V2::_prepareData() should be compatible with parameter $data (array) of method Mage_Customer_Model_Customer_Api::_prepareData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Billing::afterSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Billing::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Billing::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Billing::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Password::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Password.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Password::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Password.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Password::validate() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::validate()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Password.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Shipping::afterSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Shipping::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Shipping::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Shipping::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Store::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Customer_Attribute_Backend_Store::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Customer_Model_Customer_Attribute_Backend_Website::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Customer_Model_Resource_Address_Attribute_Backend_Region::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Address/Attribute/Backend/Region.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Address_Abstract) of method Mage_Customer_Model_Resource_Address_Attribute_Backend_Street::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Address/Attribute/Backend/Street.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Address_Abstract) of method Mage_Customer_Model_Resource_Address_Attribute_Backend_Street::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Address/Attribute/Backend/Street.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Resource_Customer::_afterSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Resource_Customer::_beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Customer_Model_Customer) of method Mage_Customer_Model_Resource_Customer::_getLoadRowSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Abstract::_getLoadRowSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id (string) of method Mage_Directory_Model_Currency::load() should be contravariant with parameter $id (int|string|null) of method Mage_Core_Model_Abstract::load()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $field (string) of method Mage_Directory_Model_Currency::load() should be contravariant with parameter $field (string|null) of method Mage_Core_Model_Abstract::load()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (float) of method Mage_Directory_Model_Currency_Filter::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter_Interface::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $idValue (string) of method Mage_Directory_Model_Resource_Country_Collection::getItemById() should be contravariant with parameter $idValue (mixed) of method Varien_Data_Collection<Mage_Directory_Model_Country>::getItemById()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Resource/Country/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Core_Model_Abstract) of method Mage_Directory_Model_Resource_Region::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Resource/Region.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $resource (object) of method Mage_Downloadable_Model_Link_Api_V2::add() should be compatible with parameter $resource (array) of method Mage_Downloadable_Model_Link_Api::add()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Link/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $store (int|string) of method Mage_Downloadable_Model_Link_Api_V2::add() should be contravariant with parameter $store (int|string|null) of method Mage_Downloadable_Model_Link_Api::add()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Link/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #5 $identifierType (string) of method Mage_Downloadable_Model_Link_Api_V2::add() should be contravariant with parameter $identifierType (string|null) of method Mage_Downloadable_Model_Link_Api::add()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Link/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $qty (int|null) of method Mage_Downloadable_Model_Product_Price::getFinalPrice() should be contravariant with parameter $qty (float|null) of method Mage_Catalog_Model_Product_Type_Price::getFinalPrice()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Resource_Eav_Attribute) of method Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Block/Adminhtml/Attribute/Grid/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (array|string) of method Mage_Eav_Model_Attribute_Data_Multiline::validateValue() should be contravariant with parameter $value (bool|string|null) of method Mage_Eav_Model_Attribute_Data_Text::validateValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Multiline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (bool|string|null) of method Mage_Eav_Model_Attribute_Data_Text::validateValue() should be contravariant with parameter $value (array|string) of method Mage_Eav_Model_Attribute_Data_Abstract::validateValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Text.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterDelete() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterDelete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterLoad() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeDelete() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeDelete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Array::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Array.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Datetime::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Datetime.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Increment::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Increment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Serialized::afterLoad() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Serialized.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Serialized::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Serialized.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Serialized::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Serialized.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Abstract) of method Mage_Eav_Model_Entity_Attribute_Backend_Time_Created::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Time/Created.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Core_Model_Abstract) of method Mage_Eav_Model_Entity_Attribute_Backend_Time_Created::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Time/Created.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Time_Created::afterLoad() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Time/Created.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Time_Updated::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Time/Updated.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Eav_Model_Attribute) of method Mage_Eav_Model_Resource_Attribute::_afterSave() should be contravariant with parameter $object (Mage_Eav_Model_Entity_Attribute) of method Mage_Eav_Model_Resource_Entity_Attribute::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Core_Model_Abstract) of method Mage_Eav_Model_Resource_Attribute::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $type (int) of method Mage_Eav_Model_Resource_Attribute_Collection::setEntityTypeFilter() should be contravariant with parameter $type (int|Mage_Eav_Model_Entity_Type) of method Mage_Eav_Model_Resource_Entity_Attribute_Collection::setEntityTypeFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Resource_Eav_Attribute) of method Mage_Eav_Model_Resource_Entity_Attribute::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Entity/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Eav_Model_Entity_Attribute) of method Mage_Eav_Model_Resource_Entity_Attribute::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Entity/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Eav_Model_Form_Element) of method Mage_Eav_Model_Resource_Form_Element::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Form/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Eav_Model_Form_Fieldset) of method Mage_Eav_Model_Resource_Form_Fieldset::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Form/Fieldset.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Eav_Model_Form_Fieldset) of method Mage_Eav_Model_Resource_Form_Fieldset::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Form/Fieldset.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Eav_Model_Form_Type) of method Mage_Eav_Model_Resource_Form_Type::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Form/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Eav_Model_Form_Type) of method Mage_Eav_Model_Resource_Form_Type::load() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::load()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Form/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_GiftMessage_Model_Entity_Attribute_Backend_Boolean_Config::afterLoad() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Model/Entity/Attribute/Backend/Boolean/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_GiftMessage_Model_Entity_Attribute_Backend_Boolean_Config::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Model/Entity/Attribute/Backend/Boolean/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $storeId (int|Mage_Core_Model_Store|string|null) of method Mage_GoogleCheckout_Model_Payment::getConfigData() should be contravariant with parameter $storeId (bool|int|Mage_Core_Model_Store|string|null) of method Mage_Payment_Model_Method_Abstract::getConfigData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GoogleCheckout/Model/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Catalog_Model_Resource_Eav_Attribute) of method Mage_ImportExport_Block_Adminhtml_Export_Filter::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Block/Adminhtml/Export/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Index_Model_Process) of method Mage_Index_Block_Adminhtml_Process_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Block/Adminhtml/Process/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Index_Model_Event) of method Mage_Index_Model_Resource_Event::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Resource/Event.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Index_Model_Event) of method Mage_Index_Model_Resource_Event::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Resource/Event.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Log_Model_Customer) of method Mage_Log_Model_Resource_Customer::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $field (string) of method Mage_Log_Model_Resource_Visitor_Online_Collection::addFieldToFilter() should be contravariant with parameter $field (array|string) of method Varien_Data_Collection_Db<Mage_Core_Model_Abstract>::addFieldToFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Visitor/Online/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition (array|string|null) of method Mage_Log_Model_Resource_Visitor_Online_Collection::addFieldToFilter() should be contravariant with parameter $condition (array|int|string|null) of method Varien_Data_Collection_Db<Mage_Core_Model_Abstract>::addFieldToFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Visitor/Online/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $queue (Mage_Newsletter_Model_Queue) of method Mage_Newsletter_Model_Resource_Queue::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Resource/Queue.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Newsletter_Model_Template) of method Mage_Newsletter_Model_Resource_Template::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Resource/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (int) of method Mage_Newsletter_Model_Subscriber::setId() should be contravariant with parameter $value (mixed) of method Mage_Core_Model_Abstract::setId()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Subscriber.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Oauth_Model_Consumer) of method Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Block/Adminhtml/Oauth/Consumer/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (string) of method Mage_Oauth_Model_Consumer_Validator_KeyLength::isValid() should be contravariant with parameter $value (mixed) of method Mage_Core_Model_Validate_Interface::isValid()',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Model/Consumer/Validator/KeyLength.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paygate_Model_Authorizenet::authorize() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::authorize()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paygate_Model_Authorizenet::cancel() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::cancel()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paygate_Model_Authorizenet::capture() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::capture()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paygate_Model_Authorizenet::refund() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::refund()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paygate_Model_Authorizenet::void() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::void()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Paypal_Model_Report_Settlement) of method Mage_Paypal_Block_Adminhtml_Settlement_Report_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Adminhtml/Settlement/Report/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Direct::acceptPayment() should be contravariant with parameter $payment (Mage_Payment_Model_Info) of method Mage_Payment_Model_Method_Abstract::acceptPayment()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Direct::authorize() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::authorize()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Direct::canReviewPayment() should be contravariant with parameter $payment (Mage_Payment_Model_Info) of method Mage_Payment_Model_Method_Abstract::canReviewPayment()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Direct::cancel() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::cancel()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Direct::capture() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::capture()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Direct::denyPayment() should be contravariant with parameter $payment (Mage_Payment_Model_Info) of method Mage_Payment_Model_Method_Abstract::denyPayment()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Direct::refund() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::refund()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Direct::void() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::void()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Express::acceptPayment() should be contravariant with parameter $payment (Mage_Payment_Model_Info) of method Mage_Payment_Model_Method_Abstract::acceptPayment()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Express::authorize() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::authorize()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Express::canReviewPayment() should be contravariant with parameter $payment (Mage_Payment_Model_Info) of method Mage_Payment_Model_Method_Abstract::canReviewPayment()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Express::cancel() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::cancel()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Express::capture() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::capture()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Express::denyPayment() should be contravariant with parameter $payment (Mage_Payment_Model_Info) of method Mage_Payment_Model_Method_Abstract::denyPayment()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Express::order() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::order()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Express::refund() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::refund()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Express::void() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::void()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $stateObject (Varien_Object) of method Mage_Paypal_Model_Hostedpro::initialize() should be contravariant with parameter $stateObject (object) of method Mage_Payment_Model_Method_Abstract::initialize()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Hostedpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Method_Agreement::acceptPayment() should be contravariant with parameter $payment (Mage_Payment_Model_Info) of method Mage_Payment_Model_Method_Abstract::acceptPayment()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Method_Agreement::authorize() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::authorize()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Method_Agreement::canReviewPayment() should be contravariant with parameter $payment (Mage_Payment_Model_Info) of method Mage_Payment_Model_Method_Abstract::canReviewPayment()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Method_Agreement::cancel() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::cancel()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Method_Agreement::capture() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::capture()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Method_Agreement::denyPayment() should be contravariant with parameter $payment (Mage_Payment_Model_Info) of method Mage_Payment_Model_Method_Abstract::denyPayment()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Method_Agreement::refund() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::refund()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Method_Agreement::void() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::void()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $stateObject (Varien_Object) of method Mage_Paypal_Model_Payflowlink::initialize() should be contravariant with parameter $stateObject (object) of method Mage_Payment_Model_Method_Abstract::initialize()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowlink.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Payflowpro::authorize() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::authorize()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Payflowpro::cancel() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::cancel()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Payflowpro::capture() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::capture()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Payflowpro::refund() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::refund()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $payment (Mage_Sales_Model_Order_Payment) of method Mage_Paypal_Model_Payflowpro::void() should be contravariant with parameter $payment (Varien_Object) of method Mage_Payment_Model_Method_Abstract::void()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $transaction (Mage_Paypal_Model_Payment_Transaction) of method Mage_Paypal_Model_Resource_Payment_Transaction::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Resource/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Paypal_Model_Report_Settlement) of method Mage_Paypal_Model_Resource_Report_Settlement::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Resource/Report/Settlement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $stateObject (Varien_Object) of method Mage_Paypal_Model_Standard::initialize() should be contravariant with parameter $stateObject (object) of method Mage_Payment_Model_Method_Abstract::initialize()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $quote (Mage_Sales_Model_Quote) of method Mage_PaypalUk_Model_Express::isAvailable() should be contravariant with parameter $quote (Mage_Sales_Model_Quote|null) of method Mage_Paypal_Model_Express::isAvailable()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/PaypalUk/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Persistent_Model_Session) of method Mage_Persistent_Model_Resource_Session::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Resource/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_ProductAlert_Model_Price) of method Mage_ProductAlert_Model_Resource_Price::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Model/Resource/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_ProductAlert_Model_Stock) of method Mage_ProductAlert_Model_Resource_Stock::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Model/Resource/Stock.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id (int) of method Mage_Rating_Model_Rating_Option::setId() should be contravariant with parameter $value (mixed) of method Mage_Core_Model_Abstract::setId()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Rating/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Rating_Model_Rating) of method Mage_Rating_Model_Resource_Rating::_afterDelete() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterDelete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Resource/Rating.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Rating_Model_Rating) of method Mage_Rating_Model_Resource_Rating::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Resource/Rating.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $field (string) of method Mage_Reports_Model_Resource_Product_Downloads_Collection::addFieldToFilter() should be contravariant with parameter $field (array|string) of method Mage_Eav_Model_Entity_Collection_Abstract<Mage_Catalog_Model_Product>::addFieldToFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Downloads/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition (string) of method Mage_Reports_Model_Resource_Product_Downloads_Collection::addFieldToFilter() should be contravariant with parameter $condition (array|int|string|null) of method Mage_Eav_Model_Entity_Collection_Abstract<Mage_Catalog_Model_Product>::addFieldToFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Downloads/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Core_Model_Abstract) of method Mage_Review_Model_Resource_Review::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $attribute (Mage_Eav_Model_Entity_Attribute_Abstract|string) of method Mage_Review_Model_Resource_Review_Product_Collection::addAttributeToFilter() should be contravariant with parameter $attribute (array|int|Mage_Eav_Model_Entity_Attribute_Interface|string) of method Mage_Catalog_Model_Resource_Product_Collection::addAttributeToFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $condition (array) of method Mage_Review_Model_Resource_Review_Product_Collection::addAttributeToFilter() should be contravariant with parameter $condition (array|int|string|null) of method Mage_Catalog_Model_Resource_Product_Collection::addAttributeToFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Core_Model_Abstract) of method Mage_Review_Model_Resource_Review_Summary::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Summary.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product (Mage_Catalog_Model_Product) of method Mage_Rss_Block_Wishlist::getProductUrl() should be contravariant with parameter $item (Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item) of method Mage_Wishlist_Block_Abstract::getProductUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Block/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $field (string) of method Mage_Rule_Model_Resource_Rule_Collection_Abstract::addFieldToFilter() should be contravariant with parameter $field (array|string) of method Varien_Data_Collection_Db<T of Mage_Rule_Model_Abstract>::addFieldToFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Resource/Rule/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Billing_Agreement) of method Mage_Sales_Block_Adminhtml_Billing_Agreement_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Adminhtml/Billing/Agreement/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Sales_Model_Recurring_Profile) of method Mage_Sales_Block_Adminhtml_Recurring_Profile_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Adminhtml/Recurring/Profile/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (float) of method Mage_Adminhtml_Block_Sales_Recurring_Profile_View_Items::formatPrice() should be contravariant with parameter $price (mixed) of method Mage_Adminhtml_Block_Sales_Items_Abstract::formatPrice()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Adminhtml/Recurring/Profile/View/Items.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Sales_Model_Order) of method Mage_Sales_Model_Entity_Order_Attribute_Backend_Billing::afterSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Sales_Model_Order) of method Mage_Sales_Model_Entity_Order_Attribute_Backend_Billing::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Sales_Model_Order) of method Mage_Sales_Model_Entity_Order_Attribute_Backend_Billing::beforeSave() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Sales_Model_Order) of method Mage_Sales_Model_Entity_Order_Attribute_Backend_Billing::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Attribute_Backend_Child::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Attribute_Backend_Parent::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Attribute/Backend/Parent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Attribute_Backend_Shipping::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Attribute/Backend/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Attribute_Backend_Shipping::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Attribute/Backend/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Creditmemo_Attribute_Backend_Child::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Creditmemo/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Creditmemo_Attribute_Backend_Parent::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Creditmemo/Attribute/Backend/Parent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Child::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Item::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice/Attribute/Backend/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Order::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice/Attribute/Backend/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Parent::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice/Attribute/Backend/Parent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Shipment_Attribute_Backend_Child::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Shipment/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Order_Shipment_Attribute_Backend_Parent::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Shipment/Attribute/Backend/Parent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $rowId (int) of method Mage_Sales_Model_Entity_Quote::_getLoadRowSelect() should be contravariant with parameter $rowId (mixed) of method Mage_Eav_Model_Entity_Abstract::_getLoadRowSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Child::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Address/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Parent::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Address/Attribute/Backend/Parent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Region::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote/Address/Attribute/Backend/Region.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data (object|null) of method Mage_Sales_Model_Order_Creditmemo_Api_V2::_prepareCreateData() should be contravariant with parameter $data (array|null) of method Mage_Sales_Model_Order_Creditmemo_Api::_prepareCreateData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filters (object|null) of method Mage_Sales_Model_Order_Creditmemo_Api_V2::_prepareListFilter() should be contravariant with parameter $filter (array|null) of method Mage_Sales_Model_Order_Creditmemo_Api::_prepareListFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $quote (Mage_Sales_Model_Quote) of method Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract::isAvailable() should be contravariant with parameter $quote (Mage_Sales_Model_Quote|null) of method Mage_Payment_Model_Method_Abstract::isAvailable()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Payment/Method/Billing/AgreementAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Attribute_Backend_Billing::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Attribute_Backend_Billing::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Attribute_Backend_Child::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Attribute_Backend_Parent::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Attribute/Backend/Parent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Attribute_Backend_Shipping::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Attribute/Backend/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Attribute_Backend_Shipping::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Attribute/Backend/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Creditmemo_Attribute_Backend_Child::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Creditmemo/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Creditmemo_Attribute_Backend_Parent::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Creditmemo/Attribute/Backend/Parent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Invoice_Attribute_Backend_Child::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Invoice/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Invoice_Attribute_Backend_Item::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Invoice/Attribute/Backend/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Invoice_Attribute_Backend_Order::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Invoice/Attribute/Backend/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Invoice_Attribute_Backend_Parent::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Invoice/Attribute/Backend/Parent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Shipment_Attribute_Backend_Child::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Shipment/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Order_Shipment_Attribute_Backend_Parent::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Shipment/Attribute/Backend/Parent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Sales_Model_Order|Mage_Sales_Model_Order_Status) of method Mage_Sales_Model_Resource_Order_Status::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Core_Model_Abstract) of method Mage_Sales_Model_Resource_Quote::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Quote_Address_Attribute_Backend_Child::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Quote/Address/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Quote_Address_Attribute_Backend_Parent::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Quote/Address/Attribute/Backend/Parent.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Sales_Model_Resource_Quote_Address_Attribute_Backend_Region::beforeSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Quote/Address/Attribute/Backend/Region.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_SalesRule_Model_Rule) of method Mage_SalesRule_Model_Resource_Rule::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Sales_Model_Quote_Address) of method Mage_SalesRule_Model_Rule_Condition_Address::validate() should be contravariant with parameter $object (Varien_Object) of method Mage_Rule_Model_Condition_Abstract::validate()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $store (array|int) of method Mage_Tag_Model_Resource_Product_Collection::addStoreFilter() should be contravariant with parameter $store (mixed) of method Mage_Catalog_Model_Resource_Product_Collection::addStoreFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Tag_Model_Tag) of method Mage_Tag_Model_Resource_Tag::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Tag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Tag_Model_Tag) of method Mage_Tag_Model_Resource_Tag::_beforeSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Tag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $object (Mage_Core_Model_Abstract) of method Mage_Tag_Model_Resource_Tag::_getLoadSelect() should be contravariant with parameter $object (Varien_Object) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Tag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $field (string) of method Mage_Tag_Model_Resource_Tag_Collection::addFieldToFilter() should be contravariant with parameter $field (array|string) of method Varien_Data_Collection_Db<Mage_Core_Model_Abstract>::addFieldToFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Tag/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $noEmpty (bool) of method Mage_Tax_Model_System_Config_Source_Tax_Country::toOptionArray() should be contravariant with parameter $isMultiselect (mixed) of method Mage_Adminhtml_Model_System_Config_Source_Country::toOptionArray()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/System/Config/Source/Tax/Country.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $request (Mage_Shipping_Model_Rate_Request) of method Mage_Usa_Model_Shipping_Carrier_Dhl::_doShipmentRequest() should be contravariant with parameter $request (Varien_Object) of method Mage_Usa_Model_Shipping_Carrier_Abstract::_doShipmentRequest()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Weee_Model_Attribute_Backend_Weee_Tax::afterLoad() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Weee_Model_Attribute_Backend_Weee_Tax::afterLoad() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Weee_Model_Attribute_Backend_Weee_Tax::afterSave() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Catalog_Model_Product) of method Mage_Weee_Model_Attribute_Backend_Weee_Tax::validate() should be contravariant with parameter $object (Varien_Object) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::validate()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Mage_Weee_Model_Attribute_Backend_Weee_Tax::afterDelete() should be contravariant with parameter $object (object) of method Mage_Eav_Model_Entity_Attribute_Backend_Interface::afterDelete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $row (Mage_Widget_Model_Widget_Instance) of method Mage_Widget_Block_Adminhtml_Widget_Instance_Grid::getRowUrl() should be contravariant with parameter $row (Varien_Object) of method Mage_Adminhtml_Block_Widget_Grid::getRowUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Block/Adminhtml/Widget/Instance/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Mage_Widget_Model_Widget_Instance) of method Mage_Widget_Model_Resource_Widget_Instance::_afterSave() should be contravariant with parameter $object (Mage_Core_Model_Abstract) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Model/Resource/Widget/Instance.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $item (Mage_Wishlist_Model_Item) of method Mage_Wishlist_Block_Share_Email_Items::hasDescription() should be contravariant with parameter $item (Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item) of method Mage_Wishlist_Block_Abstract::hasDescription()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Block/Share/Email/Items.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $product (Mage_Catalog_Model_Product) of method Mage_Wishlist_Block_Share_Email_Items::getProductUrl() should be contravariant with parameter $item (Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item) of method Mage_Wishlist_Block_Abstract::getProductUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Block/Share/Email/Items.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (array|float|int|string|Zend_Db_Expr|Zend_Db_Select|null) of method Magento_Db_Adapter_Pdo_Mysql::quote() should be contravariant with parameter $value (mixed) of method Zend_Db_Adapter_Abstract::quote()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $type (int|string|null) of method Magento_Db_Adapter_Pdo_Mysql::quote() should be contravariant with parameter $type (mixed) of method Zend_Db_Adapter_Abstract::quote()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $specificLifetime (bool|int) of method Varien_Cache_Core::save() should be contravariant with parameter $specificLifetime (int|false|null) of method Zend_Cache_Core::save()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Core.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value (array|string) of method Varien_Data_Collection_Filesystem::addFilter() should be contravariant with parameter $value (array|int|string) of method Varien_Data_Collection<Varien_Object>::addFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Filesystem.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $id (string) of method Varien_Data_Form_Element_Abstract::setId() should be contravariant with parameter $value (mixed) of method Varien_Object::setId()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $after (string|false) of method Varien_Data_Form_Element_Fieldset::addField() should be contravariant with parameter $after (mixed) of method Varien_Data_Form_Abstract::addField()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Fieldset.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $index (string) of method Varien_Data_Form_Element_Obscure::getEscapedValue() should be contravariant with parameter $index (string|null) of method Varien_Data_Form_Element_Abstract::getEscapedValue()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Obscure.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $data (array) of method Varien_Data_Tree_Db::appendChild() should be contravariant with parameter $data (array|Varien_Data_Tree_Node) of method Varien_Data_Tree::appendChild()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key (string) of method Varien_Data_Tree_Node_Collection::offsetExists() should be contravariant with parameter $offset (mixed) of method ArrayAccess<mixed,mixed>::offsetExists()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree/Node/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key (string) of method Varien_Data_Tree_Node_Collection::offsetGet() should be contravariant with parameter $offset (mixed) of method ArrayAccess<mixed,mixed>::offsetGet()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree/Node/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key (string) of method Varien_Data_Tree_Node_Collection::offsetSet() should be contravariant with parameter $offset (mixed) of method ArrayAccess<mixed,mixed>::offsetSet()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree/Node/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $key (string) of method Varien_Data_Tree_Node_Collection::offsetUnset() should be contravariant with parameter $offset (mixed) of method ArrayAccess<mixed,mixed>::offsetUnset()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree/Node/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value (string) of method Varien_Data_Tree_Node_Collection::offsetSet() should be contravariant with parameter $value (mixed) of method ArrayAccess<mixed,mixed>::offsetSet()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree/Node/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $date (string) of method Varien_Db_Adapter_Pdo_Mysql::getDateFormatSql() should be contravariant with parameter $date (string|Zend_Db_Expr) of method Varien_Db_Adapter_Interface::getDateFormatSql()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $sql (string|Zend_Db_Select) of method Varien_Db_Adapter_Pdo_Mysql::query() should be contravariant with parameter $sql (mixed) of method Varien_Db_Adapter_Interface::query()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $columns (array<int|string>) of method Varien_Db_Adapter_Pdo_Mysql::insertArray() should be contravariant with parameter $columns (array) of method Varien_Db_Adapter_Interface::insertArray()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value (array|float|int|string|Zend_Db_Expr|Zend_Db_Select|null) of method Varien_Db_Adapter_Pdo_Mysql::quoteInto() should be contravariant with parameter $value (mixed) of method Zend_Db_Adapter_Abstract::quoteInto()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $data (array<int, list>) of method Varien_Db_Adapter_Pdo_Mysql::insertArray() should be contravariant with parameter $data (array) of method Varien_Db_Adapter_Interface::insertArray()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $definition (array) of method Varien_Db_Adapter_Pdo_Mysql::modifyColumnByDdl() should be contravariant with parameter $definition (array|string) of method Varien_Db_Adapter_Interface::modifyColumnByDdl()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #4 $definition (array) of method Varien_Db_Adapter_Pdo_Mysql::changeColumn() should be contravariant with parameter $definition (array|string) of method Varien_Db_Adapter_Interface::changeColumn()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $value (array|float|int|string|Zend_Db_Expr|Zend_Db_Select|null) of method Varien_Db_Select::where() should be contravariant with parameter $value (mixed) of method Zend_Db_Select::where()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array (array) of method Varien_Filter_Array::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Array.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $array (array) of method Varien_Filter_Array::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter_Interface::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Array.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $grid (array) of method Varien_Filter_Array_Grid::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter_Interface::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Array/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (string) of method Varien_Filter_FormElementName::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter_Interface::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/FormElementName.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Varien_Filter_Object::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $object (Varien_Object) of method Varien_Filter_Object::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter_Interface::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $grid (array|Varien_Object) of method Varien_Filter_Object_Grid::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter_Interface::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Object/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (string) of method Varien_Filter_Template::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter_Interface::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $value (string) of method Varien_Filter_Template_Simple::filter() should be contravariant with parameter $value (mixed) of method Zend_Filter_Interface::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template/Simple.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $method (\'1.0\'|\'1.1\'|\'application/x-www…\'|\'basic\'|\'CONNECT\'|\'Content-Length\'|\'Content-Type\'|\'DELETE\'|\'FILE\'|\'GET\'|\'HEAD\'|\'MERGE\'|\'multipart/form-data\'|\'OPTIONS\'|\'PATCH\'|\'POST\'|\'PUT\'|\'SCALAR\'|\'TRACE\') of method Varien_Http_Adapter_Curl::write() should be contravariant with parameter $method (string) of method Zend_Http_Client_Adapter_Interface::write()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $fileName (string) of method Varien_Image_Adapter_Gd2::open() should be contravariant with parameter $fileName (mixed) of method Varien_Image_Adapter_Abstract::open()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $width (int) of method Varien_Image_Adapter_Gd2::resize() should be contravariant with parameter $width (mixed) of method Varien_Image_Adapter_Abstract::resize()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $height (int) of method Varien_Image_Adapter_Gd2::resize() should be contravariant with parameter $height (mixed) of method Varien_Image_Adapter_Abstract::resize()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dir (string) of method Varien_Io_File::cd() should be contravariant with parameter $dir (mixed) of method Varien_Io_Interface::cd()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dir (string) of method Varien_Io_File::mkdir() should be contravariant with parameter $dir (mixed) of method Varien_Io_Interface::mkdir()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dir (string) of method Varien_Io_File::rmdir() should be contravariant with parameter $dir (mixed) of method Varien_Io_Interface::rmdir()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename (string) of method Varien_Io_File::chmod() should be contravariant with parameter $filename (mixed) of method Varien_Io_Interface::chmod()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename (string) of method Varien_Io_File::read() should be contravariant with parameter $filename (mixed) of method Varien_Io_Interface::read()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename (string) of method Varien_Io_File::rm() should be contravariant with parameter $filename (mixed) of method Varien_Io_Interface::rm()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename (string) of method Varien_Io_File::write() should be contravariant with parameter $filename (mixed) of method Varien_Io_Interface::write()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $grep (Varien_Io_File) of method Varien_Io_File::ls() should be contravariant with parameter $grep (mixed) of method Varien_Io_Interface::ls()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $src (string) of method Varien_Io_File::mv() should be contravariant with parameter $src (mixed) of method Varien_Io_Interface::mv()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $dest (resource|string) of method Varien_Io_File::read() should be contravariant with parameter $dest (mixed) of method Varien_Io_Interface::read()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $dest (string) of method Varien_Io_File::mv() should be contravariant with parameter $dest (mixed) of method Varien_Io_Interface::mv()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $mode (int) of method Varien_Io_File::chmod() should be contravariant with parameter $mode (mixed) of method Varien_Io_Interface::chmod()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $mode (int) of method Varien_Io_File::mkdir() should be contravariant with parameter $mode (mixed) of method Varien_Io_Interface::mkdir()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $src (resource|string) of method Varien_Io_File::write() should be contravariant with parameter $src (mixed) of method Varien_Io_Interface::write()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $mode (int) of method Varien_Io_File::write() should be contravariant with parameter $mode (mixed) of method Varien_Io_Interface::write()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $recursive (bool) of method Varien_Io_File::mkdir() should be contravariant with parameter $recursive (mixed) of method Varien_Io_Interface::mkdir()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dir (string) of method Varien_Io_Ftp::cd() should be contravariant with parameter $dir (mixed) of method Varien_Io_Interface::cd()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dir (string) of method Varien_Io_Ftp::mkdir() should be contravariant with parameter $dir (mixed) of method Varien_Io_Interface::mkdir()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $dir (string) of method Varien_Io_Ftp::rmdir() should be contravariant with parameter $dir (mixed) of method Varien_Io_Interface::rmdir()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename (string) of method Varien_Io_Ftp::chmod() should be contravariant with parameter $filename (mixed) of method Varien_Io_Interface::chmod()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename (string) of method Varien_Io_Ftp::read() should be contravariant with parameter $filename (mixed) of method Varien_Io_Interface::read()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename (string) of method Varien_Io_Ftp::rm() should be contravariant with parameter $filename (mixed) of method Varien_Io_Interface::rm()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $filename (string) of method Varien_Io_Ftp::write() should be contravariant with parameter $filename (mixed) of method Varien_Io_Interface::write()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $src (string) of method Varien_Io_Ftp::mv() should be contravariant with parameter $src (mixed) of method Varien_Io_Interface::mv()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $dest (resource|string|null) of method Varien_Io_Ftp::read() should be contravariant with parameter $dest (mixed) of method Varien_Io_Interface::read()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $dest (string) of method Varien_Io_Ftp::mv() should be contravariant with parameter $dest (mixed) of method Varien_Io_Interface::mv()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $mode (int) of method Varien_Io_Ftp::chmod() should be contravariant with parameter $mode (mixed) of method Varien_Io_Interface::chmod()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $mode (int) of method Varien_Io_Ftp::mkdir() should be contravariant with parameter $mode (mixed) of method Varien_Io_Interface::mkdir()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $src (resource|string) of method Varien_Io_Ftp::write() should be contravariant with parameter $src (mixed) of method Varien_Io_Interface::write()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $recursive (bool) of method Varien_Io_Ftp::mkdir() should be contravariant with parameter $recursive (mixed) of method Varien_Io_Interface::mkdir()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $offset (string) of method Varien_Object::offsetExists() should be contravariant with parameter $offset (mixed) of method ArrayAccess<mixed,mixed>::offsetExists()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $offset (string) of method Varien_Object::offsetGet() should be contravariant with parameter $offset (mixed) of method ArrayAccess<mixed,mixed>::offsetGet()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $offset (string) of method Varien_Object::offsetSet() should be contravariant with parameter $offset (mixed) of method ArrayAccess<mixed,mixed>::offsetSet()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $offset (string) of method Varien_Object::offsetUnset() should be contravariant with parameter $offset (mixed) of method ArrayAccess<mixed,mixed>::offsetUnset()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tags (array<string>) of method Zend_Cache_Backend@anonymous/tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php:48::getIdsMatchingAnyTags() should be contravariant with parameter $tags (array) of method Zend_Cache_Backend_ExtendedInterface::getIdsMatchingAnyTags()',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tags (array<string>) of method Zend_Cache_Backend@anonymous/tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php:48::getIdsMatchingTags() should be contravariant with parameter $tags (array) of method Zend_Cache_Backend_ExtendedInterface::getIdsMatchingTags()',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 $tags (array<string>) of method Zend_Cache_Backend@anonymous/tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php:48::getIdsNotMatchingTags() should be contravariant with parameter $tags (array) of method Zend_Cache_Backend_ExtendedInterface::getIdsNotMatchingTags()',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #2 $tags (array<string>) of method Zend_Cache_Backend@anonymous/tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php:48::clean() should be contravariant with parameter $tags (array) of method Zend_Cache_Backend_Interface::clean()',
    'count' => 2,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #3 $tags (array<string>) of method Zend_Cache_Backend@anonymous/tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php:48::save() should be contravariant with parameter $tags (array) of method Zend_Cache_Backend_Interface::save()',
    'count' => 2,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
