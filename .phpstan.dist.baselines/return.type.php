<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage::exception() should return Mage_Core_Exception but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage::getControllerInstance() should return Mage_Core_Controller_Front_Action but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage::getEvents() should return Varien_Event_Collection but returns Varien_Event_Collection|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage::getRoot() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage::getScriptSystemUrl() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage::objects() should return Varien_Object_Cache but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Admin_Model_Roles::getResourcesList() should return array|Varien_Simplexml_Element but returns array|Varien_Simplexml_Element|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Admin_Model_Roles::getResourcesList2D() should return array|Varien_Simplexml_Element but returns array|Varien_Simplexml_Element|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Admin_Model_Roles::getResourcesTree() should return array|Varien_Simplexml_Element but returns array|Varien_Simplexml_Element|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main::_getSetId() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Attribute/Set/Main.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Configurable::getCurrentStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Composite/Fieldset/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Catalog_Product_Edit::getHeader() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Catalog_Product_Edit_Js::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Js.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Websites::getProductId() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Websites.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs::_translateHtml() should return string but returns array|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Customer_Edit::getHeaderText() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Customer_Edit_Tab_View::getBillingAddressHtml() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Customer_Edit_Tab_View::getCreateDate() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Customer_Online_Grid_Renderer_Ip::render() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Online/Grid/Renderer/Ip.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Dashboard_Graph::getChartData() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Graph.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Dashboard_Graph::getChartLabels() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Graph.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Dashboard_Searches_Renderer_Searchquery::render() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Dashboard/Searches/Renderer/Searchquery.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Newsletter_Queue_Edit::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Queue/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Newsletter_Template_Edit::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Template/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Page_Menu::_afterToHtml() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Page/Menu.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Page_Menu::_buildMenuArray() should return array<string, array{id?: string, children?: array, title?: string, label: string, sort_order: int, url: string, click?: \'return false\', active: bool, ...}> but returns array<string, array{id: non-falsy-string, label: string, sort_order: int, url: string, click?: \'return false\', active: bool, level: int, target?: Varien_Simplexml_Element, ...}|array{last: true}>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Page/Menu.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Promo_Widget_Chooser_Sku::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Promo/Widget/Chooser/Sku.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Report_Grid_Abstract::getCollection() should return Varien_Data_Collection but returns Varien_Data_Collection|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Grid/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Items_Column_Default::getCustomizedOptionValue() should return string but returns array<string, mixed>|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Items/Column/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Abstract::formatPrice() should return string but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Comment::getCommentNote() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Comment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Customer_Grid::getRowUrl() should return string but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Customer/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Data::getCurrencyName() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Data::getCurrencySymbol() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract::getForm() should return Varien_Data_Form but returns Varien_Data_Form|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Form/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Form_Address::getAddressAsString() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Form/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Giftmessage_Form::getMessage() should return Mage_GiftMessage_Model_Message but returns Mage_GiftMessage_Model_Message|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Giftmessage/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Cart::getProductId() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Sidebar/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Pcompared::getIdentifierId() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Sidebar/Pcompared.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Wishlist::getProductId() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Sidebar/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Create_Totals_Default::formatPrice() should return string but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Totals/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging::getContainerTypeByCode() should return string but returns string|void.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Shipment/Packaging.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging::getDeliveryConfirmationTypeByCode() should return string but returns string|void.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Shipment/Packaging.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Status_Grid::decorateState() should return string but returns array|float|int|string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Status/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_Totals_Tax::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Totals/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_View::getOrderId() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_View_Giftmessage::getEntity() should return Mage_Sales_Model_Order but returns Mage_Sales_Model_Order|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Giftmessage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_View_Giftmessage::getMessage() should return Mage_GiftMessage_Model_Message but returns Mage_GiftMessage_Model_Message|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Giftmessage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_View_Tab_History::getItemComment() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Tab/History.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Order_View_Tab_History::getItemTitle() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Tab/History.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Shipping_Carrier_Tablerate_Grid::getWebsiteId() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Shipping/Carrier/Tablerate/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sitemap_Grid_Renderer_Link::render() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sitemap/Grid/Renderer/Link.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_System_Convert_Gui_Edit::getHeaderText() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Convert/Gui/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard::getValue() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Convert/Gui/Edit/Tab/Wizard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_System_Convert_Profile_Edit::getHeaderText() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Convert/Profile/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Tag_Assigned_Grid::_getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tag/Assigned/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Template::maliciousCodeFilter() should return string but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Urlrewrite_Category_Tree::getTreeArray() should return array|string but returns array<array<mixed>|bool|int|string|null>|int<min, -1>|int<1, max>|string|true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Urlrewrite/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid::_getFileContainerContent() should return string but returns bool|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid::getCollection() should return Varien_Data_Collection but returns Varien_Data_Collection|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid::getEmptyText() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid::getEmptyTextClass() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Datetime::getEscapedValue() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Datetime.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Datetime::getEscapedValue() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Datetime.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::renderHeader() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Country::render() should return string|null but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Country.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Ip::render() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Ip.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Longtext::render() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Longtext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options::render() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Options.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options::render() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Options.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Theme::render() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Theme.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Grid_Massaction_Item::getAdditionalActionBlock() should return Mage_Core_Block_Abstract but returns array<Mage_Core_Block_Abstract>|Mage_Core_Block_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Massaction/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Tabs::getTabLabel() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Widget_Tabs::getTabLabel() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Model_Config_Data::getConfigRoot() should return Mage_Core_Model_Config_Element but returns Mage_Core_Model_Config_Element|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Config/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Model_Giftmessage_Save::_getMappedType() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Giftmessage/Save.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Model_Sales_Order_Create::_getCustomerAddressForm() should return Mage_Customer_Model_Form but returns bool|Mage_Customer_Model_Form.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Model_Sales_Order_Create::_getCustomerForm() should return Mage_Customer_Model_Form but returns bool|Mage_Customer_Model_Form.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Model_Sales_Order_Create::_getQuoteItem() should return Mage_Sales_Model_Quote_Item|false but returns Mage_Sales_Model_Quote_Item|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Model_Sales_Order_Create::getCustomerCompareList() should return Mage_Catalog_Model_Product_Compare_List but returns Mage_Catalog_Model_Product_Compare_List|false.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Model_Sales_Order_Create::getCustomerWishlist() should return Mage_Wishlist_Model_Wishlist but returns Mage_Wishlist_Model_Wishlist|false.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Model_Session_Quote::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Session/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Model_System_Config_Source_Log_Level::toOptionArray() should return array<int, string> but returns array<int|string, string>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Source/Log/Level.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_IndexController::_getModel() should return Mage_Core_Model_Abstract|false but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Tax_RuleController::_getSingletonModel() should return Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Tax/RuleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api_Model_User::hasAssigned2Role() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Config::getResourceAttributes() should return array but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Config::getResourceGroup() should return bool|Mage_Core_Model_Config_Element but returns Varien_Simplexml_Element.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Config::getResourceSubresources() should return array but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Config::getResourceUserPrivileges() should return array but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Config::getValidationConfig() should return array but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Renderer::factory() should return Mage_Core_Model_Abstract|false but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Renderer_Xml_Writer::render() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Renderer/Xml/Writer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Request::getBodyParams() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Request::getVersion() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Request_Internal::getBodyParams() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Request/Internal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Request_Interpreter::factory() should return Mage_Core_Model_Abstract|false but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Request/Interpreter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Request_Interpreter_Query::_validateQuery() should return bool but returns int|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Request/Interpreter/Query.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Api2_Model_Resource::getWorkingModel() should return Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Adminhtml/Catalog/Product/Edit/Tab/Bundle/Option/Search/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Block_Adminhtml_Sales_Order_Items_Renderer::getChilds() should return array but returns array<Mage_Sales_Model_Order_Creditmemo_Item|Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item>|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Adminhtml/Sales/Order/Items/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option::_getSelectedQty() should return int but returns float|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Catalog/Product/View/Type/Bundle/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option::formatPriceString() should return string but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Catalog/Product/View/Type/Bundle/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option::getSelectionPrice() should return float|int but returns float|int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Catalog/Product/View/Type/Bundle/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Block_Sales_Order_Items_Renderer::getChilds() should return array but returns array<Mage_Sales_Model_Order_Creditmemo_Item|Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item>|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Sales/Order/Items/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Block_Sales_Order_Items_Renderer::getValueHtml() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Sales/Order/Items/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Model_Product_Price::calculatePrice() should return float but returns float|int|true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Model_Product_Price::getOptions() should return Mage_Bundle_Model_Resource_Option_Collection but returns array.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Model_Resource_Bundle::getSelectionsData() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Bundle.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Model_Resource_Price_Index::_getBasePrice() should return float but returns float|true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract::getChilds() should return array but returns array<Mage_Sales_Model_Order_Creditmemo_Item|Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Shipment_Item>|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Sales/Order/Pdf/Items/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Captcha_Model_Resource_Log::countAttemptsByRemoteAddress() should return int|string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/Model/Resource/Log.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Captcha_Model_Resource_Log::countAttemptsByUserLogin() should return int|string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/Model/Resource/Log.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Captcha_Model_Zend::randomSize() should return int but returns float|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/Model/Zend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Block_Navigation::getStoreCategories() should return Varien_Data_Tree_Node_Collection but returns array|Varien_Data_Collection|Varien_Data_Tree_Node_Collection.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Navigation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Block_Product_Abstract::_getSingletonModel() should return Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Block_Product_Abstract::getAddToWishlistUrlCustom() should return string but returns bool|string.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Block_Product_Abstract::getImageLabel() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Block_Product_Abstract::getPageLayout() should return Varien_Object but returns Varien_Object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Block_Product_Compare_List::getAddToWishlistUrlCustom() should return string but returns bool|string.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Compare/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Block_Product_List::_getProductCollection() should return Mage_Catalog_Model_Resource_Product_Collection but returns Mage_Catalog_Model_Resource_Product_Collection|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Block_Product_List::getToolbarBlock() should return Mage_Core_Block_Abstract but returns Mage_Core_Block_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Block_Product_Widget_Html_Pager::getCurrentPage() should return int but returns float|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Widget/Html/Pager.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Block_Product_Widget_Html_Pager::getLimit() should return int but returns int|string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Widget/Html/Pager.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Block_Seo_Sitemap_Product::getItemUrl() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Seo/Sitemap/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Helper_Category::getCategoryUrlPath() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Helper_Data::getMsrpExplanationMessage() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Helper_Data::getMsrpExplanationMessageWhatsThis() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Helper_Image::_getModel() should return Mage_Catalog_Model_Product_Image but returns Mage_Catalog_Model_Product_Image|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Helper_Image::getAngle() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Helper_Image::getImageFile() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Helper_Image::getProduct() should return Mage_Catalog_Model_Product but returns Mage_Catalog_Model_Product|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Helper_Image::getWatermark() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Helper_Image::getWatermarkPosition() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Helper_Image::getWatermarkSize() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Helper_Product_Type_Composite::getCurrentStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product/Type/Composite.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Abstract::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Api_Resource::_getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Category::_getAttribute() should return Mage_Eav_Model_Entity_Attribute_Abstract but returns Mage_Eav_Model_Entity_Attribute_Abstract|false.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Category::checkId() should return bool but returns bool|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Category::getChildrenCategories() should return Mage_Catalog_Model_Resource_Category_Collection but returns array|Mage_Catalog_Model_Resource_Category_Collection.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Category::getChildrenCategoriesWithInactive() should return Mage_Catalog_Model_Resource_Category_Collection but returns array|Mage_Catalog_Model_Resource_Category_Collection.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Category::getDefaultAttributeSetId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Category::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Category::getTreeModelInstance() should return Mage_Catalog_Model_Resource_Category_Tree but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Category_Api::_getProductId() should return int but returns int<min, -1>|int<1, max>|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Category_Api::create() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Category_Api_V2::create() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Category_Indexer_Flat::_getIndexer() should return Mage_Catalog_Model_Resource_Category_Flat but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Indexer/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Convert_Adapter_Product::getStoreByCode() should return Mage_Core_Model_Store|false but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Convert_Adapter_Product::getStoreById() should return Mage_Core_Model_Store|false but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Convert_Parser_Product::getProductTypeId() should return string|false but returns int<min, -1>|int<1, max>|non-falsy-string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Parser/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Layer::getCurrentStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Layer_Filter_Abstract::getItems() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Layer_Filter_Abstract::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Layer_Filter_Category::_isValidCategory() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Layer_Filter_Item::getFilter() should return Mage_Catalog_Model_Layer_Filter_Abstract but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Layer_Filter_Price::_renderRangeLabel() should return string but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Layer_Filter_Price_Algorithm::_findPriceSeparator() should return array|null but returns array|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product::getAttributeText() should return string but returns bool|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product::getCacheIdTagsWithCategories() should return array but returns array|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product::getDefaultAttributeSetId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product::getStockItem() should return Mage_CatalogInventory_Model_Stock_Item but returns Mage_CatalogInventory_Model_Stock_Item|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product::getTypeInstance() should return Mage_Catalog_Model_Product_Type_Abstract but returns Mage_Catalog_Model_Product_Type_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Api::create() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Api_V2::create() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Attribute_Backend_Groupprice::_getResource() should return Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Attribute_Backend_Sku::validate() should return bool but returns $this(Mage_Catalog_Model_Product_Attribute_Backend_Sku)|bool.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Sku.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Attribute_Backend_Tierprice::_getResource() should return Mage_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Tierprice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Option_Type_File::_isImage() should return bool but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Option_Type_Select::getFormattedOptionValue() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Option_Type_Text::getFormattedOptionValue() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/Text.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Status::getOptionText() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Type::factory() should return Mage_Catalog_Model_Product_Type_Abstract|false but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Type::priceFactory() should return Mage_Catalog_Model_Product_Type_Price|Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Type_Price::calculatePrice() should return float but returns float|int|true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Url::_getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Product_Visibility::getOptionText() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Visibility.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Category::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Category_Collection::getNewEmptyItem() should return Mage_Catalog_Model_Category but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Category_Flat::_getAttributeTypeValues() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Category_Flat::checkId() should return bool but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Category_Flat::getAttribute() should return Mage_Eav_Model_Entity_Attribute_Abstract but returns Mage_Eav_Model_Entity_Attribute_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Category_Flat_Collection::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Category_Indexer_Product::_getStoresInfo() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Indexer/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Category_Tree::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Collection_Abstract::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Config::getAttributesUsedForSortBy() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Config::getAttributesUsedInListing() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Config::getEntityTypeId() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Config::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Layer_Filter_Price::_getPriceExpression() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice_Abstract::loadPriceData() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Collection::getAdditionalPriceExpression() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Collection::getMaxPrice() should return float but returns float|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Collection::getMinPrice() should return float but returns float|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Collection::getPriceExpression() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Collection::getPriceStandardDeviation() should return float but returns float|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Collection::getPricesCount() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Compare_Item_Collection::getComparableAttributes() should return array<Mage_Eav_Model_Entity_Attribute_Abstract> but returns array<Mage_Eav_Model_Entity_Attribute_Abstract|false>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Compare/Item/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Flat::getAttribute() should return Mage_Eav_Model_Entity_Attribute_Abstract but returns Mage_Eav_Model_Entity_Attribute_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Flat::getTypeId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Indexer_Abstract::_getAttribute() should return Mage_Catalog_Model_Resource_Eav_Attribute but returns Mage_Eav_Model_Entity_Attribute_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Link::getAttributesByType() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Link.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Type_Configurable::getChildrenIds() should return array<int, non-empty-array> but returns array{array}.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_Model_Resource_Product_Website::_getProductResource() should return Mage_Catalog_Model_Resource_Product but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Catalog_ProductController::_initProduct() should return Mage_Catalog_Model_Product but returns Mage_Catalog_Model_Product|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/controllers/ProductController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogIndex_Model_Indexer::getRetreiver() should return Mage_CatalogIndex_Model_Data_Abstract but returns Mage_CatalogIndex_Model_Data_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogIndex_Model_Resource_Data_Abstract::getMinimalPrice() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogIndex_Model_Resource_Data_Abstract::getTierPrices() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogIndex_Model_Resource_Indexer_Minimalprice::getMinimalValue() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Indexer/Minimalprice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogIndex_Model_Resource_Price::getMinimalPrices() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogIndex_Model_Resource_Retreiver::getProductTypes() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Retreiver.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogInventory_Block_Qtyincrements::getProductName() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Block/Qtyincrements.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogInventory_Model_Api2_Stock_Item_Rest::_retrieveCollection() should return array but returns array<int<0, max>|string, array|int>|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Api2/Stock/Item/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogInventory_Model_Resource_Stock::getProductsStock() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Resource/Stock.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogInventory_Model_Resource_Stock_Item_Collection::_initSelect() should return $this(Mage_CatalogInventory_Model_Resource_Stock_Item_Collection) but returns Varien_Db_Select.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Resource/Stock/Item/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogInventory_Model_Stock_Item::getCustomerGroupId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogRule_Model_Resource_Rule::_getRuleProductsStmt() should return Zend_Db_Statement_Interface but returns PDOStatement|Zend_Db_Statement_Interface.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogRule_Model_Resource_Rule::getRulesForProduct() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogRule_Model_Resource_Rule::getRulesFromProduct() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogSearch_Helper_Data::getEscapedQueryText() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogSearch_Model_Advanced::getProductCollection() should return Mage_CatalogSearch_Model_Resource_Advanced_Collection but returns array|float|int|string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Advanced.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogSearch_Model_Indexer_Fulltext::_getResource() should return Mage_CatalogSearch_Model_Resource_Indexer_Fulltext but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Indexer/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogSearch_Model_Query::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Query.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogSearch_Model_Resource_Fulltext::_getSearchableAttribute() should return Mage_Eav_Model_Entity_Attribute_Abstract but returns Mage_Eav_Model_Entity_Attribute_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_CatalogSearch_Model_Resource_Fulltext::_getSearchableProducts() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Centinel_Helper_Data::getMethodFormBlock() should return Mage_Core_Block_Abstract|false but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Centinel/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Centinel_Model_Config::getStore() should return int|Mage_Core_Model_Store but returns int|Mage_Core_Model_Store|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Centinel/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Centinel_Model_Service::_initValidationState() should return Mage_Centinel_Model_StateAbstract but returns Mage_Centinel_Model_StateAbstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Centinel/Model/Service.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Centinel_Model_StateAbstract::getDataStorage() should return Varien_Object but returns Varien_Object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Centinel/Model/StateAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Block_Cart_Item_Renderer::getProductAdditionalInformationBlock() should return Mage_Core_Block_Abstract but returns Mage_Core_Block_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Cart/Item/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Block_Cart_Item_Renderer::getProductName() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Cart/Item/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Block_Cart_Item_Renderer_Configurable::getProductName() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Cart/Item/Renderer/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Block_Multishipping_Addresses::getItems() should return array but returns array<mixed>|Varien_Object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Multishipping/Addresses.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Block_Multishipping_Overview::formatPrice() should return string but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Multishipping/Overview.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Block_Multishipping_Shipping::getAddressItems() should return array<Mage_Sales_Model_Quote_Address_Item> but returns array<mixed>|Varien_Object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Multishipping/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Block_Total_Nominal::formatPrice() should return string but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Total/Nominal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Helper_Data::formatPrice() should return string but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Model_Api_Resource::_getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Api/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Model_Cart::_getResource() should return Mage_Checkout_Model_Resource_Cart but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Model_Cart::getSummaryQty() should return float|int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Model_Cart_Product_Api_V2::_prepareProductsData() should return array but returns array|null.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Product/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_Model_Resource_Cart::fetchItems() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Resource/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Checkout_OnepageController::_getAdditionalHtml() should return string but returns array|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/OnepageController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Cms_Helper_Wysiwyg_Images::convertIdToPath() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Helper/Wysiwyg/Images.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Cms_Model_Page::getCmsPageIdentifierById() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Cms_Model_Resource_Page::checkIdentifier() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Cms_Model_Resource_Page::getCmsPageIdentifierById() should return string|false but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Cms_Model_Resource_Page::getCmsPageTitleById() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Cms_Model_Resource_Page::getCmsPageTitleByIdentifier() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Cms_Model_Resource_Page::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Cms_Model_Wysiwyg_Images_Storage::getConfig() should return Mage_Core_Model_Config_Element but returns Mage_Core_Model_Config_Element|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Cms_Model_Wysiwyg_Images_Storage::resizeOnTheFly() should return string|false but returns bool|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Block_Abstract::getHelper() should return Mage_Core_Block_Abstract but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Block_Abstract::getSortedChildBlocks() should return array<Mage_Core_Block_Abstract> but returns array<Mage_Core_Block_Abstract|false>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Block_Abstract::helper() should return Mage_Core_Helper_Abstract but returns Mage_Core_Helper_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Block_Store_Switcher::getCurrentStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Store/Switcher.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Block_Template::fetchView() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Controller_Varien_Front::getRouterByFrontName() should return Mage_Core_Controller_Varien_Router_Standard|false but returns Mage_Core_Controller_Varien_Router_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Front.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Controller_Varien_Front::getRouterByRoute() should return Mage_Core_Controller_Varien_Router_Abstract but returns Mage_Core_Controller_Varien_Router_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Front.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Controller_Varien_Router_Default::_getNoRouteConfig() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Abstract::stripTags() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Cookie::getAcceptedSaveCookiesWebsiteIds() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Cookie.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Data::currencyByStore() should return float but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Data::formatPrice() should return string but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Data::formatTime() should return string but returns string|Zend_Date|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Data::formatTimezoneDate() should return string but returns int|string|Zend_Date|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Data::getEncryptor() should return Mage_Core_Model_Encryption but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Data::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Data::jsonEncode() should return string but returns array|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_EnvironmentConfigLoader::getAsArray() should return array<string, string> but returns array<string, int|string>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/EnvironmentConfigLoader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_File_Storage::getStorageModel() should return Mage_Core_Model_File_Storage_Database|Mage_Core_Model_File_Storage_File but returns Mage_Core_Model_File_Storage_Database|Mage_Core_Model_File_Storage_File|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/File/Storage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Http::validateIpAddr() should return bool but returns int|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Http.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Log::getHandler() should return Monolog\\Handler\\HandlerInterface but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Log.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Log::getLogLevelMaxValue() should return int but returns int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Log.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Purifier::purify() should return T of array<string>|string but returns list<string>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Purifier.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Purifier::purify() should return T of array<string>|string but returns string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Purifier.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_String::cleanString() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_String::strlen() should return int but returns int<0, max>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_String::substr() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Translate::apply() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Translate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Translate::apply() should return string but returns string|true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Translate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Helper_Url::_getSingletonModel() should return Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Abstract::getResource() should return Mage_Core_Model_Resource_Db_Abstract but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_App::_getCacheId() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_App::getSafeStore() should return Varien_Object but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_App::getWebsite() should return Mage_Core_Model_Website but returns Mage_Core_Model_Website|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_App::prepareCacheId() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_App::useCache() should return array|false but returns array|bool.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Cache::_getResource() should return Mage_Core_Model_Resource_Cache but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cache.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Cache::canUse() should return array|bool but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cache.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Cache::getDbAdapter() should return Varien_Db_Adapter_Interface but returns Varien_Db_Adapter_Interface|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cache.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Config::getModuleConfig() should return Mage_Core_Model_Config_Element but returns Mage_Core_Model_Config_Element|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Config::getNode() should return Mage_Core_Model_Config_Element|false but returns Varien_Simplexml_Element|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Config::getResourceHelper() should return Mage_Core_Model_Resource_Helper_Abstract|false but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Config::getResourceModelInstance() should return Mage_Core_Model_Resource_Db_Collection_Abstract|false but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Config::getSectionNode() should return Mage_Core_Model_Config_Element|false but returns Varien_Simplexml_Element|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Config_Data::getOldValue() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Cookie::getDomain() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cookie.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Cookie::getLifetime() should return int|string but returns float|int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cookie.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Cookie::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cookie.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Date::_getConfigTimezone() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Design_Fallback::_getFallbackTheme() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Fallback.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Design_Fallback::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Fallback.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Design_Package::_checkUserAgentAgainstRegexps() should return string|false but returns bool|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Design_Package::_initMergerDir() should return bool but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Design_Package::beforeMergeCss() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Design_Package::getStore() should return int|Mage_Core_Model_Store|string but returns int|Mage_Core_Model_Store|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Design_Source_Design::getOptionText() should return string but returns int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Source/Design.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Email_Template_Filter::htmlescapeDirective() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Encryption::getHash() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Encryption.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Encryption::getHashPassword() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Encryption.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Factory::getModel() should return bool|Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Factory.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Factory::getResourceModel() should return object but returns Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Factory.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Factory::getSingleton() should return Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Factory.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Input_Filter::_getFiltrationHelper() should return Mage_Core_Helper_Abstract but returns Mage_Core_Helper_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Input/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Input_Filter_MaliciousCode::linkFilter() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Input/Filter/MaliciousCode.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Layout::addBlock() should return Mage_Core_Block_Abstract but returns Mage_Core_Block_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Layout_Update::asSimplexml() should return SimpleXMLElement but returns SimpleXMLElement|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout/Update.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Layout_Update::getFileLayoutUpdatesXml() should return SimpleXMLElement but returns SimpleXMLElement|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout/Update.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Locale::getDateFormat() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Locale::getDateFormatWithLongYear() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Locale::getLocale() should return Zend_Locale but returns Zend_Locale|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Locale::getTimeFormat() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource::createConnection() should return Varien_Db_Adapter_Interface but returns Varien_Db_Adapter_Interface|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Abstract::mktime() should return int but returns int|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Config::getConfig() should return string|false but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Db_Abstract::_getConnection() should return Magento_Db_Adapter_Pdo_Mysql|false but returns Varien_Db_Adapter_Interface|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Db_Abstract::_getReadAdapter() should return Magento_Db_Adapter_Pdo_Mysql but returns Magento_Db_Adapter_Pdo_Mysql|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Db_Abstract::_getWriteAdapter() should return Magento_Db_Adapter_Pdo_Mysql but returns Magento_Db_Adapter_Pdo_Mysql|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Db_Abstract::getUniqueFields() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Db_Collection_Abstract::_getInitialFieldsToSelect() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Db_Collection_Abstract::formatDate() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Db_Collection_Abstract::getData() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Db_Collection_Abstract::getResource() should return Mage_Core_Model_Resource_Db_Abstract but returns Mage_Core_Model_Resource_Db_Abstract|Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Design::_checkIntersection() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Design.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Email_Template::getSystemConfigByPathsAndTemplateId() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Email/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Entity_Table::getTable() should return string but returns array|string|Varien_Simplexml_Config|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Entity/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_File_Storage_Abstract::_getConnection() should return Varien_Db_Adapter_Interface but returns Varien_Db_Adapter_Interface|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/File/Storage/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Helper_Abstract::_getConnection() should return Varien_Db_Adapter_Interface but returns Varien_Db_Adapter_Interface|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Helper/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Helper_Mysql4::getQueryUsingAnalyticFunction() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Session::getLifeTime() should return int but returns int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Setup::_getResource() should return Mage_Core_Model_Resource_Resource but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Setup_Query_Modifier::_getColumnDefinitionFromSql() should return array but returns array<string, mixed>|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Setup/Query/Modifier.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Type_Db_Pdo_Mysql::_getDbAdapterInstance() should return Varien_Db_Adapter_Pdo_Mysql but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Type/Db/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Resource_Url_Rewrite::getRequestPathByIdPath() should return string|false but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Url/Rewrite.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Session_Abstract::getCookieLifetime() should return int but returns int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Session_Abstract::getMessages() should return Mage_Core_Model_Message_Collection but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Session_Abstract_Varien::getSessionId() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Varien.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Session_Abstract_Varien::getSessionName() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Varien.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Store::_updateMediaPathUseRewrites() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Store::convertPrice() should return float but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Store::getBaseCurrencyCode() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Store::getConfig() should return string|null but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Store::getDefaultCurrencyCode() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Store::processSubst() should return string but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Store_Group::getStores() should return array<Mage_Core_Model_Store> but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Translate::getResource() should return Mage_Core_Model_Resource_Translate but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Translate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Url::_getSingletonModel() should return Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Url::getRequest() should return Mage_Core_Controller_Request_Http but returns Zend_Controller_Request_Http.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Url::sessionUrlVar() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Url_Rewrite_Request::_getRouterByRoute() should return Mage_Core_Controller_Varien_Router_Abstract but returns Mage_Core_Controller_Varien_Router_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url/Rewrite/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Validate_Abstract::_createMessage() should return string|null but returns array<string>|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Validate/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Website::getGroups() should return array<Mage_Core_Model_Store_Group> but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Core_Model_Website::getStores() should return array<Mage_Core_Model_Store> but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Block_Address_Renderer_Default::_prepareAddressTemplateData() should return string but returns array<string>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Address/Renderer/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Block_Address_Renderer_Default::getFormat() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Address/Renderer/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Block_Widget_Abstract::_getAttribute() should return Mage_Customer_Model_Attribute|false but returns Mage_Eav_Model_Entity_Attribute_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Widget/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Block_Widget_Name::_getAttribute() should return Mage_Customer_Model_Attribute|false but returns Mage_Eav_Model_Entity_Attribute_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Widget/Name.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Helper_Data::getPasswordTimestamp() should return int but returns int|string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Address_Abstract::getFormated() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Address_Abstract::getStreet1() should return string but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Address_Abstract::getStreet2() should return string but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Address_Abstract::getStreet3() should return string but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Address_Abstract::getStreet4() should return string but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Address_Api::create() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Address_Api_V2::create() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Address_Config::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Customer::getAddressItemById() should return Mage_Customer_Model_Address but returns Mage_Customer_Model_Address|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Customer::getCreatedAtTimestamp() should return int|null but returns int|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Customer::getPrimaryBillingAddress() should return Mage_Customer_Model_Address but returns Mage_Customer_Model_Address|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Customer::getPrimaryShippingAddress() should return Mage_Customer_Model_Address but returns Mage_Customer_Model_Address|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Customer::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Customer_Api::create() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_Model_Customer_Attribute_Backend_Password::validate() should return bool but returns $this(Mage_Customer_Model_Customer_Attribute_Backend_Password)|bool.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Password.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_AccountController::_escapeHtml() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AccountController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Customer_AccountController::_getModel() should return Mage_Core_Model_Abstract|false but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AccountController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Dataflow_Model_Batch_Io::read() should return array|string|false|null but returns array|bool|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Batch/Io.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Dataflow_Model_Batch_Io::write() should return bool but returns bool|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Batch/Io.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Dataflow_Model_Convert_Adapter_Io::getResource() should return Varien_Io_Abstract|false but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Adapter/Io.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Dataflow_Model_Resource_Import::loadBySessionId() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Resource/Import.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Directory_Helper_Data::getCountriesWithOptionalZip() should return array|string but returns array|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Directory_Model_Country::formatAddress() should return string but returns string|null.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Country.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Attribute_Data_Abstract::_getFormFilter() should return Varien_Data_Form_Filter_Interface|false but returns object.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Attribute_Data_Date::outputValue() should return array|string but returns array|float|int|string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Attribute_Data_Datetime::_getFormFilter() should return Varien_Data_Form_Filter_Interface|false but returns object.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Datetime.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Attribute_Data_Multiselect::extractValue() should return array|string but returns array<mixed, mixed>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Multiselect.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Attribute_Data_Select::_getOptionText() should return string but returns bool|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Attribute_Data_Text::extractValue() should return array|string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Text.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Config::_isCacheEnabled() should return bool but returns array|bool.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Config::getAttribute() should return Mage_Eav_Model_Entity_Attribute_Abstract|false but returns Mage_Eav_Model_Entity_Attribute_Interface.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Config::getAttributes() should return array<Mage_Eav_Model_Entity_Attribute_Abstract> but returns list<Mage_Eav_Model_Entity_Attribute_Abstract|false>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Convert_Adapter_Entity::_getCollectionForLoad() should return Mage_Eav_Model_Entity_Collection|false but returns Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Convert/Adapter/Entity.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Convert_Adapter_Entity::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Convert/Adapter/Entity.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Abstract::_getReadAdapter() should return Varien_Db_Adapter_Interface|false but returns Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Abstract::_getWriteAdapter() should return Varien_Db_Adapter_Interface|false but returns Varien_Db_Adapter_Interface|Zend_Db_Adapter_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Abstract::getAttribute() should return Mage_Catalog_Model_Resource_Eav_Attribute|false but returns Mage_Eav_Model_Entity_Attribute_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Abstract::getType() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Abstract::getWriteConnection() should return Varien_Db_Adapter_Interface but returns Varien_Db_Adapter_Interface|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute::getBackendTypeByInput() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute::getDefaultValueByInput() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Abstract::getBackendTable() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Abstract::getEntity() should return Mage_Eav_Model_Entity_Abstract but returns Mage_Eav_Model_Entity_Abstract|Mage_Eav_Model_Entity_Type.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::getTable() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Attribute_Source_Table::getFlatColums() should return array<array{unsigned: bool, default: string|null, extra: string|null, type: string, is_null?: bool, nullable?: bool, comment?: string, length?: int}|void> but returns non-empty-array<string, array{type: \'int\'|\'text\'|\'varchar(255)\', unsigned: false, is_null: true, default: null, extra: null}|array{type: \'integer\'|\'text\', length: 255|\'65535\'|null, unsigned: false, nullable: true, default: null, extra: null, comment: non-falsy-string}>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Collection_Abstract::getAttribute() should return Mage_Eav_Model_Entity_Attribute_Abstract but returns Mage_Catalog_Model_Resource_Eav_Attribute|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Setup::getAttributeGroupId() should return int but returns float|int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Setup::getAttributeId() should return int|false but returns float|int|numeric-string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Setup::getAttributeSetId() should return int but returns float|int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Setup::getEntityTypeId() should return int but returns float|int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Type::_getAttributeCollection() should return object but returns Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Entity_Type::getEntityTablePrefix() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Form::getEntityType() should return Mage_Eav_Model_Entity_Type but returns Mage_Eav_Model_Entity_Type|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Form::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Form::getSystemAttributes() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Form::getUserAttributes() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Resource_Attribute_Collection::getWebsite() should return Mage_Core_Model_Website but returns Mage_Core_Model_Website|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Resource_Entity_Attribute::getIdByCode() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Entity/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Resource_Entity_Attribute_Set::getDefaultGroupId() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Entity/Attribute/Set.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Resource_Entity_Type::getAdditionalAttributeTable() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Entity/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Resource_Form_Attribute_Collection::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Form/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Resource_Form_Type::getFormTypesByAttribute() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Form/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Eav_Model_Resource_Helper_Mysql4::getDdlTypeByColumnType() should return string but returns int|string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_GiftMessage_Block_Adminhtml_Sales_Order_Create_Items::getMessageText() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Block/Adminhtml/Sales/Order/Create/Items.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_GiftMessage_Block_Adminhtml_Sales_Order_View_Items::getMessageText() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Block/Adminhtml/Sales/Order/View/Items.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_GiftMessage_Block_Adminhtml_Sales_Order_View_Items::getRecipient() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Block/Adminhtml/Sales/Order/View/Items.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_GiftMessage_Block_Adminhtml_Sales_Order_View_Items::getSender() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Block/Adminhtml/Sales/Order/View/Items.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_GiftMessage_Block_Message_Form::getEscaped() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Block/Message/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_GiftMessage_Block_Message_Form::getMessage() should return Mage_GiftMessage_Model_Message but returns Mage_GiftMessage_Model_Message|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Block/Message/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_GiftMessage_Block_Message_Inline::getEscaped() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Block/Message/Inline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_GiftMessage_Model_Message::getEntityModelByType() should return Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Model/Message.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ImportExport_Block_Adminhtml_Export_Filter::prepareCollection() should return Mage_Eav_Model_Resource_Entity_Attribute_Collection but returns Varien_Data_Collection|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Block/Adminhtml/Export/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ImportExport_Model_Export_Adapter_Abstract::getContents() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Adapter/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ImportExport_Model_Import::getDataSourceModel() should return Mage_ImportExport_Model_Resource_Import_Data but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ImportExport_Model_Import_Entity_Customer_Address::validateRow() should return bool but returns bool|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Customer/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ImportExport_Model_Import_Entity_Product::_getAttribute() should return Mage_Eav_Model_Entity_Attribute_Abstract but returns Mage_Catalog_Model_Resource_Eav_Attribute|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ImportExport_Model_Import_Entity_Product::getModel() should return bool|Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ImportExport_Model_Import_Entity_Product::getResourceModel() should return object but returns Mage_Core_Model_Resource_Db_Collection_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract::_isPriceCorr() should return int but returns int|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ImportExport_Model_Resource_Import_Data::getIterator() should return ArrayIterator<int|string, mixed> but returns Traversable<mixed, mixed>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Resource/Import/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Index_Model_Resource_Event::getUnprocessedEvents() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Resource/Event.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Install_Block_Abstract::getCurrentStep() should return Varien_Object but returns Varien_Object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Install_Model_Installer::getDataModel() should return Mage_Install_Model_Session but returns Mage_Install_Model_Installer_Data|Mage_Install_Model_Session|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Install_Model_Installer_Console::_getDataModel() should return Mage_Install_Model_Installer_Data but returns Mage_Install_Model_Installer_Data|Mage_Install_Model_Session.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer/Console.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Log_Model_Aggregation::_date() should return string but returns int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Aggregation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Log_Model_Aggregation::_timestamp() should return int but returns int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Aggregation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Log_Model_Customer::getLoginAtTimestamp() should return int|null but returns int|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Log_Model_Resource_Aggregation::getLastRecordDate() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Aggregation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Log_Model_Resource_Aggregation::getLogId() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Aggregation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Media_Model_Image::getTmpImage() should return GdImage|resource but returns resource|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Page_Block_Html_Topmenu_Renderer::render() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Topmenu/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Page_Block_Switch::getCurrentStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Switch.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paygate_Block_Authorizenet_Form_Cc::getPartialAuthorizationFormMessage() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Block/Authorizenet/Form/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paygate_Model_Authorizenet::_preauthorizeCaptureCardTransaction() should return Mage_Sales_Model_Order_Payment_Transaction but returns Mage_Sales_Model_Order_Payment_Transaction|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paygate_Model_Authorizenet::_refundCardTransaction() should return Mage_Sales_Model_Order_Payment_Transaction but returns Mage_Sales_Model_Order_Payment_Transaction|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paygate_Model_Authorizenet::_voidCardTransaction() should return Mage_Sales_Model_Order_Payment_Transaction but returns Mage_Sales_Model_Order_Payment_Transaction|null.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paygate_Model_Authorizenet::getCardsStorage() should return Mage_Paygate_Model_Authorizenet_Cards but returns Mage_Paygate_Model_Authorizenet_Cards|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paygate_Model_Authorizenet_Cards::registerCard() should return Varien_Object but returns Varien_Object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet/Cards.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Payment_Block_Form::getInfoData() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Block/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Payment_Helper_Data::getMethodFormBlock() should return Mage_Core_Block_Abstract but returns Mage_Core_Block_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Payment_Helper_Data::getMethodInstance() should return Mage_Payment_Model_Method_Abstract|false but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Payment_Model_Billing_AgreementAbstract::getPaymentMethodInstance() should return Mage_Payment_Model_Method_Abstract|null but returns Mage_Payment_Model_Method_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Billing/AgreementAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Payment_Model_Method_Cc::validateCcNumOther() should return bool but returns int|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Method/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Global::getElementBackendConfig() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Fieldset/Global.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paypal_Model_Api_Abstract::_exportToRequest() should return array but returns array|Varien_Object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paypal_Model_Cart::getSalesEntity() should return Mage_Sales_Model_Quote but returns Mage_Sales_Model_Order|Mage_Sales_Model_Quote.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paypal_Model_Config::getMethodCode() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paypal_Model_Direct::getConfigPaymentAction() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paypal_Model_Express::getConfigPaymentAction() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paypal_Model_Info::exportFromPayment() should return array|Varien_Object but returns array|(callable)|Varien_Object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paypal_Model_Method_Agreement::getConfigPaymentAction() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paypal_Model_Payflowlink::_getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowlink.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paypal_Model_Pro::getApi() should return Mage_Paypal_Model_Api_Nvp but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Pro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Paypal_Model_Resource_Payment_Transaction::_lookupByTxnId() should return array|string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Resource/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_PaypalUk_Model_Pro::_getParentTransactionId() should return string but returns array|float|int|string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/PaypalUk/Model/Pro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ProductAlert_Block_Email_Abstract::_getFilteredProductShortDescription() should return string|null but returns array|float|int|string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Block/Email/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ProductAlert_Block_Email_Abstract::_getFilteredProductShortDescription() should return string|null but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Block/Email/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ProductAlert_Block_Email_Abstract::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Block/Email/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ProductAlert_Helper_Data::createBlock() should return Mage_ProductAlert_Block_Email_Price|Mage_ProductAlert_Block_Email_Stock but returns Mage_Core_Block_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ProductAlert_Helper_Data::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ProductAlert_Model_Email::_getPriceBlock() should return Mage_ProductAlert_Block_Email_Price but returns Mage_ProductAlert_Block_Email_Price|Mage_ProductAlert_Block_Email_Stock.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Model/Email.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ProductAlert_Model_Email::_getStockBlock() should return Mage_ProductAlert_Block_Email_Stock but returns Mage_ProductAlert_Block_Email_Price|Mage_ProductAlert_Block_Email_Stock.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Model/Email.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_ProductAlert_Model_Observer::_getWebsites() should return array<Mage_Core_Model_Website> but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Rating_Model_Rating::getReviewSummary() should return array but returns array|Mage_Rating_Model_Rating.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Rating.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Rating_Model_Resource_Rating::_getEntitySummaryData() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Resource/Rating.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Rating_Model_Resource_Rating::getEntityIdByCode() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Resource/Rating.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Rating_Model_Resource_Rating_Entity::getIdByCode() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Resource/Rating/Entity.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Reports_Block_Product_Abstract::_getModel() should return Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Block/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Reports_Model_Config::getGlobalConfig() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Reports_Model_Product_Index_Abstract::getCustomerId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Product/Index/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Reports_Model_Product_Index_Abstract::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Product/Index/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Reports_Model_Product_Index_Abstract::getVisitorId() should return int but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Product/Index/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Reports_Model_Resource_Product_Lowstock_Collection::_getInventoryItemResource() should return Mage_CatalogInventory_Model_Resource_Stock_Item but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Lowstock/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Reports_Model_Test::getUsersCities() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Test.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Reports_Model_Test::returnAsDataSource() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Test.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Review_Block_Form::getProductInfo() should return Mage_Core_Model_Abstract|false but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Review_Model_Resource_Review::getEntityIdByCode() should return bool|int but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Review_Model_Resource_Review::getTotalReviews() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Review_ProductController::_initProduct() should return Mage_Catalog_Model_Product|false but returns Mage_Catalog_Model_Product|true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/controllers/ProductController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Rss_Block_Catalog_Abstract::_getPriceBlock() should return Mage_Core_Block_Abstract but returns Mage_Core_Block_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Block/Catalog/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Rss_Block_List::getCurrentStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Block/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Rss_Block_List::getRssCatalogFeeds() should return array but returns array|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Block/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Rule_Model_Condition_Abstract::getValueName() should return string but returns array|int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Rule_Model_Condition_Combine::_getNewConditionModelInstance() should return bool|Mage_Rule_Model_Condition_Abstract but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Combine.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Rule_Model_Condition_Product_Abstract::getAttributeObject() should return Mage_Catalog_Model_Resource_Eav_Attribute but returns Mage_Eav_Model_Entity_Attribute_Abstract|Varien_Object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Adminhtml_Block_Sales_Recurring_Profile_View_Items::formatPrice() should return string but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Adminhtml/Recurring/Profile/View/Items.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Block_Billing_Agreements::getItemValue() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Billing/Agreements.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Block_Order_Email_Items_Default::getProductAdditionalInformationBlock() should return Mage_Core_Block_Abstract but returns Mage_Core_Block_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Email/Items/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Block_Order_Email_Items_Default::getValueHtml() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Email/Items/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Block_Order_Email_Items_Order_Default::getProductAdditionalInformationBlock() should return Mage_Core_Block_Abstract|null but returns Mage_Core_Block_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Email/Items/Order/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Block_Order_Email_Items_Order_Default::getValueHtml() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Email/Items/Order/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Block_Order_Item_Renderer_Default::getProductAdditionalInformationBlock() should return Mage_Core_Block_Abstract|null but returns Mage_Core_Block_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Item/Renderer/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Block_Order_Print_Shipment::getBillingAddressFormattedHtml() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Print/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Block_Order_Print_Shipment::getShipmentAddressFormattedHtml() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Print/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Block_Reorder_Sidebar::isItemAvailableForReorder() should return bool but returns bool|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Reorder/Sidebar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Api2_Order_Address_Rest::_retrieveCollection() should return array but returns int|list<array>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Api2/Order/Address/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Api2_Order_Comment_Rest::_retrieveCollection() should return array but returns array<int<0, max>|string, array|int>|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Api2/Order/Comment/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Entity_Quote::getReservedOrderId() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Entity_Sale_Collection::getAttribute() should return Mage_Eav_Model_Entity_Attribute_Abstract but returns Mage_Catalog_Model_Resource_Eav_Attribute|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Sale/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Config::_getState() should return Varien_Simplexml_Element but returns Varien_Simplexml_Element|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Config::_getStatus() should return Varien_Simplexml_Element but returns Varien_Simplexml_Element|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Creditmemo::getBillingAddress() should return Mage_Sales_Model_Order_Address but returns Mage_Sales_Model_Order_Address|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Creditmemo::getShippingAddress() should return Mage_Sales_Model_Order_Address but returns Mage_Sales_Model_Order_Address|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Creditmemo_Comment::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo/Comment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Invoice::getBillingAddress() should return Mage_Sales_Model_Order_Address but returns Mage_Sales_Model_Order_Address|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Invoice::getShippingAddress() should return Mage_Sales_Model_Order_Address but returns Mage_Sales_Model_Order_Address|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Invoice_Comment::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice/Comment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Invoice_Item::getOrderItem() should return Mage_Sales_Model_Order_Item but returns Mage_Sales_Model_Order_Item|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Item::getOrder() should return Mage_Sales_Model_Order but returns Mage_Sales_Model_Order|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Item::getRealProductType() should return array|null but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Payment_Transaction::getOrderId() should return int|null but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Payment_Transaction::getParentTransaction() should return $this(Mage_Sales_Model_Order_Payment_Transaction)|false but returns Mage_Sales_Model_Order_Payment_Transaction|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Shipment::getBillingAddress() should return Mage_Sales_Model_Order_Address but returns Mage_Sales_Model_Order_Address|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Shipment::getShippingAddress() should return Mage_Sales_Model_Order_Address but returns Mage_Sales_Model_Order_Address|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Shipment::getShippingLabel() should return string but returns array|float|int|string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Shipment_Comment::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Comment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Shipment_Track::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Track.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Shipment_Track::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Track.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Order_Status_History::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Status/History.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Quote::addProductAdvanced() should return Mage_Sales_Model_Quote_Item|string but returns Mage_Sales_Model_Quote_Item|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Quote::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Quote::getStoreId() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Quote_Address_Total_Nominal_RecurringAbstract::fetch() should return array but returns $this(Mage_Sales_Model_Quote_Address_Total_Nominal_RecurringAbstract)|array.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Nominal/RecurringAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Quote_Item::getProductType() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Resource_Order_Abstract::getVirtualGridColumns() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Resource_Order_Payment_Transaction::_lookupByTxnId() should return array|string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Resource_Order_Payment_Transaction::getOrderWebsiteId() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Resource_Quote::getReservedOrderId() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_Model_Service_Order::_getLocaleNumber() should return float but returns float|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Service/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Sales_GuestController::_loadValidOrder() should return bool but returns bool|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/controllers/GuestController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_SalesRule_Model_Quote_Nominal_Discount::fetch() should return array but returns $this(Mage_SalesRule_Model_Quote_Nominal_Discount)|array.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Quote/Nominal/Discount.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_SalesRule_Model_Resource_Rule::getCustomerUses() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_SalesRule_Model_Resource_Rule::getStoreLabel() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_SalesRule_Model_Rule::_getAddressId() should return string but returns int|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_SalesRule_Model_Validator::_getSingleton() should return Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Shipping_Model_Carrier_Abstract::getCarrierCode() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Shipping_Model_Carrier_Abstract::getDebugFlag() should return bool but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Shipping_Model_Carrier_Tablerate::_getModel() should return Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Shipping_Model_Config::getCarrierInstance() should return Mage_Usa_Model_Shipping_Carrier_Abstract|false but returns Mage_Shipping_Model_Carrier_Abstract|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Shipping_Model_Rate_Result::getError() should return bool|null but returns bool|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Rate/Result.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Shipping_Model_Shipping::getCarrierByCode() should return bool|Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Shipping_Model_Tracking_Result_Error::getAllData() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Tracking/Result/Error.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Shipping_Model_Tracking_Result_Status::getAllData() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Tracking/Result/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Tag_Model_Api::info() should return array<string, array<int|string, int>|int|string> but returns array<string, mixed>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Tag_Model_Resource_Tag::_getAggregationPerStoreView() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Tag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Tax_Block_Adminhtml_Notifications::getInfoUrl() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Block/Adminhtml/Notifications.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Tax_Model_Resource_Calculation::_collectPercent() should return int but returns float|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Resource/Calculation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Tax_Model_Resource_Sales_Order_Tax_Item::getTaxItemsByItemId() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Resource/Sales/Order/Tax/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Tax_Model_Sales_Total_Quote_Nominal_Subtotal::fetch() should return array but returns $this(Mage_Tax_Model_Sales_Total_Quote_Nominal_Subtotal)|array.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Nominal/Subtotal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Tax_Model_Sales_Total_Quote_Tax::_calculateRowWeeeTax() should return int but returns float|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Uploader_Helper_File::getPostMaxSize() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Uploader/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Uploader_Helper_File::getUploadMaxSize() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Uploader/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Dhl::_doShipmentRequest() should return Varien_Object but returns Mage_Shipping_Model_Rate_Result|Varien_Object|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Dhl::_getQuotes() should return Mage_Shipping_Model_Rate_Result but returns Mage_Shipping_Model_Rate_Result|Varien_Object|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Dhl::getCode() should return array|bool but returns array<int|string, string>|string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Dhl::getCode() should return array|bool but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Dhl_International::_doShipmentRequest() should return Varien_Object but returns Mage_Shipping_Model_Rate_Result|Varien_Object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Dhl_International::_getQuotes() should return Mage_Shipping_Model_Rate_Result but returns Mage_Shipping_Model_Rate_Result|Varien_Object.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Dhl_International::_parseResponse() should return Mage_Shipping_Model_Rate_Result|Varien_Object but returns bool|Mage_Shipping_Model_Rate_Result_Error.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_PageBuilder::_x() should return int but returns float|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Label/Pdf/PageBuilder.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_PageBuilder::_y() should return int but returns float|int.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Label/Pdf/PageBuilder.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Fedex::_removeErrorsIfRateExist() should return Mage_Shipping_Model_Rate_Result but returns Mage_Shipping_Model_Rate_Result|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Fedex::getCode() should return array|bool but returns array<string, array<int|string, array<string, list<string>>|string>>|string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Fedex::getDeliveryConfirmationTypes() should return array but returns array|bool.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Ups::_formShipmentRestRequest() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Ups::getCode() should return array|false but returns array{\'01\': string, \'02\': string, \'03\': string, \'07\': string, \'08\': string, 11: string, 12: string, 13: string, ...}|array{\'01\': string, \'02\': string, \'03\': string, \'07\': string, \'08\': string, 12: string, 14: string, 54: string, ...}|array{\'01\': string, \'02\': string, \'07\': string, \'08\': string, 11: string, 12: string, 14: string, 65: string}|array{\'07\': string, \'08\': string, 11: string, 54: string, 65: string, 82: string, 83: string, 84: string, ...}|array{\'07\': string, \'08\': string, 11: string, 54: string, 65: string}|array{containers: array{\'00\'}, filters: array{within_us: array{method: array{\'01\', \'13\', \'12\', \'59\', \'03\', \'14\', \'02\'}}, from_us: array{method: array{\'07\', \'54\', \'08\', \'65\', \'11\'}}}}|array{containers: array{\'01\', \'04\'}, filters: array{within_us: array{method: array{\'01\', \'14\', \'02\', \'59\', \'13\'}}, from_us: array{method: array{\'07\', \'54\', \'65\'}}}}|array{containers: array{\'04\'}, filters: array{within_us: array{method: array{}}, from_us: array{method: array{\'08\'}}}}|array{containers: array{\'24\', \'25\'}, filters: array{within_us: array{method: array{}}, from_us: array{method: array{\'07\', \'54\', \'65\'}}}}|array{containers: array{\'2a\', \'2b\', \'2c\', \'03\'}, filters: array{within_us: array{method: array{\'01\', \'13\', \'14\', \'02\', \'59\', \'13\'}}, from_us: array{method: array{\'07\', \'54\', \'08\', \'65\'}}}}|array{label: \'Customer Counter\', code: \'03\'}|array{label: \'Letter Center\', code: \'19\'}|array{label: \'On Call Air\', code: \'07\'}|array{label: \'One Time Pickup\', code: \'06\'}|array{label: \'Regular Daily Pickup\', code: \'01\'}|string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Usps::getCode() should return array<string, mixed>|bool but returns array<int|string, array<string, array<int|string, array<string, list<string>>|string>>|string>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Usa_Model_Shipping_Carrier_Usps::getCode() should return array<string, mixed>|bool but returns array<string, array<int|string, array<string, list<string>>|string>>|string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Weee_Model_Resource_Attribute_Backend_Weee_Tax::loadProductData() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Resource/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Weee_Model_Resource_Tax::fetchOne() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Resource/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Weee_Model_Resource_Tax::getProductDiscountPercent() should return string but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Resource/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Widget_Model_Widget_Config::decodeWidgetsFromQuery() should return array but returns list<string>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Model/Widget/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Wishlist_Block_Abstract::getAddToWishlistUrlCustom() should return string but returns bool|string.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Wishlist_Block_Abstract::getEscapedDescription() should return string but returns array<string|null>|string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Wishlist_Block_Abstract::getWishlistItems() should return Mage_Wishlist_Model_Resource_Item_Collection but returns Mage_Wishlist_Model_Resource_Item_Collection|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Wishlist_Block_Customer_Sidebar::getWishlistItems() should return Mage_Wishlist_Model_Resource_Item_Collection but returns Mage_Wishlist_Model_Resource_Item_Collection|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Block/Customer/Sidebar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Wishlist_Helper_Data::_getCurrentCustomer() should return Mage_Customer_Model_Customer but returns Mage_Customer_Model_Customer|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Wishlist_Helper_Data::_getSingletonModel() should return Mage_Core_Model_Abstract but returns object|false.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Wishlist_Helper_Data::_getUrlStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Wishlist_Model_Wishlist::addNewItem() should return Mage_Wishlist_Model_Item|string but returns Mage_Wishlist_Model_Item|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Wishlist_Model_Wishlist::getItem() should return Mage_Wishlist_Model_Item|false but returns Mage_Wishlist_Model_Item|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Wishlist_Model_Wishlist::getStore() should return Mage_Core_Model_Store but returns Mage_Core_Model_Store|null.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Error_Processor::_loadXml() should return SimpleXMLElement|null but returns SimpleXMLElement|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../errors/processor.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Archive::_getArchiver() should return Mage_Archive_Bz|Mage_Archive_Gz|Mage_Archive_Tar but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Archive_Tar::_getReader() should return Mage_Archive_Helper_File but returns Mage_Archive_Helper_File|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Archive_Tar::_getWriter() should return Mage_Archive_Helper_File but returns Mage_Archive_Helper_File|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Cache_Backend_File::_filePutContents() should return bool but returns int<0, max>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_Cache_Backend_File::getMetadatas() should return array but returns array|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_DB_Mysqli::connect() should return bool but returns bool|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/DB/Mysqli.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_HTTP_Client::getInstance() should return Mage_HTTP_IClient but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/HTTP/Client.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_System_Ftp::chmod() should return bool but returns int|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_System_Ftp::correctFilePath() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_System_Ftp::mdkir() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Mage_System_Ftp::nlist() should return bool but returns array<int, string>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Magento_Db_Adapter_Pdo_Mysql::quote() should return string but returns float|string.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Magento_Db_Sql_Trigger::getBody() should return array but returns array|string.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Db/Sql/Trigger.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Magento_Profiler_Output_Firebug::_renderTimerId() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Profiler/Output/Firebug.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Magento_Profiler_Output_Html::_renderTimerId() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Profiler/Output/Html.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Cache_Backend_Database::_saveTags() should return bool but returns Zend_Db_Statement_Interface|true.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Backend/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Cache_Backend_Database::load() should return string|false but returns string|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Backend/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Cache_Core::_id() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Core.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Crypt::factory() should return Varien_Crypt_Abstract but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Crypt.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Data_Collection::getFirstItem() should return T of Varien_Object but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Data_Collection::getLastItem() should return T of Varien_Object but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Data_Collection::getNewEmptyItem() should return T of Varien_Object but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Data_Collection::getPageSize() should return int but returns int|false|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Data_Collection_Db::getData() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Data_Form_Abstract::addField() should return Varien_Data_Form_Element_Abstract but returns object.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Data_Form_Filter_Date::outputFilter() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Filter/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Data_Form_Filter_Datetime::outputFilter() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Filter/Datetime.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Data_Tree::getNodeById() should return Varien_Data_Tree_Node but returns Varien_Data_Tree_Node|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Data_Tree_Node::getLastChild() should return Varien_Data_Tree_Node but returns Varien_Data_Tree_Node|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree/Node.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Date::_convert() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Date::toTimestamp() should return int|false but returns int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Db_Adapter_Mysqli::raw_query() should return mysqli_result but returns bool|mysqli_result.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Mysqli.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Db_Adapter_Pdo_Mysql::describeTable() should return array but returns array|int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Db_Adapter_Pdo_Mysql::getCreateTable() should return string but returns array|bool|int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Db_Adapter_Pdo_Mysql::getForeignKeys() should return array but returns array|int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Db_Adapter_Pdo_Mysql::getIndexList() should return array but returns array|int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Db_Adapter_Pdo_Mysql::raw_query() should return Zend_Db_Statement_Interface but returns PDOStatement|Zend_Db_Statement_Interface.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Db_Select::deleteFromSelect() should return string but returns int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Event_Observer_Regex::isValidFor() should return bool but returns int|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Event/Observer/Regex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_File_Uploader::getCorrectFileName() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Uploader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_File_Uploader::save() should return array|false but returns array|bool.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Uploader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Filter_FormElementName::filter() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/FormElementName.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Filter_Template_Simple::filter() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template/Simple.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Http_Adapter_Curl::_getResource() should return CurlHandle|resource but returns CurlHandle|resource|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Image::quality() should return int but returns int|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Image_Adapter_Abstract::getOriginalHeight() should return int|null but returns int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Image_Adapter_Abstract::getOriginalWidth() should return int|null but returns int|string.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Io_File::filePutContent() should return int but returns int<0, max>|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Io_Ftp::chmod() should return bool but returns int|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Io_Ftp::mkdir() should return bool but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Io_Ftp::pwd() should return string but returns string|false.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Object::__toArray() should return array but returns array|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Object_Mapper::accumulateByMap() should return array|Varien_Object but returns array|object.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Object/Mapper.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Simplexml_Config::getXpath() should return array<Varien_Simplexml_Element>|false but returns non-empty-array<SimpleXMLElement>.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Simplexml/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Method Varien_Simplexml_Element::getAttribute() should return string but returns string|null.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Simplexml/Element.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
