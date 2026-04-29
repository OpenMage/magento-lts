<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (object) of method Mage_Admin_Model_Rules::getCollection() should be covariant with return type (Mage_Core_Model_Resource_Db_Collection_Abstract|false) of method Mage_Core_Model_Abstract::getCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Rules.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Adminhtml_Block_Catalog_Category_Widget_Chooser::_getNodeJson() should be covariant with return type (array<string, mixed>) of method Mage_Adminhtml_Block_Catalog_Category_Tree::_getNodeJson()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Widget/Chooser.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes::_getAdditionalElementTypes() should be covariant with return type (array<string|void>) of method Mage_Adminhtml_Block_Widget_Form::_getAdditionalElementTypes()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Attributes.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories::_getNodeJson() should be covariant with return type (array<string, mixed>) of method Mage_Adminhtml_Block_Catalog_Category_Tree::_getNodeJson()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Categories.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|null) of method Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Price::getEscapedValue() should be covariant with return type (string) of method Varien_Data_Form_Element_Abstract::getEscapedValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Helper/Form/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Filter_Status::_getOptions() should be covariant with return type (array<array>) of method Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select::_getOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Newsletter/Grid/Filter/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|false) of method Mage_Adminhtml_Block_Customer_Form_Element_File::getEscapedValue() should be covariant with return type (string) of method Varien_Data_Form_Element_Abstract::getEscapedValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Form/Element/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|false) of method Mage_Adminhtml_Block_Customer_Form_Element_Image::_getPreviewUrl() should be covariant with return type (string) of method Mage_Adminhtml_Block_Customer_Form_Element_File::_getPreviewUrl()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Form/Element/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Adminhtml_Block_Page_Menu::getCacheKeyInfo() should be covariant with return type (array<int|string, string>) of method Mage_Core_Block_Template::getCacheKeyInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Page/Menu.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (mixed) of method Mage_Adminhtml_Block_Report_Grid::getExcel() should be covariant with return type (string) of method Mage_Adminhtml_Block_Widget_Grid::getExcel()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Block_Report_Grid_Abstract) of method Mage_Adminhtml_Block_Report_Grid_Abstract::addColumn() should be covariant with return type ($this(Mage_Adminhtml_Block_Widget_Grid)) of method Mage_Adminhtml_Block_Widget_Grid::addColumn()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Grid/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Adminhtml_Block_System_Email_Template_Grid_Filter_Type::_getOptions() should be covariant with return type (array<array>) of method Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select::_getOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Email/Template/Grid/Filter/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|null) of method Mage_Adminhtml_Block_System_Store_Grid_Render_Group::render() should be covariant with return type (string) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Grid/Render/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|null) of method Mage_Adminhtml_Block_System_Store_Grid_Render_Store::render() should be covariant with return type (string) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Grid/Render/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|null) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Country::render() should be covariant with return type (string) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Country.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (mixed) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number::_getValue() should be covariant with return type (string|null) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::_getValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Number.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (mixed) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text::_getValue() should be covariant with return type (string|null) of method Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::_getValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Text.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Admin_Model_Session) of method Mage_Adminhtml_Controller_Report_Abstract::_getSession() should be compatible with return type (Mage_Adminhtml_Model_Session) of method Mage_Adminhtml_Controller_Action::_getSession()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Controller/Report/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Model_Config) of method Mage_Adminhtml_Model_Config::saveCache() should be covariant with return type ($this(Varien_Simplexml_Config)) of method Varien_Simplexml_Config::saveCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Adminhtml_Model_System_Config_Backend_Customer_Show_Address::_getAttributeObjects() should be covariant with return type (array<int, mixed>) of method Mage_Adminhtml_Model_System_Config_Backend_Customer_Show_Customer::_getAttributeObjects()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Customer/Show/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Url) of method Mage_Adminhtml_Model_Url::setRouteParams() should be covariant with return type ($this(Mage_Core_Model_Url)) of method Mage_Core_Model_Url::setRouteParams()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Api_RoleController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Api/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Api_UserController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Api/UserController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Catalog_CategoryController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/CategoryController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute) of method Mage_Adminhtml_Catalog_Product_Action_AttributeController::_getHelper() should be compatible with return type (Mage_Adminhtml_Helper_Data) of method Mage_Adminhtml_Controller_Action::_getHelper()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/Product/Action/AttributeController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Catalog_Product_ReviewController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/Product/ReviewController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Catalog_Product_SetController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/Product/SetController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Catalog_ProductController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/ProductController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Catalog_SearchController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/SearchController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Checkout_AgreementController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Checkout/AgreementController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Cms_BlockController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Cms/BlockController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Cms_PageController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Cms/PageController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Customer_GroupController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Customer/GroupController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_CustomerController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/CustomerController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Newsletter_TemplateController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Newsletter/TemplateController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Permissions_BlockController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Permissions/BlockController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Permissions_RoleController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Permissions/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Permissions_UserController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Permissions/UserController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Promo_CatalogController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Promo/CatalogController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Promo_QuoteController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Promo/QuoteController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Admin_Model_Session) of method Mage_Adminhtml_Report_StatisticsController::_getSession() should be compatible with return type (Mage_Adminhtml_Model_Session) of method Mage_Adminhtml_Controller_Action::_getSession()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Report/StatisticsController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Model_Session_Quote) of method Mage_Adminhtml_Sales_Order_CreateController::_getSession() should be compatible with return type (Mage_Adminhtml_Model_Session) of method Mage_Adminhtml_Controller_Action::_getSession()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/Order/CreateController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Sales_OrderController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/OrderController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_SitemapController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/SitemapController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_System_DesignController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/DesignController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_System_Email_TemplateController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/Email/TemplateController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_System_StoreController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/StoreController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Tax_RuleController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Tax/RuleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_App) of method Mage_Api_Model_Config::_saveCache() should be compatible with return type (bool) of method Varien_Simplexml_Config::_saveCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Zend_Cache_Core) of method Mage_Api_Model_Config::getCache() should be compatible with return type (Varien_Simplexml_Config_Cache_Abstract) of method Varien_Simplexml_Config::getCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|false) of method Mage_Api_Model_Config::_loadCache() should be covariant with return type (bool) of method Varien_Simplexml_Config::_loadCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Api_Controller_Action|Varien_Object) of method Mage_Api_Model_Server_Adapter_Jsonrpc::getController() should be covariant with return type (Mage_Api_Controller_Action) of method Mage_Api_Model_Server_Adapter_Interface::getController()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Adapter/Jsonrpc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Object) of method Mage_Api_Model_Server_Adapter_Soap::getController() should be compatible with return type (Mage_Api_Controller_Action) of method Mage_Api_Model_Server_Adapter_Interface::getController()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Adapter/Soap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Api_Controller_Action|Varien_Object) of method Mage_Api_Model_Server_Adapter_Xmlrpc::getController() should be covariant with return type (Mage_Api_Controller_Action) of method Mage_Api_Model_Server_Adapter_Interface::getController()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Adapter/Xmlrpc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Api_Model_Wsdl_Config) of method Mage_Api_Model_Server_V2_Adapter_Soap::_getWsdlConfig() should be compatible with return type (Varien_Object) of method Mage_Api_Model_Server_Adapter_Soap::_getWsdlConfig()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/V2/Adapter/Soap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Api_Model_Wsdl_Config) of method Mage_Api_Model_Server_Wsi_Adapter_Soap::_getWsdlConfig() should be compatible with return type (Varien_Object) of method Mage_Api_Model_Server_Adapter_Soap::_getWsdlConfig()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Wsi/Adapter/Soap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (stdClass) of method Mage_Api_Model_Server_Wsi_Handler::endSession() should be compatible with return type (true) of method Mage_Api_Model_Server_Handler_Abstract::endSession()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Wsi/Handler.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Api_Model_Session::clear() should be compatible with return type ($this(Mage_Core_Model_Session_Abstract_Varien)) of method Mage_Core_Model_Session_Abstract_Varien::clear()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (void) of method Mage_Api_Model_Session::revalidateCookie() should be compatible with return type ($this(Mage_Core_Model_Session_Abstract_Varien)) of method Mage_Core_Model_Session_Abstract_Varien::revalidateCookie()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Abstract) of method Mage_Api_Model_User::delete() should be covariant with return type ($this(Mage_Core_Model_Abstract)) of method Mage_Core_Model_Abstract::delete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (object) of method Mage_Api_Model_User::getCollection() should be covariant with return type (Mage_Core_Model_Resource_Db_Collection_Abstract|false) of method Mage_Core_Model_Abstract::getCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_App) of method Mage_Api_Model_Wsdl_Config::_saveCache() should be compatible with return type (bool) of method Varien_Simplexml_Config::_saveCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Simplexml_Config_Cache_Abstract|Zend_Cache_Core) of method Mage_Api_Model_Wsdl_Config::getCache() should be covariant with return type (Varien_Simplexml_Config_Cache_Abstract) of method Varien_Simplexml_Config::getCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool|string) of method Mage_Api_Model_Wsdl_Config::getXmlString() should be covariant with return type (string) of method Varien_Simplexml_Config::getXmlString()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|false) of method Mage_Api_Model_Wsdl_Config::_loadCache() should be covariant with return type (bool) of method Varien_Simplexml_Config::_loadCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type ($this(Mage_Api_Model_Wsdl_Config_Base)|false) of method Mage_Api_Model_Wsdl_Config_Base::loadFile() should be covariant with return type (bool) of method Varien_Simplexml_Config::loadFile()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Base.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Simplexml_Element) of method Mage_Api_Model_Wsdl_Config_Element::extend() should be covariant with return type ($this(Varien_Simplexml_Element)) of method Varien_Simplexml_Element::extend()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Api_Model_Wsdl_Config_Element::getChildren() should be compatible with return type (RecursiveIterator|null) of method RecursiveIterator<string,static(SimpleXMLElement)>::getChildren()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Api_Model_Wsdl_Config_Element::getChildren() should be compatible with return type (RecursiveIterator|null) of method SimpleXMLElement::getChildren()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|null) of method Mage_Api_Model_Wsdl_Config_Element::getAttribute() should be covariant with return type (string) of method Varien_Simplexml_Element::getAttribute()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Zend_Acl) of method Mage_Api2_Model_Acl::addRole() should be covariant with return type ($this(Zend_Acl)) of method Zend_Acl::addRole()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|bool) of method Mage_Api2_Model_Route_Abstract::match() should be covariant with return type (array|false) of method Zend_Controller_Router_Route::match()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Route/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Api2_Adminhtml_Api2_AttributeController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/controllers/Adminhtml/Api2/AttributeController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Api2_Adminhtml_Api2_RoleController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/controllers/Adminhtml/Api2/RoleController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Authorizenet_Helper_Admin::getSaveOrderUrlParams() should be covariant with return type (array<string, string>) of method Mage_Authorizenet_Helper_Data::getSaveOrderUrlParams()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Authorizenet/Helper/Admin.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (float|null) of method Mage_Bundle_Model_Product_Price::getGroupPrice() should be covariant with return type (float) of method Mage_Catalog_Model_Product_Type_Price::getGroupPrice()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Bundle_Model_Product_Type::getOrderOptions() should be covariant with return type (array<string, mixed>) of method Mage_Catalog_Model_Product_Type_Abstract::getOrderOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Bundle_Model_Product_Type::getParentIdsByChild() should be covariant with return type (array<int|void>) of method Mage_Catalog_Model_Product_Type_Abstract::getParentIdsByChild()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array<string, array<array<string>|string>>) of method Mage_Bundle_Model_Product_Type::processBuyRequest() should be covariant with return type (array<array<string>|void>) of method Mage_Catalog_Model_Product_Type_Abstract::processBuyRequest()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Bundle_Model_Product_Type::isVirtual() should be covariant with return type (false) of method Mage_Catalog_Model_Product_Type_Abstract::isVirtual()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool|null) of method Mage_Bundle_Model_Product_Type::isMapEnabledInOptions() should be covariant with return type (false) of method Mage_Catalog_Model_Product_Type_Abstract::isMapEnabledInOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (int) of method Mage_Bundle_Model_Product_Type::prepareQuoteItemQty() should be compatible with return type (float) of method Mage_Catalog_Model_Product_Type_Abstract::prepareQuoteItemQty()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Customer_Model_Session) of method Mage_Captcha_Model_Zend::getSession() should be compatible with return type (Laminas\\Session\\Container) of method Laminas\\Captcha\\AbstractWord::getSession()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/Model/Zend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Block_Template) of method Mage_Catalog_Block_Category_View::_prepareLayout() should be covariant with return type ($this(Mage_Core_Block_Abstract)) of method Mage_Core_Block_Abstract::_prepareLayout()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Category/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Catalog_Block_Navigation::getCacheKeyInfo() should be covariant with return type (array<int|string, string>) of method Mage_Core_Block_Template::getCacheKeyInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Navigation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (object) of method Mage_Catalog_Block_Product_List_Promotion::_getProductCollection() should be covariant with return type (Mage_Catalog_Model_Resource_Product_Collection) of method Mage_Catalog_Block_Product_List::_getProductCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/List/Promotion.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (object) of method Mage_Catalog_Block_Product_List_Random::_getProductCollection() should be covariant with return type (Mage_Catalog_Model_Resource_Product_Collection) of method Mage_Catalog_Block_Product_List::_getProductCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/List/Random.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array<int|string, mixed>) of method Mage_Catalog_Block_Product_New::getCacheKeyInfo() should be covariant with return type (array<int|string, string>) of method Mage_Core_Block_Template::getCacheKeyInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/New.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (float|int) of method Mage_Catalog_Block_Product_Widget_Html_Pager::getLastPageNum() should be covariant with return type (int) of method Mage_Page_Block_Html_Pager::getLastPageNum()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Widget/Html/Pager.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Page_Block_Html_Pager) of method Mage_Catalog_Block_Seo_Sitemap_Tree_Pager::setCollection() should be covariant with return type ($this(Mage_Page_Block_Html_Pager)) of method Mage_Page_Block_Html_Pager::setCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Seo/Sitemap/Tree/Pager.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Catalog_Model_Resource_Collection_Abstract) of method Mage_Catalog_Model_Abstract::getResourceCollection() should be compatible with return type (Mage_Core_Model_Resource_Db_Collection_Abstract) of method Mage_Core_Model_Abstract::getResourceCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract) of method Mage_Catalog_Model_Category_Attribute_Backend_Sortby::afterLoad() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Attribute/Backend/Sortby.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Catalog_Model_Resource_Product_Collection) of method Mage_Catalog_Model_Convert_Adapter_Product::_getCollectionForLoad() should be compatible with return type (Mage_Eav_Model_Entity_Collection|false) of method Mage_Eav_Model_Convert_Adapter_Entity::_getCollectionForLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Convert_Adapter_Entity) of method Mage_Catalog_Model_Convert_Adapter_Product::save() should be covariant with return type ($this(Mage_Eav_Model_Convert_Adapter_Entity)) of method Mage_Eav_Model_Convert_Adapter_Entity::save()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Object) of method Mage_Catalog_Model_Product::setOrigData() should be covariant with return type ($this(Mage_Core_Model_Abstract)) of method Mage_Core_Model_Abstract::setOrigData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Catalog_Model_Product_Attribute_Api_V2::info() should be covariant with return type (array<string, mixed>) of method Mage_Catalog_Model_Product_Attribute_Api::info()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract|null) of method Mage_Catalog_Model_Product_Attribute_Backend_Media::beforeSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract|void) of method Mage_Catalog_Model_Product_Attribute_Backend_Media::afterSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Catalog_Model_Product_Attribute_Backend_Boolean) of method Mage_Catalog_Model_Product_Attribute_Backend_Msrp::beforeSave() should be covariant with return type ($this(Mage_Catalog_Model_Product_Attribute_Backend_Boolean)) of method Mage_Catalog_Model_Product_Attribute_Backend_Boolean::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Msrp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (mixed) of method Mage_Catalog_Model_Product_Attribute_Source_Countryofmanufacture::getAllOptions() should be covariant with return type (array) of method Mage_Eav_Model_Entity_Attribute_Source_Interface::getAllOptions()',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Source/Countryofmanufacture.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Catalog_Model_Product_Attribute_Source_Inputtype::toOptionArray() should be covariant with return type (array<int, array<string, string>>) of method Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype::toOptionArray()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Source/Inputtype.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Catalog_Model_Product_Type_Configurable::getOrderOptions() should be covariant with return type (array<string, mixed>) of method Mage_Catalog_Model_Product_Type_Abstract::getOrderOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Catalog_Model_Product_Type_Configurable::getParentIdsByChild() should be covariant with return type (array<int|void>) of method Mage_Catalog_Model_Product_Type_Abstract::getParentIdsByChild()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Catalog_Model_Product_Type_Configurable::isVirtual() should be covariant with return type (false) of method Mage_Catalog_Model_Product_Type_Abstract::isVirtual()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool|null) of method Mage_Catalog_Model_Product_Type_Configurable::isMapEnabledInOptions() should be covariant with return type (false) of method Mage_Catalog_Model_Product_Type_Abstract::isMapEnabledInOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Catalog_Model_Product_Type_Grouped::getParentIdsByChild() should be covariant with return type (array<int|void>) of method Mage_Catalog_Model_Product_Type_Abstract::getParentIdsByChild()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Grouped.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Catalog_Model_Product_Type_Virtual::isVirtual() should be covariant with return type (false) of method Mage_Catalog_Model_Product_Type_Abstract::isVirtual()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Virtual.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Collection_Abstract) of method Mage_Catalog_Model_Resource_Collection_Abstract::_joinAttributeToSelect() should be covariant with return type ($this(Mage_Eav_Model_Entity_Collection_Abstract<T of Mage_Core_Model_Abstract>)) of method Mage_Eav_Model_Entity_Collection_Abstract<T of Mage_Catalog_Model_Abstract>::_joinAttributeToSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Catalog_Model_Resource_Product_Attribute_Collection::_getLoadDataFields() should be covariant with return type (array<int, string>) of method Mage_Eav_Model_Resource_Entity_Attribute_Collection::_getLoadDataFields()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Object) of method Mage_Catalog_Model_Resource_Product_Collection::getNewEmptyItem() should be covariant with return type (Mage_Catalog_Model_Product) of method Varien_Data_Collection<Mage_Catalog_Model_Product>::getNewEmptyItem()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Abstract) of method Mage_Catalog_Model_Resource_Product_Option::_afterSave() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Abstract)) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Abstract) of method Mage_Catalog_Model_Resource_Product_Option_Value::_afterSave() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Abstract)) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Option/Value.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Catalog_Model_Resource_Setup::_prepareValues() should be covariant with return type (array<string, array<mixed>|bool|int|string|null>) of method Mage_Eav_Model_Entity_Setup::_prepareValues()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Flag) of method Mage_CatalogIndex_Model_Catalog_Index_Flag::_beforeSave() should be covariant with return type ($this(Mage_Core_Model_Flag)) of method Mage_Core_Model_Flag::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Catalog/Index/Flag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (false) of method Mage_CatalogIndex_Model_Data_Grouped::getFinalPrice() should be compatible with return type (float) of method Mage_CatalogIndex_Model_Data_Abstract::getFinalPrice()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Data/Grouped.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_CatalogRule_Model_Rule_Action_Collection::getNewChildSelectOptions() should be covariant with return type (array<int, array<string, string>>) of method Mage_Rule_Model_Action_Abstract::getNewChildSelectOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule/Action/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Rule_Model_Action_Abstract) of method Mage_CatalogRule_Model_Rule_Action_Product::loadAttributeOptions() should be covariant with return type ($this(Mage_Rule_Model_Action_Abstract)) of method Mage_Rule_Model_Action_Abstract::loadAttributeOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule/Action/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Rule_Model_Action_Abstract) of method Mage_CatalogRule_Model_Rule_Action_Product::loadOperatorOptions() should be covariant with return type ($this(Mage_Rule_Model_Action_Abstract)) of method Mage_Rule_Model_Action_Abstract::loadOperatorOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule/Action/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_CatalogRule_Model_Rule_Condition_Combine::getNewChildSelectOptions() should be covariant with return type (array<int, array<string, string>>) of method Mage_Rule_Model_Condition_Abstract::getNewChildSelectOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule/Condition/Combine.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Catalog_Model_Resource_Product_Collection) of method Mage_CatalogSearch_Model_Resource_Fulltext_Collection::_beforeLoad() should be covariant with return type ($this(Mage_Catalog_Model_Resource_Product_Collection)) of method Mage_Catalog_Model_Resource_Product_Collection::_beforeLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Controller_Front_Action) of method Mage_CatalogSearch_TermController::preDispatch() should be covariant with return type ($this(Mage_Core_Controller_Front_Action)) of method Mage_Core_Controller_Front_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/controllers/TermController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Checkout_Block_Cart_Sidebar::getCacheKeyInfo() should be covariant with return type (array<int|string, string>) of method Mage_Core_Block_Template::getCacheKeyInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Cart/Sidebar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|null) of method Mage_Checkout_Block_Cart_Totals::getTotals() should be covariant with return type (array) of method Mage_Checkout_Block_Cart_Abstract::getTotals()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Cart/Totals.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Abstract) of method Mage_Checkout_Model_Resource_Agreement::_afterLoad() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Abstract)) of method Mage_Core_Model_Resource_Db_Abstract::_afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Resource/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Abstract) of method Mage_Checkout_Model_Resource_Agreement::_afterSave() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Abstract)) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Resource/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Abstract) of method Mage_Checkout_Model_Resource_Agreement::_beforeSave() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Abstract)) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Resource/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type ($this(Mage_Checkout_OnepageController)|null) of method Mage_Checkout_OnepageController::preDispatch() should be covariant with return type ($this(Mage_Core_Controller_Front_Action)) of method Mage_Core_Controller_Front_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/OnepageController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Cms_Block_Widget_Block::getCacheKeyInfo() should be covariant with return type (array<int|string, string>) of method Mage_Core_Block_Template::getCacheKeyInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Block/Widget/Block.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Zend_Db_Select) of method Mage_Cms_Model_Resource_Block::_getLoadSelect() should be covariant with return type (Varien_Db_Select) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Block.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Zend_Db_Select) of method Mage_Cms_Model_Resource_Page::_getLoadSelect() should be covariant with return type (Varien_Db_Select) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array<string, bool|int|string|null>) of method Mage_Cms_Model_Wysiwyg_Images_Storage_Collection::_generateRow() should be covariant with return type (array<string, string>) of method Varien_Data_Collection_Filesystem::_generateRow()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Type_Configurable::getChildrenIds() should be covariant with return type (array<int, non-empty-array>) of method Mage_Catalog_Model_Resource_Product_Type_Configurable::getChildrenIds()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Model/Resource/Catalog/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array<int|string, string>) of method Mage_Core_Block_Template::getCacheKeyInfo() should be covariant with return type (array<int, string>) of method Mage_Core_Block_Abstract::getCacheKeyInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Block_Template) of method Mage_Core_Block_Template_Zend::assign() should be covariant with return type ($this(Mage_Core_Block_Template)) of method Mage_Core_Block_Template::assign()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Template/Zend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|false) of method Mage_Core_Controller_Request_Http::getHttpHost() should be covariant with return type (string) of method Zend_Controller_Request_Http::getHttpHost()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Request/Http.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (int|string|false) of method Mage_Core_Controller_Varien_Router_Standard::getRouteByFrontName() should be covariant with return type (string) of method Mage_Core_Controller_Varien_Router_Abstract::getRouteByFrontName()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|false) of method Mage_Core_Controller_Varien_Router_Standard::getFrontNameByRoute() should be covariant with return type (string) of method Mage_Core_Controller_Varien_Router_Abstract::getFrontNameByRoute()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_App) of method Mage_Core_Model_Config::_saveCache() should be compatible with return type (bool) of method Varien_Simplexml_Config::_saveCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Simplexml_Config) of method Mage_Core_Model_Config::setNode() should be covariant with return type ($this(Varien_Simplexml_Config)) of method Varien_Simplexml_Config::setNode()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Zend_Cache_Core) of method Mage_Core_Model_Config::getCache() should be compatible with return type (Varien_Simplexml_Config_Cache_Abstract) of method Varien_Simplexml_Config::getCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|false) of method Mage_Core_Model_Config::_loadCache() should be covariant with return type (bool) of method Varien_Simplexml_Config::_loadCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|bool) of method Mage_Core_Model_File_Uploader::save() should be covariant with return type (array|false) of method Varien_File_Uploader::save()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Uploader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Core_Model_Resource_Db_Collection_Abstract::_canUseCache() should be covariant with return type (Zend_Cache_Core|false) of method Varien_Data_Collection_Db<T of Mage_Core_Model_Abstract>::_canUseCache()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Db_Adapter_Interface) of method Mage_Core_Model_Resource_File_Storage_Abstract::_getConnection() should be covariant with return type (Magento_Db_Adapter_Pdo_Mysql|false) of method Mage_Core_Model_Resource_Db_Abstract::_getConnection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/File/Storage/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Db_Adapter_Interface) of method Mage_Core_Model_Resource_File_Storage_Abstract::_getReadAdapter() should be covariant with return type (Magento_Db_Adapter_Pdo_Mysql) of method Mage_Core_Model_Resource_Db_Abstract::_getReadAdapter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/File/Storage/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Db_Adapter_Interface) of method Mage_Core_Model_Resource_File_Storage_Abstract::_getWriteAdapter() should be covariant with return type (Magento_Db_Adapter_Pdo_Mysql) of method Mage_Core_Model_Resource_Db_Abstract::_getWriteAdapter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/File/Storage/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Core_Model_Resource_Session::gc() should be covariant with return type (int|false) of method SessionHandlerInterface::gc()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Zend_Db_Select) of method Mage_Core_Model_Resource_Url_Rewrite::_getLoadSelect() should be covariant with return type (Varien_Db_Select) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Url/Rewrite.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Config_Element|string) of method Mage_Core_Model_Session_Abstract::getSessionSaveMethod() should be covariant with return type (string) of method Mage_Core_Model_Session_Abstract_Varien::getSessionSaveMethod()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Config_Element|string) of method Mage_Core_Model_Session_Abstract::getSessionSavePath() should be covariant with return type (string) of method Mage_Core_Model_Session_Abstract_Varien::getSessionSavePath()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string) of method Mage_Customer_Block_Newsletter::getAction() should be compatible with return type (Mage_Core_Controller_Varien_Action) of method Mage_Core_Block_Abstract::getAction()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Newsletter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|void) of method Mage_Customer_Model_Api2_Customer_Rest_Customer_V1::_retrieve() should be covariant with return type (array) of method Mage_Customer_Model_Api2_Customer_Rest::_retrieve()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Api2/Customer/Rest/Customer/V1.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Convert_Adapter_Entity) of method Mage_Customer_Model_Convert_Adapter_Customer::load() should be covariant with return type ($this(Mage_Eav_Model_Convert_Adapter_Entity)) of method Mage_Eav_Model_Convert_Adapter_Entity::load()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Adapter/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Abstract) of method Mage_Customer_Model_Resource_Customer::_afterSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Abstract)) of method Mage_Eav_Model_Entity_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Abstract) of method Mage_Customer_Model_Resource_Group::_afterDelete() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Abstract)) of method Mage_Core_Model_Resource_Db_Abstract::_afterDelete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Abstract) of method Mage_Customer_Model_Resource_Group::_beforeDelete() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Abstract)) of method Mage_Core_Model_Resource_Db_Abstract::_beforeDelete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Customer_Model_Resource_Setup::_prepareValues() should be covariant with return type (array<string, array<mixed>|bool|int|string|null>) of method Mage_Eav_Model_Entity_Setup::_prepareValues()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Io_Abstract|false) of method Mage_Dataflow_Model_Convert_Adapter_Io::getResource() should be covariant with return type (object) of method Mage_Dataflow_Model_Convert_Adapter_Abstract::getResource()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Adapter/Io.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (float|null) of method Mage_Directory_Model_Currency_Import_Webservicex::_convert() should be covariant with return type (float) of method Mage_Directory_Model_Currency_Import_Abstract::_convert()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency/Import/Webservicex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Directory_Model_Country|Mage_Directory_Model_Resource_Country) of method Mage_Directory_Model_Resource_Country_Collection::getItemById() should be covariant with return type (Mage_Directory_Model_Country|null) of method Varien_Data_Collection<Mage_Directory_Model_Country>::getItemById()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Resource/Country/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Downloadable_Model_Product_Type::getOrderOptions() should be covariant with return type (array<string, mixed>) of method Mage_Catalog_Model_Product_Type_Abstract::getOrderOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (void) of method Mage_Downloadable_Product_EditController::preDispatch() should be compatible with return type (Mage_Adminhtml_Controller_Action) of method Mage_Adminhtml_Catalog_ProductController::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/controllers/Product/EditController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|bool) of method Mage_Eav_Model_Attribute_Data_Date::validateValue() should be covariant with return type (array|true) of method Mage_Eav_Model_Attribute_Data_Abstract::validateValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|false) of method Mage_Eav_Model_Attribute_Data_Date::extractValue() should be covariant with return type (array|string) of method Mage_Eav_Model_Attribute_Data_Abstract::extractValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|string|false) of method Mage_Eav_Model_Attribute_Data_File::extractValue() should be covariant with return type (array|string) of method Mage_Eav_Model_Attribute_Data_Abstract::extractValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Attribute_Data_Text) of method Mage_Eav_Model_Attribute_Data_Multiline::restoreValue() should be covariant with return type ($this(Mage_Eav_Model_Attribute_Data_Text)) of method Mage_Eav_Model_Attribute_Data_Text::restoreValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Multiline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|false) of method Mage_Eav_Model_Attribute_Data_Multiline::extractValue() should be covariant with return type (array|string) of method Mage_Eav_Model_Attribute_Data_Text::extractValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Multiline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|bool) of method Mage_Eav_Model_Attribute_Data_Select::validateValue() should be covariant with return type (array|true) of method Mage_Eav_Model_Attribute_Data_Abstract::validateValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|bool) of method Mage_Eav_Model_Attribute_Data_Text::validateValue() should be covariant with return type (array|true) of method Mage_Eav_Model_Attribute_Data_Abstract::validateValue()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Text.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Db_Adapter_Interface|false) of method Mage_Eav_Model_Entity_Abstract::_getReadAdapter() should be covariant with return type (Varien_Db_Adapter_Interface) of method Mage_Core_Model_Resource_Abstract::_getReadAdapter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Db_Adapter_Interface|false) of method Mage_Eav_Model_Entity_Abstract::_getWriteAdapter() should be covariant with return type (Varien_Db_Adapter_Interface) of method Mage_Core_Model_Resource_Abstract::_getWriteAdapter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|string|false) of method Mage_Eav_Model_Entity_Attribute_Source_Table::getOptionText() should be covariant with return type (bool|string) of method Mage_Eav_Model_Entity_Attribute_Source_Abstract::getOptionText()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Eav_Model_Entity_Collection_Abstract::toArray() should be covariant with return type (array<string, int|list<array>>) of method Varien_Data_Collection<T of Mage_Core_Model_Abstract>::toArray()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (stdClass) of method Mage_GiftMessage_Model_Api_V2::_setGiftMessage() should be compatible with return type (array) of method Mage_GiftMessage_Model_Api::_setGiftMessage()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Model/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (object) of method Mage_ImportExport_Model_Export_Entity_Customer::getAttributeCollection() should be covariant with return type (Mage_Eav_Model_Resource_Entity_Attribute_Collection) of method Mage_ImportExport_Model_Export_Entity_Abstract::getAttributeCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract) of method Mage_ImportExport_Model_Import_Entity_Product_Type_Configurable::_addAttributeParams() should be covariant with return type ($this(Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract)) of method Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract::_addAttributeParams()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract) of method Mage_ImportExport_Model_Import_Entity_Product_Type_Configurable::saveData() should be covariant with return type ($this(Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract)) of method Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract::saveData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract) of method Mage_ImportExport_Model_Import_Entity_Product_Type_Grouped::saveData() should be covariant with return type ($this(Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract)) of method Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract::saveData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Grouped.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Collection_Abstract) of method Mage_Log_Model_Resource_Visitor_Collection::load() should be covariant with return type ($this(Varien_Data_Collection_Db<T of Varien_Object>)) of method Varien_Data_Collection_Db<Mage_Core_Model_Abstract>::load()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Visitor/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Collection_Abstract) of method Mage_Log_Model_Resource_Visitor_Online_Collection::addFieldToFilter() should be covariant with return type ($this(Varien_Data_Collection_Db<T of Varien_Object>)) of method Varien_Data_Collection_Db<Mage_Core_Model_Abstract>::addFieldToFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Visitor/Online/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Db_Adapter_Interface|false) of method Mage_Media_Model_File_Image::_getReadAdapter() should be covariant with return type (Varien_Db_Adapter_Interface) of method Mage_Core_Model_Resource_Abstract::_getReadAdapter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/File/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Db_Adapter_Interface|false) of method Mage_Media_Model_File_Image::_getWriteAdapter() should be covariant with return type (Varien_Db_Adapter_Interface) of method Mage_Core_Model_Resource_Abstract::_getWriteAdapter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Media/Model/File/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Page_Block_Html_Breadcrumbs::getCacheKeyInfo() should be covariant with return type (array<int|string, string>) of method Mage_Core_Block_Template::getCacheKeyInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Breadcrumbs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array<int, mixed>) of method Mage_Page_Block_Html_Footer::getCacheKeyInfo() should be covariant with return type (array<int|string, string>) of method Mage_Core_Block_Template::getCacheKeyInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Footer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Page_Block_Html_Topmenu::getCacheKeyInfo() should be covariant with return type (array<int|string, string>) of method Mage_Core_Block_Template::getCacheKeyInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Topmenu.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Page_Block_Template_Links::getCacheKeyInfo() should be covariant with return type (array<int|string, string>) of method Mage_Core_Block_Template::getCacheKeyInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Template/Links.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (mixed) of method Mage_Paygate_Block_Authorizenet_Info_Cc::getInfo() should be covariant with return type (Mage_Payment_Model_Info) of method Mage_Payment_Block_Info::getInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Block/Authorizenet/Info/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|null) of method Mage_Payment_Model_Method_Free::getConfigPaymentAction() should be covariant with return type (string) of method Mage_Payment_Model_Method_Abstract::getConfigPaymentAction()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Method/Free.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Expanded::_getCollapseState() should be covariant with return type (int|false) of method Mage_Adminhtml_Block_System_Config_Form_Fieldset::_getCollapseState()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Fieldset/Expanded.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Payment::_getCollapseState() should be covariant with return type (int|false) of method Mage_Adminhtml_Block_System_Config_Form_Fieldset::_getCollapseState()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Fieldset/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (false) of method Mage_Paypal_Block_Hosted_Pro_Info::getCcTypeName() should be compatible with return type (string|null) of method Mage_Paypal_Block_Payment_Info::getCcTypeName()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Hosted/Pro/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (false) of method Mage_Paypal_Block_Payflow_Link_Info::getCcTypeName() should be compatible with return type (string|null) of method Mage_Paypal_Block_Payment_Info::getCcTypeName()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Payflow/Link/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|null) of method Mage_Paypal_Block_Payment_Info::getCcTypeName() should be covariant with return type (string) of method Mage_Payment_Block_Info_Cc::getCcTypeName()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Payment/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool|void) of method Mage_Paypal_Model_Api_Nvp::_exportLineItems() should be covariant with return type (bool) of method Mage_Paypal_Model_Api_Abstract::_exportLineItems()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Paypal_Model_Hostedpro::getAllowedCcTypes() should be compatible with return type (string) of method Mage_Paypal_Model_Direct::getAllowedCcTypes()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Hostedpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Paypal_Model_Hostedpro::validate() should be compatible with return type ($this(Mage_Payment_Model_Method_Cc)) of method Mage_Payment_Model_Method_Cc::validate()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Hostedpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Paypal_Model_Payflowlink::validate() should be compatible with return type ($this(Mage_Payment_Model_Method_Cc)) of method Mage_Payment_Model_Method_Cc::validate()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowlink.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type ($this(Mage_Paypal_Model_Payflowpro)|false) of method Mage_Paypal_Model_Payflowpro::cancel() should be covariant with return type ($this(Mage_Payment_Model_Method_Abstract)) of method Mage_Payment_Model_Method_Abstract::cancel()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Abstract) of method Mage_Paypal_Model_Resource_Cert::_beforeSave() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Abstract)) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Resource/Cert.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (void) of method Mage_PaypalUk_Model_Pro::fetchTransactionInfo() should be compatible with return type (array) of method Mage_Paypal_Model_Pro::fetchTransactionInfo()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/PaypalUk/Model/Pro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Zend_Db_Select) of method Mage_Persistent_Model_Resource_Session::_getLoadSelect() should be covariant with return type (Varien_Db_Select) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Resource/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array<int, mixed>) of method Mage_Reports_Model_Product_Index_Viewed::getExcludeProductIds() should be covariant with return type (array<int|void>) of method Mage_Reports_Model_Product_Index_Abstract::getExcludeProductIds()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Product/Index/Viewed.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Reports_Model_Resource_Product_Index_Abstract) of method Mage_Reports_Model_Resource_Product_Index_Abstract::save() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Abstract)) of method Mage_Core_Model_Resource_Db_Abstract::save()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Index/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Catalog_Model_Resource_Product_Collection) of method Mage_Reports_Model_Resource_Product_Index_Collection_Abstract::_afterLoad() should be covariant with return type ($this(Mage_Catalog_Model_Resource_Product_Collection)) of method Mage_Catalog_Model_Resource_Product_Collection::_afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Index/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Catalog_Model_Resource_Product_Collection) of method Mage_Reports_Model_Resource_Product_Viewed_Collection::_beforeLoad() should be covariant with return type ($this(Mage_Catalog_Model_Resource_Product_Collection)) of method Mage_Catalog_Model_Resource_Product_Collection::_beforeLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Viewed/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string) of method Mage_Review_Block_Form::getAction() should be compatible with return type (Mage_Core_Controller_Varien_Action) of method Mage_Core_Block_Abstract::getAction()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Zend_Db_Select) of method Mage_Review_Model_Resource_Review::_getLoadSelect() should be covariant with return type (Varien_Db_Select) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Db_Select|null) of method Mage_Review_Model_Resource_Review_Product_Collection::getSelectCountSql() should be covariant with return type (Varien_Db_Select) of method Mage_Catalog_Model_Resource_Product_Collection::getSelectCountSql()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Controller_Front_Action|null) of method Mage_Review_ProductController::preDispatch() should be covariant with return type ($this(Mage_Core_Controller_Front_Action)) of method Mage_Core_Controller_Front_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/controllers/ProductController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Rule_Model_Action_Abstract) of method Mage_Rule_Model_Action_Collection::process() should be covariant with return type ($this(Mage_Rule_Model_Action_Abstract)) of method Mage_Rule_Model_Action_Abstract::process()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Action/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Rule_Model_Action_Collection::asArray() should be covariant with return type (array<string, string>) of method Mage_Rule_Model_Action_Abstract::asArray()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Action/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Rule_Model_Condition_Abstract) of method Mage_Rule_Model_Condition_Combine::loadValueOptions() should be covariant with return type ($this(Mage_Rule_Model_Condition_Abstract)) of method Mage_Rule_Model_Condition_Abstract::loadValueOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Combine.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Rule_Model_Condition_Combine::asArray() should be covariant with return type (array<string, array<mixed>|bool|int|string|null>) of method Mage_Rule_Model_Condition_Abstract::asArray()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Combine.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Rule_Model_Resource_Rule_Collection_Abstract) of method Mage_Rule_Model_Resource_Rule_Collection_Abstract::addFieldToFilter() should be covariant with return type ($this(Varien_Data_Collection_Db<T of Varien_Object>)) of method Varien_Data_Collection_Db<T of Mage_Rule_Model_Abstract>::addFieldToFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Resource/Rule/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Block_Widget_Grid) of method Mage_Sales_Block_Adminhtml_Billing_Agreement_View_Tab_Orders::_prepareCollection() should be covariant with return type ($this(Mage_Adminhtml_Block_Sales_Order_Grid)) of method Mage_Adminhtml_Block_Sales_Order_Grid::_prepareCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Adminhtml/Billing/Agreement/View/Tab/Orders.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Block_Widget_Grid) of method Mage_Sales_Block_Adminhtml_Customer_Edit_Tab_Agreement::_prepareCollection() should be covariant with return type ($this(Mage_Sales_Block_Adminhtml_Billing_Agreement_Grid)) of method Mage_Sales_Block_Adminhtml_Billing_Agreement_Grid::_prepareCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Adminhtml/Customer/Edit/Tab/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Block_Widget_Grid) of method Mage_Sales_Block_Adminhtml_Customer_Edit_Tab_Recurring_Profile::_prepareCollection() should be covariant with return type ($this(Mage_Sales_Block_Adminhtml_Recurring_Profile_Grid)) of method Mage_Sales_Block_Adminhtml_Recurring_Profile_Grid::_prepareCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Adminhtml/Customer/Edit/Tab/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Object|null) of method Mage_Sales_Block_Payment_Info_Billing_Agreement::_prepareSpecificInformation() should be covariant with return type (Varien_Object) of method Mage_Payment_Block_Info::_prepareSpecificInformation()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Payment/Info/Billing/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|false) of method Mage_Sales_Model_Email_Template::getInclude() should be covariant with return type (string) of method Mage_Core_Model_Email_Template::getInclude()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Email/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract) of method Mage_Sales_Model_Entity_Order_Creditmemo_Attribute_Backend_Child::beforeSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Creditmemo/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract) of method Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Child::beforeSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract) of method Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Item::afterSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice/Attribute/Backend/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract) of method Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Order::beforeSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Invoice/Attribute/Backend/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract) of method Mage_Sales_Model_Entity_Order_Shipment_Attribute_Backend_Child::beforeSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Shipment/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Payment_Model_Method_Abstract) of method Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract::assignData() should be covariant with return type ($this(Mage_Payment_Model_Method_Abstract)) of method Mage_Payment_Model_Method_Abstract::assignData()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Payment/Method/Billing/AgreementAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Catalog_Model_Product_Configuration_Item_Option_Interface|null) of method Mage_Sales_Model_Quote_Address_Item::getOptionByCode() should be covariant with return type (Mage_Catalog_Model_Product_Configuration_Item_Option_Interface) of method Mage_Catalog_Model_Product_Configuration_Item_Interface::getOptionByCode()',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Sales_Model_Quote_Address_Total_Nominal_RecurringAbstract::_getAddressItems() should be covariant with return type (array<Mage_Sales_Model_Quote_Address_Item>) of method Mage_Sales_Model_Quote_Address_Total_Abstract::_getAddressItems()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Nominal/RecurringAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Sales_Model_Quote_Address_Total_Nominal_Shipping::_getAddressItems() should be covariant with return type (array<Mage_Sales_Model_Quote_Address_Item>) of method Mage_Sales_Model_Quote_Address_Total_Abstract::_getAddressItems()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Nominal/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|Mage_Sales_Model_Quote_Address_Total_Abstract) of method Mage_Sales_Model_Quote_Address_Total_Nominal_Shipping::fetch() should be covariant with return type ($this(Mage_Sales_Model_Quote_Address_Total_Shipping)) of method Mage_Sales_Model_Quote_Address_Total_Shipping::fetch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Nominal/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Sales_Model_Quote_Address_Total_Nominal_Subtotal::_getAddressItems() should be covariant with return type (array<Mage_Sales_Model_Quote_Address_Item>) of method Mage_Sales_Model_Quote_Address_Total_Abstract::_getAddressItems()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Nominal/Subtotal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|Mage_Sales_Model_Quote_Address_Total_Abstract) of method Mage_Sales_Model_Quote_Address_Total_Nominal_Subtotal::fetch() should be covariant with return type ($this(Mage_Sales_Model_Quote_Address_Total_Subtotal)) of method Mage_Sales_Model_Quote_Address_Total_Subtotal::fetch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Nominal/Subtotal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Sales_Model_Quote_Item_Option|null) of method Mage_Sales_Model_Quote_Item::getOptionByCode() should be covariant with return type (Mage_Catalog_Model_Product_Configuration_Item_Option_Interface) of method Mage_Catalog_Model_Product_Configuration_Item_Interface::getOptionByCode()',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Abstract) of method Mage_Sales_Model_Resource_Order_Address::_afterSave() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Abstract)) of method Mage_Core_Model_Resource_Db_Abstract::_afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract) of method Mage_Sales_Model_Resource_Order_Creditmemo_Attribute_Backend_Child::beforeSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Creditmemo/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract) of method Mage_Sales_Model_Resource_Order_Invoice_Attribute_Backend_Child::beforeSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Invoice/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract) of method Mage_Sales_Model_Resource_Order_Invoice_Attribute_Backend_Item::afterSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Invoice/Attribute/Backend/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract) of method Mage_Sales_Model_Resource_Order_Shipment_Attribute_Backend_Child::beforeSave() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Shipment/Attribute/Backend/Child.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Collection_Abstract) of method Mage_Sales_Model_Resource_Quote_Payment_Collection::_afterLoad() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Collection_Abstract<T of Mage_Core_Model_Abstract>)) of method Mage_Core_Model_Resource_Db_Collection_Abstract<Mage_Sales_Model_Quote_Payment>::_afterLoad()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Quote/Payment/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type ($this(Mage_Sales_Billing_AgreementController)|null) of method Mage_Sales_Billing_AgreementController::preDispatch() should be covariant with return type ($this(Mage_Core_Controller_Front_Action)) of method Mage_Core_Controller_Front_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/controllers/Billing/AgreementController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type ($this(Mage_Sales_Recurring_ProfileController)|null) of method Mage_Sales_Recurring_ProfileController::preDispatch() should be covariant with return type ($this(Mage_Core_Controller_Front_Action)) of method Mage_Core_Controller_Front_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/controllers/Recurring/ProfileController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_SalesRule_Model_Quote_Nominal_Discount::_getAddressItems() should be covariant with return type (array<Mage_Sales_Model_Quote_Address_Item>) of method Mage_Sales_Model_Quote_Address_Total_Abstract::_getAddressItems()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Quote/Nominal/Discount.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Core_Model_Resource_Db_Abstract) of method Mage_SalesRule_Model_Resource_Coupon::_beforeSave() should be covariant with return type ($this(Mage_Core_Model_Resource_Db_Abstract)) of method Mage_Core_Model_Resource_Db_Abstract::_beforeSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Coupon.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Sales_Model_Resource_Report_Collection_Abstract) of method Mage_SalesRule_Model_Resource_Report_Collection::_applyCustomFilter() should be covariant with return type ($this(Mage_Sales_Model_Resource_Report_Collection_Abstract)) of method Mage_Sales_Model_Resource_Report_Collection_Abstract::_applyCustomFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Report/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_SalesRule_Model_Rule_Condition_Product_Combine) of method Mage_SalesRule_Model_Rule::getActionsInstance() should be compatible with return type (Mage_Rule_Model_Action_Collection) of method Mage_Rule_Model_Abstract::getActionsInstance()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_SalesRule_Model_Rule_Action_Collection::getNewChildSelectOptions() should be covariant with return type (array<int, array<string, string>>) of method Mage_Rule_Model_Action_Abstract::getNewChildSelectOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Action/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Rule_Model_Action_Abstract) of method Mage_SalesRule_Model_Rule_Action_Product::loadAttributeOptions() should be covariant with return type ($this(Mage_Rule_Model_Action_Abstract)) of method Mage_Rule_Model_Action_Abstract::loadAttributeOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Action/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Rule_Model_Action_Abstract) of method Mage_SalesRule_Model_Rule_Action_Product::loadOperatorOptions() should be covariant with return type ($this(Mage_Rule_Model_Action_Abstract)) of method Mage_Rule_Model_Action_Abstract::loadOperatorOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Action/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Rule_Model_Condition_Abstract) of method Mage_SalesRule_Model_Rule_Condition_Address::loadAttributeOptions() should be covariant with return type ($this(Mage_Rule_Model_Condition_Abstract)) of method Mage_Rule_Model_Condition_Abstract::loadAttributeOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (mixed) of method Mage_SalesRule_Model_Rule_Condition_Address::getValueSelectOptions() should be covariant with return type (array) of method Mage_Rule_Model_Condition_Abstract::getValueSelectOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_SalesRule_Model_Rule_Condition_Combine::getNewChildSelectOptions() should be covariant with return type (array<int, array<string, string>>) of method Mage_Rule_Model_Condition_Abstract::getNewChildSelectOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Combine.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_SalesRule_Model_Rule_Condition_Product_Combine::getNewChildSelectOptions() should be covariant with return type (array<int, array<string, string>>) of method Mage_Rule_Model_Condition_Abstract::getNewChildSelectOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Product/Combine.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_SalesRule_Model_Rule_Condition_Product_Combine) of method Mage_SalesRule_Model_Rule_Condition_Product_Subselect::loadArray() should be covariant with return type ($this(Mage_Rule_Model_Condition_Combine)) of method Mage_Rule_Model_Condition_Combine::loadArray()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Product/Subselect.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_SalesRule_Model_Rule_Condition_Product_Combine) of method Mage_SalesRule_Model_Rule_Condition_Product_Subselect::loadAttributeOptions() should be covariant with return type ($this(Mage_Rule_Model_Condition_Abstract)) of method Mage_Rule_Model_Condition_Abstract::loadAttributeOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Product/Subselect.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_SalesRule_Model_Rule_Condition_Product_Combine) of method Mage_SalesRule_Model_Rule_Condition_Product_Subselect::loadOperatorOptions() should be covariant with return type ($this(Mage_Rule_Model_Condition_Abstract)) of method Mage_Rule_Model_Condition_Abstract::loadOperatorOptions()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Product/Subselect.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|false) of method Mage_Sitemap_Model_Resource_Catalog_Category::getCollection() should be covariant with return type (array) of method Mage_Sitemap_Model_Resource_Catalog_Abstract::getCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sitemap/Model/Resource/Catalog/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|false) of method Mage_Sitemap_Model_Resource_Catalog_Product::getCollection() should be covariant with return type (array) of method Mage_Sitemap_Model_Resource_Catalog_Abstract::getCollection()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sitemap/Model/Resource/Catalog/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|object) of method Mage_Tag_Model_Api_V2::info() should be covariant with return type (array<string, array<int|string, int>|int|string>) of method Mage_Tag_Model_Api::info()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Collection_Abstract) of method Mage_Tag_Model_Entity_Customer_Collection::load() should be covariant with return type ($this(Mage_Eav_Model_Entity_Collection_Abstract<T of Mage_Core_Model_Abstract>)) of method Mage_Eav_Model_Entity_Collection_Abstract<Mage_Customer_Model_Customer>::load()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Entity/Customer/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Zend_Db_Select) of method Mage_Tag_Model_Resource_Tag::_getLoadSelect() should be covariant with return type (Varien_Db_Select) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Tag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Tax_Model_Sales_Pdf_Grandtotal::getTotalsForDisplay() should be covariant with return type (array<int, array<string, int|string>>) of method Mage_Sales_Model_Order_Pdf_Total_Default::getTotalsForDisplay()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Pdf/Grandtotal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Tax_Model_Sales_Pdf_Shipping::getTotalsForDisplay() should be covariant with return type (array<int, array<string, int|string>>) of method Mage_Sales_Model_Order_Pdf_Total_Default::getTotalsForDisplay()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Pdf/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Tax_Model_Sales_Pdf_Subtotal::getTotalsForDisplay() should be covariant with return type (array<int, array<string, int|string>>) of method Mage_Sales_Model_Order_Pdf_Total_Default::getTotalsForDisplay()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Pdf/Subtotal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Tax_Model_Sales_Pdf_Tax::getTotalsForDisplay() should be covariant with return type (array<int, array<string, int|string>>) of method Mage_Sales_Model_Order_Pdf_Total_Default::getTotalsForDisplay()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Pdf/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Tax_Model_Sales_Total_Quote_Nominal_Subtotal::_getAddressItems() should be covariant with return type (array<Mage_Sales_Model_Quote_Address_Item>) of method Mage_Sales_Model_Quote_Address_Total_Abstract::_getAddressItems()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Nominal/Subtotal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Tax_Model_Sales_Total_Quote_Nominal_Tax::_getAddressItems() should be covariant with return type (array<Mage_Sales_Model_Quote_Address_Item>) of method Mage_Sales_Model_Quote_Address_Total_Abstract::_getAddressItems()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Nominal/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|Mage_Sales_Model_Quote_Address_Total_Abstract) of method Mage_Tax_Model_Sales_Total_Quote_Nominal_Tax::fetch() should be covariant with return type ($this(Mage_Tax_Model_Sales_Total_Quote_Tax)) of method Mage_Tax_Model_Sales_Total_Quote_Tax::fetch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Nominal/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Mage_Usa_Model_Shipping_Carrier_Abstract::isZipCodeRequired() should be covariant with return type (false) of method Mage_Shipping_Model_Carrier_Abstract::isZipCodeRequired()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool|Mage_Shipping_Model_Carrier_Abstract|Mage_Shipping_Model_Rate_Result_Error) of method Mage_Usa_Model_Shipping_Carrier_Abstract::proccessAdditionalValidation() should be covariant with return type ($this(Mage_Shipping_Model_Carrier_Abstract)) of method Mage_Shipping_Model_Carrier_Abstract::proccessAdditionalValidation()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|null) of method Mage_Usa_Model_Shipping_Carrier_Abstract::getCarrierCode() should be covariant with return type (string) of method Mage_Shipping_Model_Carrier_Abstract::getCarrierCode()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|bool) of method Mage_Usa_Model_Shipping_Carrier_Dhl::getContainerTypes() should be covariant with return type (array<string|void>) of method Mage_Shipping_Model_Carrier_Abstract::getContainerTypes()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Usa_Model_Shipping_Carrier_Fedex::getDeliveryConfirmationTypes() should be covariant with return type (array<string|void>) of method Mage_Shipping_Model_Carrier_Abstract::getDeliveryConfirmationTypes()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|bool) of method Mage_Usa_Model_Shipping_Carrier_Fedex::getContainerTypes() should be covariant with return type (array<string|void>) of method Mage_Shipping_Model_Carrier_Abstract::getContainerTypes()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|bool) of method Mage_Usa_Model_Shipping_Carrier_Ups::getContainerTypes() should be covariant with return type (array<string|void>) of method Mage_Shipping_Model_Carrier_Abstract::getContainerTypes()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array<int|string, array<string, string>|string>) of method Mage_Usa_Model_Shipping_Carrier_Usps::getDeliveryConfirmationTypes() should be covariant with return type (array<string|void>) of method Mage_Shipping_Model_Carrier_Abstract::getDeliveryConfirmationTypes()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array<string, string>|bool) of method Mage_Usa_Model_Shipping_Carrier_Usps::getContainerTypes() should be covariant with return type (array<string|void>) of method Mage_Shipping_Model_Carrier_Abstract::getContainerTypes()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Catalog_Model_Product_Attribute_Backend_Price) of method Mage_Weee_Model_Attribute_Backend_Weee_Tax::afterSave() should be covariant with return type ($this(Mage_Catalog_Model_Product_Attribute_Backend_Price)) of method Mage_Catalog_Model_Product_Attribute_Backend_Price::afterSave()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Eav_Model_Entity_Attribute_Backend_Abstract) of method Mage_Weee_Model_Attribute_Backend_Weee_Tax::afterDelete() should be covariant with return type ($this(Mage_Eav_Model_Entity_Attribute_Backend_Abstract)) of method Mage_Eav_Model_Entity_Attribute_Backend_Abstract::afterDelete()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Sales_Model_Order_Creditmemo_Total_Abstract) of method Mage_Weee_Model_Total_Creditmemo_Weee::collect() should be covariant with return type ($this(Mage_Sales_Model_Order_Creditmemo_Total_Abstract)) of method Mage_Sales_Model_Order_Creditmemo_Total_Abstract::collect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Total/Creditmemo/Weee.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Mage_Weee_Model_Total_Quote_Nominal_Weee::_getAddressItems() should be covariant with return type (array<Mage_Sales_Model_Quote_Address_Item>) of method Mage_Sales_Model_Quote_Address_Total_Abstract::_getAddressItems()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Total/Quote/Nominal/Weee.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Data_Form_Element_Abstract|false) of method Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Properties::_addField() should be covariant with return type (Varien_Data_Form_Element_Abstract) of method Mage_Widget_Block_Adminhtml_Widget_Options::_addField()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Block/Adminhtml/Widget/Instance/Edit/Tab/Properties.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Adminhtml_Controller_Action) of method Mage_Widget_Adminhtml_Widget_InstanceController::preDispatch() should be covariant with return type ($this(Mage_Adminhtml_Controller_Action)) of method Mage_Adminhtml_Controller_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/controllers/Adminhtml/Widget/InstanceController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Mage_Wishlist_Model_Item_Option|null) of method Mage_Wishlist_Model_Item::getOptionByCode() should be covariant with return type (Mage_Catalog_Model_Product_Configuration_Item_Option_Interface) of method Mage_Catalog_Model_Product_Configuration_Item_Interface::getOptionByCode()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Zend_Db_Select) of method Mage_Wishlist_Model_Resource_Wishlist::_getLoadSelect() should be covariant with return type (Varien_Db_Select) of method Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Resource/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type ($this(Mage_Wishlist_IndexController)|null) of method Mage_Wishlist_IndexController::preDispatch() should be covariant with return type ($this(Mage_Core_Controller_Front_Action)) of method Mage_Core_Controller_Front_Action::preDispatch()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|bool) of method Mage_Cache_Backend_File::_getMetadatas() should be covariant with return type (array|false) of method Zend_Cache_Backend_File::_getMetadatas()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|string) of method Mage_Cache_Backend_File::_path() should be covariant with return type (string) of method Zend_Cache_Backend_File::_path()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool|string) of method Mage_Cache_Backend_File::load() should be covariant with return type (string|false) of method Zend_Cache_Backend_File::load()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool|string) of method Mage_Cache_Backend_File::load() should be covariant with return type (string|false) of method Zend_Cache_Backend_Interface::load()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (float|string) of method Magento_Db_Adapter_Pdo_Mysql::_quote() should be covariant with return type (string) of method Zend_Db_Adapter_Pdo_Abstract::_quote()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|false) of method Varien_Cache_Backend_Database::getMetadatas() should be covariant with return type (array) of method Zend_Cache_Backend_ExtendedInterface::getMetadatas()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Backend/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (int|true) of method Varien_Cache_Backend_Database::touch() should be covariant with return type (bool) of method Zend_Cache_Backend_ExtendedInterface::touch()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Backend/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool|string) of method Varien_Cache_Backend_Memcached::load() should be covariant with return type (string|false) of method Zend_Cache_Backend_Interface::load()',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Cache/Backend/Memcached.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool|string) of method Varien_Cache_Backend_Memcached::load() should be covariant with return type (string|false) of method Zend_Cache_Backend_Memcached::load()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Cache/Backend/Memcached.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (int) of method Varien_Data_Collection::count() should be covariant with return type (int<0, max>) of method Countable::count()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Data_Form_Abstract) of method Varien_Data_Form::removeField() should be covariant with return type ($this(Varien_Data_Form_Abstract)) of method Varien_Data_Form_Abstract::removeField()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (mixed) of method Varien_Data_Form_Element_Hidden::getDefaultHtml() should be covariant with return type (string) of method Varien_Data_Form_Element_Abstract::getDefaultHtml()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Hidden.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|null) of method Varien_Data_Form_Filter_Date::inputFilter() should be covariant with return type (string) of method Varien_Data_Form_Filter_Interface::inputFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Filter/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (string|null) of method Varien_Data_Form_Filter_Datetime::inputFilter() should be covariant with return type (string) of method Varien_Data_Form_Filter_Interface::inputFilter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Filter/Datetime.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Varien_Data_Tree) of method Varien_Data_Tree_Db::removeNode() should be covariant with return type ($this(Varien_Data_Tree)) of method Varien_Data_Tree::removeNode()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Tree/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type ($this(Varien_Db_Adapter_Pdo_Mysql)) of method Varien_Db_Adapter_Pdo_Mysql::dropTemporaryTable() should be compatible with return type (bool) of method Varien_Db_Adapter_Interface::dropTemporaryTable()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (PDOStatement|Zend_Db_Statement_Interface) of method Varien_Db_Adapter_Pdo_Mysql::addForeignKey() should be covariant with return type (Varien_Db_Adapter_Interface) of method Varien_Db_Adapter_Interface::addForeignKey()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (PDOStatement|Zend_Db_Statement_Interface) of method Varien_Db_Adapter_Pdo_Mysql::changeColumn() should be covariant with return type (Varien_Db_Adapter_Interface) of method Varien_Db_Adapter_Interface::changeColumn()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (PDOStatement|Zend_Db_Statement_Interface) of method Varien_Db_Adapter_Pdo_Mysql::createTable() should be covariant with return type (Zend_Db_Statement_Interface) of method Varien_Db_Adapter_Interface::createTable()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (PDOStatement|Zend_Db_Statement_Interface) of method Varien_Db_Adapter_Pdo_Mysql::createTemporaryTable() should be covariant with return type (Zend_Db_Statement_Interface) of method Varien_Db_Adapter_Interface::createTemporaryTable()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (PDOStatement|Zend_Db_Statement_Interface) of method Varien_Db_Adapter_Pdo_Mysql::query() should be covariant with return type (Zend_Db_Statement_Interface) of method Varien_Db_Adapter_Interface::query()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (Zend_Db_Statement_Interface|true) of method Varien_Db_Adapter_Pdo_Mysql::dropColumn() should be covariant with return type (bool) of method Varien_Db_Adapter_Interface::dropColumn()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array) of method Varien_Db_Adapter_Pdo_Mysql::multiQuery() should be compatible with return type (Varien_Db_Adapter_Interface) of method Varien_Db_Adapter_Interface::multiQuery()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool) of method Varien_Db_Adapter_Pdo_Mysql::showTableStatus() should be covariant with return type (array|false) of method Varien_Db_Adapter_Interface::showTableStatus()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool|Zend_Db_Statement_Interface) of method Varien_Db_Adapter_Pdo_Mysql::addColumn() should be covariant with return type (Varien_Db_Adapter_Interface) of method Varien_Db_Adapter_Interface::addColumn()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array<mixed>|Varien_Object) of method Varien_Filter_Object_Grid::filter() should be covariant with return type (Varien_Object) of method Varien_Filter_Object::filter()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/Object/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (bool|string) of method Varien_Http_Adapter_Curl::read() should be covariant with return type (string) of method Zend_Http_Client_Adapter_Interface::read()',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Return type (array|false) of method Zend_Cache_Backend@anonymous/tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php:48::getMetadatas() should be covariant with return type (array) of method Zend_Cache_Backend_ExtendedInterface::getMetadatas()',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/Purifier/DefinitionCacheTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
