<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method checkConfigurableProducts() of class Mage_Eav_Model_Resource_Entity_Attribute_Collection.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Attribute/Set/Main.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getGroupCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Websites.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getStoreCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Websites.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getWebsiteCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Websites.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method addTemplateInfo() of class Mage_Newsletter_Model_Resource_Queue_Collection:
since 1.4.0.1',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Newsletter/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method addDaysInWishlist() of class Mage_Wishlist_Model_Resource_Item_Collection:
after 1.4.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/View/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method addDaysInWishlist() of class Mage_Wishlist_Model_Resource_Item_Collection:
after 1.4.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method isValidForSend() of class Mage_Newsletter_Model_Template:
since 1.4.0.1',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Template/Grid/Renderer/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getTaxAmount() of class Mage_Sales_Model_Quote_Item_Abstract.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Items/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getSaveUrl() of class Mage_Adminhtml_Block_Widget_Form_Container.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Form/Container.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method submit() of class Mage_Sales_Model_Service_Quote:
after 1.4.0.1',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _getCollectionNames() of class Mage_Adminhtml_Report_StatisticsController:
after 1.4.0.1',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Report/StatisticsController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getSelectionPrice() of class Mage_Bundle_Model_Product_Price:
after 1.6.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCheckoutMethod() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _convertPrice() of class Mage_Catalog_Block_Product_View_Type_Configurable.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _prepareOldPrice() of class Mage_Catalog_Block_Product_View_Type_Configurable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _preparePrice() of class Mage_Catalog_Block_Product_View_Type_Configurable.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _registerJsPrice() of class Mage_Catalog_Block_Product_View_Type_Configurable.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCurrentStore() of class Mage_Catalog_Block_Product_View_Type_Configurable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method clear() of class Mage_Eav_Model_Config.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Entity/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getIsGlobal() of class Mage_Eav_Model_Entity_Attribute_Abstract:
moved to catalog attribute model',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method setDestination() of class Zend_File_Transfer_Adapter_Abstract:
Will be changed to be a filter!!!',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getIsGlobal() of class Mage_Eav_Model_Entity_Attribute_Abstract:
moved to catalog attribute model',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getIsGlobal() of class Mage_Catalog_Model_Resource_Eav_Attribute:
moved to catalog attribute model',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method clear() of class Mage_Eav_Model_Config.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Eav/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getIsGlobal() of class Mage_Catalog_Model_Resource_Eav_Attribute:
moved to catalog attribute model',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Eav/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method loadParentProductIds() of class Mage_Catalog_Model_Product:
after 1.4.2.0',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _prepareCondition() of class Mage_CatalogSearch_Model_Advanced:
after 1.4.1.0 - use Mage_CatalogSearch_Model_Resource_Advanced->_prepareCondition()',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Advanced.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method updateCategoryIndex() of class Mage_CatalogSearch_Model_Fulltext:
after 1.6.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Indexer/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCheckoutMethod() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Onepage/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCheckoutMethod() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Onepage/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCheckoutMethod() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Api/Resource/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCheckoutMethod() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCheckoutMethod() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Onepage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method isAllowedGuestCheckout() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Onepage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method isInstalled() of class Mage_Core_Model_App:
since 1.2',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout/Update.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method revalidateCookie() of class Mage_Core_Model_Session_Abstract_Varien:
after 1.4 cookie renew moved to session start method',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Varien.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method isCurrentlySecure() of class Mage_Core_Model_Store.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getSuggestedZeroDate() of interface Varien_Db_Adapter_Interface:
after 1.5.1.0',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/sql/customer_setup/install-1.6.0.0.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _getFileTypeByExt() of class Mage_Downloadable_Helper_File.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method clear() of class Mage_Eav_Model_Config.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _afterSetConfig() of class Mage_Eav_Model_Entity_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getSuggestedZeroDate() of interface Varien_Db_Adapter_Interface:
after 1.5.1.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/sql/eav_setup/install-1.6.0.0.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getIsGlobal() of class Mage_Eav_Model_Entity_Attribute_Abstract:
moved to catalog attribute model',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getIsGlobal() of class Mage_Eav_Model_Entity_Attribute_Abstract:
moved to catalog attribute model',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getIsGlobal() of class Mage_Eav_Model_Entity_Attribute_Abstract:
moved to catalog attribute model',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _getHtml() of class Mage_Page_Block_Html_Topmenu:
since 1.8.2.0 use child block catalog.topnav.renderer instead',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Topmenu.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getFormated() of class Mage_Customer_Model_Address_Abstract:
for public function format',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Express/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCheckoutMethod() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCheckoutMethod() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express/Checkout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method setJoinFlag() of class Mage_Tag_Model_Resource_Product_Collection:
after 1.3.2.3',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Tag/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getFormated() of class Mage_Customer_Model_Address_Abstract:
for public function format',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Recurring/Profile/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method addStatusToHistory() of class Mage_Sales_Model_Order:
after 1.4.0.0-alpha3',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getTotalModels() of class Mage_Sales_Model_Quote_Address.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method calcTaxAmount() of class Mage_Sales_Model_Quote_Item_Abstract:
logic moved to tax totals calculation model',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getBaseTaxAmount() of class Mage_Sales_Model_Quote_Item_Abstract.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getTaxAmount() of class Mage_Sales_Model_Quote_Item_Abstract.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getBaseTaxAmount() of class Mage_Sales_Model_Quote_Item_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _getTrackingUrl() of class Mage_Shipping_Helper_Data:
the non-model usage',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method addVisibleFilterToCollection() of class Mage_Catalog_Model_Product_Status:
remove on new builds',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Customer/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method aggregate() of class Mage_Tag_Model_Resource_Tag:
after 1.4.0.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Tag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getBaseTaxAmount() of class Mage_Sales_Model_Quote_Item_Abstract.',
    'count' => 9,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getTaxAmount() of class Mage_Sales_Model_Quote_Item_Abstract.',
    'count' => 9,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _processLocalizedQty() of class Mage_Wishlist_Controller_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Controller/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method _processLocalizedQty() of class Mage_Wishlist_Controller_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getGroupCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/edit/websites.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getStoreCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/edit/websites.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getWebsiteCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/edit/websites.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getGridUrl() of class Mage_Adminhtml_Block_Widget_Grid:
