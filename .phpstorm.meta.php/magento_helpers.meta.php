<?php
namespace PHPSTORM_META {
    override( \Mage::helper(0),
        map( [
            'adminnotification' => \Mage_AdminNotification_Helper_Data::class,
            'adminnotification/data' => \Mage_AdminNotification_Helper_Data::class,
            'admin/block' => \Mage_Admin_Helper_Block::class,
            'admin' => \Mage_Admin_Helper_Data::class,
            'admin/data' => \Mage_Admin_Helper_Data::class,
            'admin/rules_fallback' => \Mage_Admin_Helper_Rules_Fallback::class,
            'admin/variable' => \Mage_Admin_Helper_Variable::class,
            'adminhtml/addresses' => \Mage_Adminhtml_Helper_Addresses::class,
            'adminhtml/catalog' => \Mage_Adminhtml_Helper_Catalog::class,
            'adminhtml/catalog_product_composite' => \Mage_Adminhtml_Helper_Catalog_Product_Composite::class,
            'adminhtml/catalog_product_edit_action_attribute' => \Mage_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute::class,
            'adminhtml/config' => \Mage_Adminhtml_Helper_Config::class,
            'adminhtml/dashboard_abstract' => \Mage_Adminhtml_Helper_Dashboard_Abstract::class,
            'adminhtml/dashboard_data' => \Mage_Adminhtml_Helper_Dashboard_Data::class,
            'adminhtml/dashboard_order' => \Mage_Adminhtml_Helper_Dashboard_Order::class,
            'adminhtml' => \Mage_Adminhtml_Helper_Data::class,
            'adminhtml/data' => \Mage_Adminhtml_Helper_Data::class,
            'adminhtml/help_mapping' => \Mage_Adminhtml_Helper_Help_Mapping::class,
            'adminhtml/js' => \Mage_Adminhtml_Helper_Js::class,
            'adminhtml/media_js' => \Mage_Adminhtml_Helper_Media_Js::class,
            'adminhtml/rss' => \Mage_Adminhtml_Helper_Rss::class,
            'adminhtml/sales' => \Mage_Adminhtml_Helper_Sales::class,
            'api2' => \Mage_Api2_Helper_Data::class,
            'api2/data' => \Mage_Api2_Helper_Data::class,
            'api' => \Mage_Api_Helper_Data::class,
            'api/data' => \Mage_Api_Helper_Data::class,
            'authorizenet/admin' => \Mage_Authorizenet_Helper_Admin::class,
            'authorizenet' => \Mage_Authorizenet_Helper_Data::class,
            'authorizenet/data' => \Mage_Authorizenet_Helper_Data::class,
            'bundle/catalog_product_configuration' => \Mage_Bundle_Helper_Catalog_Product_Configuration::class,
            'bundle' => \Mage_Bundle_Helper_Data::class,
            'bundle/data' => \Mage_Bundle_Helper_Data::class,
            'captcha' => \Mage_Captcha_Helper_Data::class,
            'captcha/data' => \Mage_Captcha_Helper_Data::class,
            'cataloginventory' => \Mage_CatalogInventory_Helper_Data::class,
            'cataloginventory/data' => \Mage_CatalogInventory_Helper_Data::class,
            'cataloginventory/minsaleqty' => \Mage_CatalogInventory_Helper_Minsaleqty::class,
            'catalogrule' => \Mage_CatalogRule_Helper_Data::class,
            'catalogrule/data' => \Mage_CatalogRule_Helper_Data::class,
            'catalogsearch' => \Mage_CatalogSearch_Helper_Data::class,
            'catalogsearch/data' => \Mage_CatalogSearch_Helper_Data::class,
            'catalog/category' => \Mage_Catalog_Helper_Category::class,
            'catalog/category_flat' => \Mage_Catalog_Helper_Category_Flat::class,
            'catalog/category_url_rewrite' => \Mage_Catalog_Helper_Category_Url_Rewrite::class,
            'catalog' => \Mage_Catalog_Helper_Data::class,
            'catalog/data' => \Mage_Catalog_Helper_Data::class,
            'catalog/flat_abstract' => \Mage_Catalog_Helper_Flat_Abstract::class,
            'catalog/image' => \Mage_Catalog_Helper_Image::class,
            'catalog/map' => \Mage_Catalog_Helper_Map::class,
            'catalog/output' => \Mage_Catalog_Helper_Output::class,
            'catalog/product' => \Mage_Catalog_Helper_Product::class,
            'catalog/product_compare' => \Mage_Catalog_Helper_Product_Compare::class,
            'catalog/product_configuration' => \Mage_Catalog_Helper_Product_Configuration::class,
            'catalog/product_flat' => \Mage_Catalog_Helper_Product_Flat::class,
            'catalog/product_options' => \Mage_Catalog_Helper_Product_Options::class,
            'catalog/product_type_composite' => \Mage_Catalog_Helper_Product_Type_Composite::class,
            'catalog/product_url' => \Mage_Catalog_Helper_Product_Url::class,
            'catalog/product_url_rewrite' => \Mage_Catalog_Helper_Product_Url_Rewrite::class,
            'catalog/product_view' => \Mage_Catalog_Helper_Product_View::class,
            'centinel' => \Mage_Centinel_Helper_Data::class,
            'centinel/data' => \Mage_Centinel_Helper_Data::class,
            'checkout/cart' => \Mage_Checkout_Helper_Cart::class,
            'checkout' => \Mage_Checkout_Helper_Data::class,
            'checkout/data' => \Mage_Checkout_Helper_Data::class,
            'checkout/url' => \Mage_Checkout_Helper_Url::class,
            'cms' => \Mage_Cms_Helper_Data::class,
            'cms/data' => \Mage_Cms_Helper_Data::class,
            'cms/page' => \Mage_Cms_Helper_Page::class,
            'cms/wysiwyg_images' => \Mage_Cms_Helper_Wysiwyg_Images::class,
            'configurableswatches' => \Mage_ConfigurableSwatches_Helper_Data::class,
            'configurableswatches/data' => \Mage_ConfigurableSwatches_Helper_Data::class,
            'configurableswatches/list_price' => \Mage_ConfigurableSwatches_Helper_List_Price::class,
            'configurableswatches/mediafallback' => \Mage_ConfigurableSwatches_Helper_Mediafallback::class,
            'configurableswatches/productimg' => \Mage_ConfigurableSwatches_Helper_Productimg::class,
            'configurableswatches/productlist' => \Mage_ConfigurableSwatches_Helper_Productlist::class,
            'configurableswatches/swatchdimensions' => \Mage_ConfigurableSwatches_Helper_Swatchdimensions::class,
            'core/abstract' => \Mage_Core_Helper_Abstract::class,
            'core/array' => \Mage_Core_Helper_Array::class,
            'core/cookie' => \Mage_Core_Helper_Cookie::class,
            'core' => \Mage_Core_Helper_Data::class,
            'core/data' => \Mage_Core_Helper_Data::class,
            'core/file_storage' => \Mage_Core_Helper_File_Storage::class,
            'core/file_storage_database' => \Mage_Core_Helper_File_Storage_Database::class,
            'core/hint' => \Mage_Core_Helper_Hint::class,
            'core/http' => \Mage_Core_Helper_Http::class,
            'core/js' => \Mage_Core_Helper_Js::class,
            'core/purifier' => \Mage_Core_Helper_Purifier::class,
            'core/security' => \Mage_Core_Helper_Security::class,
            'core/string' => \Mage_Core_Helper_String::class,
            'core/translate' => \Mage_Core_Helper_Translate::class,
            'core/unserializeArray' => \Mage_Core_Helper_UnserializeArray::class,
            'core/url' => \Mage_Core_Helper_Url::class,
            'core/url_rewrite' => \Mage_Core_Helper_Url_Rewrite::class,
            'currencysymbol' => \Mage_CurrencySymbol_Helper_Data::class,
            'currencysymbol/data' => \Mage_CurrencySymbol_Helper_Data::class,
            'customer/address' => \Mage_Customer_Helper_Address::class,
            'customer' => \Mage_Customer_Helper_Data::class,
            'customer/data' => \Mage_Customer_Helper_Data::class,
            'dataflow' => \Mage_Dataflow_Helper_Data::class,
            'dataflow/data' => \Mage_Dataflow_Helper_Data::class,
            'directory' => \Mage_Directory_Helper_Data::class,
            'directory/data' => \Mage_Directory_Helper_Data::class,
            'directory/url' => \Mage_Directory_Helper_Url::class,
            'downloadable/catalog_product_configuration' => \Mage_Downloadable_Helper_Catalog_Product_Configuration::class,
            'downloadable' => \Mage_Downloadable_Helper_Data::class,
            'downloadable/data' => \Mage_Downloadable_Helper_Data::class,
            'downloadable/download' => \Mage_Downloadable_Helper_Download::class,
            'downloadable/file' => \Mage_Downloadable_Helper_File::class,
            'eav' => \Mage_Eav_Helper_Data::class,
            'eav/data' => \Mage_Eav_Helper_Data::class,
            'giftmessage' => \Mage_GiftMessage_Helper_Data::class,
            'giftmessage/data' => \Mage_GiftMessage_Helper_Data::class,
            'giftmessage/message' => \Mage_GiftMessage_Helper_Message::class,
            'giftmessage/url' => \Mage_GiftMessage_Helper_Url::class,
            'googleanalytics' => \Mage_GoogleAnalytics_Helper_Data::class,
            'googleanalytics/data' => \Mage_GoogleAnalytics_Helper_Data::class,
            'importexport' => \Mage_ImportExport_Helper_Data::class,
            'importexport/data' => \Mage_ImportExport_Helper_Data::class,
            'index' => \Mage_Index_Helper_Data::class,
            'index/data' => \Mage_Index_Helper_Data::class,
            'install' => \Mage_Install_Helper_Data::class,
            'install/data' => \Mage_Install_Helper_Data::class,
            'log' => \Mage_Log_Helper_Data::class,
            'log/data' => \Mage_Log_Helper_Data::class,
            'media' => \Mage_Media_Helper_Data::class,
            'media/data' => \Mage_Media_Helper_Data::class,
            'newsletter' => \Mage_Newsletter_Helper_Data::class,
            'newsletter/data' => \Mage_Newsletter_Helper_Data::class,
            'oauth' => \Mage_Oauth_Helper_Data::class,
            'oauth/data' => \Mage_Oauth_Helper_Data::class,
            'page' => \Mage_Page_Helper_Data::class,
            'page/data' => \Mage_Page_Helper_Data::class,
            'page/html' => \Mage_Page_Helper_Html::class,
            'page/layout' => \Mage_Page_Helper_Layout::class,
            'payment' => \Mage_Payment_Helper_Data::class,
            'payment/data' => \Mage_Payment_Helper_Data::class,
            'paypaluk' => \Mage_PaypalUk_Helper_Data::class,
            'paypaluk/data' => \Mage_PaypalUk_Helper_Data::class,
            'paypal/checkout' => \Mage_Paypal_Helper_Checkout::class,
            'paypal' => \Mage_Paypal_Helper_Data::class,
            'paypal/data' => \Mage_Paypal_Helper_Data::class,
            'paypal/hss' => \Mage_Paypal_Helper_Hss::class,
            'persistent' => \Mage_Persistent_Helper_Data::class,
            'persistent/data' => \Mage_Persistent_Helper_Data::class,
            'persistent/session' => \Mage_Persistent_Helper_Session::class,
            'productalert' => \Mage_ProductAlert_Helper_Data::class,
            'productalert/data' => \Mage_ProductAlert_Helper_Data::class,
            'rating' => \Mage_Rating_Helper_Data::class,
            'rating/data' => \Mage_Rating_Helper_Data::class,
            'reports' => \Mage_Reports_Helper_Data::class,
            'reports/data' => \Mage_Reports_Helper_Data::class,
            'review' => \Mage_Review_Helper_Data::class,
            'review/data' => \Mage_Review_Helper_Data::class,
            'rss/catalog' => \Mage_Rss_Helper_Catalog::class,
            'rss' => \Mage_Rss_Helper_Data::class,
            'rss/data' => \Mage_Rss_Helper_Data::class,
            'rss/order' => \Mage_Rss_Helper_Order::class,
            'rule' => \Mage_Rule_Helper_Data::class,
            'rule/data' => \Mage_Rule_Helper_Data::class,
            'salesrule/coupon' => \Mage_SalesRule_Helper_Coupon::class,
            'salesrule' => \Mage_SalesRule_Helper_Data::class,
            'salesrule/data' => \Mage_SalesRule_Helper_Data::class,
            'sales' => \Mage_Sales_Helper_Data::class,
            'sales/data' => \Mage_Sales_Helper_Data::class,
            'sales/guest' => \Mage_Sales_Helper_Guest::class,
            'sales/reorder' => \Mage_Sales_Helper_Reorder::class,
            'sendfriend' => \Mage_Sendfriend_Helper_Data::class,
            'sendfriend/data' => \Mage_Sendfriend_Helper_Data::class,
            'shipping' => \Mage_Shipping_Helper_Data::class,
            'shipping/data' => \Mage_Shipping_Helper_Data::class,
            'sitemap' => \Mage_Sitemap_Helper_Data::class,
            'sitemap/data' => \Mage_Sitemap_Helper_Data::class,
            'tag' => \Mage_Tag_Helper_Data::class,
            'tag/data' => \Mage_Tag_Helper_Data::class,
            'tax' => \Mage_Tax_Helper_Data::class,
            'tax/data' => \Mage_Tax_Helper_Data::class,
            'uploader' => \Mage_Uploader_Helper_Data::class,
            'uploader/data' => \Mage_Uploader_Helper_Data::class,
            'uploader/file' => \Mage_Uploader_Helper_File::class,
            'usa' => \Mage_Usa_Helper_Data::class,
            'usa/data' => \Mage_Usa_Helper_Data::class,
            'weee' => \Mage_Weee_Helper_Data::class,
            'weee/data' => \Mage_Weee_Helper_Data::class,
            'widget' => \Mage_Widget_Helper_Data::class,
            'widget/data' => \Mage_Widget_Helper_Data::class,
            'wishlist' => \Mage_Wishlist_Helper_Data::class,
            'wishlist/data' => \Mage_Wishlist_Helper_Data::class,
        ])
    );
}