after 1.3.2.3 Use getAbsoluteGridUrl() method instead',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/dashboard/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getRowId() of class Mage_Adminhtml_Block_Widget_Grid:
since 1.1.7',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/dashboard/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getIsPriceWebsiteScope() of class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links:
since 1.14.2.0',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/downloadable/product/edit/downloadable/links.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getHtmlId() of class Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default:
after 1.4.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/downloadable/sales/order/view/items/renderer/downloadable.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getGridUrl() of class Mage_Adminhtml_Block_Widget_Grid:
after 1.3.2.3 Use getAbsoluteGridUrl() method instead',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getGroupCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getStoreCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getWebsiteCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getGroupCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher/enhanced.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getStoreCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher/enhanced.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getWebsiteCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher/enhanced.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getGroupCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/create/store/select.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getStoreCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/create/store/select.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getWebsiteCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/create/store/select.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getHtmlId() of class Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default:
after 1.4.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/view/items/renderer/default.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getGroupCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/store/switcher/enhanced.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getStoreCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/store/switcher/enhanced.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getWebsiteCollection() of class Mage_Adminhtml_Block_Store_Switcher.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/store/switcher/enhanced.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getSaveUrl() of class Mage_Adminhtml_Block_Tag_Edit.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/tag/edit/container.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getGridUrl() of class Mage_Adminhtml_Block_Widget_Grid:
after 1.3.2.3 Use getAbsoluteGridUrl() method instead',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/widget/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCheckoutMethod() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method isAllowedGuestCheckout() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 6,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCheckoutMethod() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method isAllowedGuestCheckout() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getCheckoutMethod() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method isAllowedGuestCheckout() of class Mage_Sales_Model_Quote:
after 1.4 beta1 it is checkout module responsibility',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method sendNewPasswordEmail() of class Mage_Admin_Model_User:
deprecated since version 1.6.1.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Admin/Model/UserTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getPopupObjectUrl() of class Mage_AdminNotification_Helper_Data:
v19.4.16',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/AdminNotification/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getChartDataHash() of class Mage_Adminhtml_Helper_Dashboard_Data.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Adminhtml/Helper/Dashboard/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method addPageHelpUrl() of class Mage_Adminhtml_Helper_Data.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Adminhtml/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getPageHelpUrl() of class Mage_Adminhtml_Helper_Data.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Adminhtml/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method setPageHelpUrl() of class Mage_Adminhtml_Helper_Data.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Adminhtml/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method format() of class Mage_Catalog_Helper_Product_Url.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Catalog/Helper/Product/UrlTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method isSwfDisabled() of class Mage_Cms_Helper_Data:
since 19.5.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Cms/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getRegionJson() of class Mage_Directory_Helper_Data:
after 1.7.0.2',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Directory/Helper/DataTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method lockIndexer() of class Mage_Index_Model_Indexer:
after 1.6.1.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Index/Model/IndexerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method unlockIndexer() of class Mage_Index_Model_Indexer:
after 1.6.1.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Index/Model/IndexerTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method isValidForSend() of class Mage_Newsletter_Model_Template:
since 1.4.0.1',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Newsletter/Model/TemplateTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method addCssIe() of class Mage_Page_Block_Html_Head.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Page/Block/Html/HeadTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method addJsIe() of class Mage_Page_Block_Html_Head.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Page/Block/Html/HeadTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method asArray() of class Mage_Rule_Model_Abstract:
since 1.7.0.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Rule/Model/AbstractTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method asHtml() of class Mage_Rule_Model_Abstract:
since 1.7.0.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Rule/Model/AbstractTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method asString() of class Mage_Rule_Model_Abstract:
since 1.7.0.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Rule/Model/AbstractTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method addWishlistLink() of class Mage_Wishlist_Block_Links:
after 1.4.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Wishlist/Block/LinksTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method initLinkProperties() of class Mage_Wishlist_Block_Links:
after 1.6.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Wishlist/Block/LinksTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method getDataForSave() of class Mage_Wishlist_Model_Item:
since 1.4.0.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Wishlist/Model/ItemTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Call to deprecated method setMethods() of class PHPUnit\\Framework\\MockObject\\MockBuilder:
https://github.com/sebastianbergmann/phpunit/pull/3687',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/OpenMageTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
