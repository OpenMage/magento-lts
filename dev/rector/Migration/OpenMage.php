<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration;

use Mage;
use Mage_Admin_Model_Session;
use Mage_Admin_Model_User;
use Mage_Adminhtml_Helper_Catalog;
use Mage_Adminhtml_Helper_Dashboard_Data;
use Mage_Adminhtml_Helper_Data;
use Mage_Adminhtml_Model_System_Config_Backend_Translate;
use Mage_Api_Helper_Data;
use Mage_CatalogInventory_Block_Stockqty_Abstract;
use Mage_CatalogInventory_Helper_Data;
use Mage_CatalogInventory_Model_Stock_Item;
use Mage_CatalogSearch_Model_Fulltext;
use Mage_CatalogSearch_Model_Layer;
use Mage_CatalogSearch_Model_Query;
use Mage_Catalog_Block_Seo_Sitemap_Tree_Category;
use Mage_Catalog_Helper_Category;
use Mage_Catalog_Helper_Category_Flat;
use Mage_Catalog_Helper_Data;
use Mage_Catalog_Helper_Map;
use Mage_Catalog_Helper_Price;
use Mage_Catalog_Helper_Product;
use Mage_Catalog_Helper_Product_Flat;
use Mage_Catalog_Model_Config;
use Mage_Catalog_Model_Layer_Filter_Price;
use Mage_Checkout_Block_Cart_Sidebar;
use Mage_Checkout_Helper_Cart;
use Mage_Checkout_Helper_Data;
use Mage_Cms_Helper_Data;
use Mage_Cms_Helper_Page;
use Mage_ConfigurableSwatches_Helper_List_Price;
use Mage_Contacts_Helper_Data;
use Mage_Contacts_IndexController;
use Mage_Core_Block_Template;
use Mage_Core_Helper_Cookie;
use Mage_Core_Helper_Data;
use Mage_Core_Model_Cookie;
use Mage_Core_Model_Email_Template;
use Mage_Core_Model_Email_Template_Abstract;
use Mage_Core_Model_File_Storage;
use Mage_Core_Model_Layout_Validator;
use Mage_Core_Model_Locale;
use Mage_Core_Model_Session_Abstract;
use Mage_Core_Model_Store;
use Mage_Cron_Model_Observer;
use Mage_CurrencySymbol_Model_System_Currencysymbol;
use Mage_Customer_Helper_Address;
use Mage_Customer_Helper_Data;
use Mage_Customer_Model_Config_Share;
use Mage_Customer_Model_Customer;
use Mage_Customer_Model_Group;
use Mage_Directory_Helper_Data;
use Mage_Directory_Model_Currency;
use Mage_Directory_Model_Currency_Import_Currencyconverterapi;
use Mage_Directory_Model_Currency_Import_Fixerio;
use Mage_Directory_Model_Observer;
use Mage_Downloadable_Helper_Download;
use Mage_Downloadable_Model_Link;
use Mage_Downloadable_Model_Link_Purchased_Item;
use Mage_Downloadable_Model_Observer;
use Mage_Downloadable_Model_Sample;
use Mage_Eav_Helper_Data;
use Mage_GoogleAnalytics_Helper_Data;
use Mage_ImportExport_Helper_Data;
use Mage_Log_Helper_Data;
use Mage_Log_Model_Cron;
use Mage_Log_Model_Visitor_Online;
use Mage_Newsletter_Model_Subscriber;
use Mage_Oauth_Helper_Data;
use Mage_Page_Helper_Data;
use Mage_Payment_Model_Method_Free;
use Mage_Paypal_Model_Config;
use Mage_Persistent_Helper_Data;
use Mage_ProductAlert_Model_Email;
use Mage_ProductAlert_Model_Observer;
use Mage_Reports_Block_Product_Compared;
use Mage_Reports_Block_Product_Viewed;
use Mage_Reports_Helper_Data;
use Mage_Rss_Helper_Data;
use Mage_SalesRule_Helper_Coupon;
use Mage_Sales_Helper_Reorder;
use Mage_Sales_Model_Order;
use Mage_Sales_Model_Order_Creditmemo;
use Mage_Sales_Model_Order_Invoice;
use Mage_Sales_Model_Order_Pdf_Abstract;
use Mage_Sales_Model_Order_Shipment;
use Mage_Shipping_Model_Config;
use Mage_Shipping_Model_Shipping;
use Mage_Sitemap_Model_Observer;
use Mage_Tax_Model_Config;
use Mage_Weee_Helper_Data;
use Mage_Wishlist_Helper_Data;
use Rector\Arguments\ValueObject\ReplaceArgumentDefaultValue;

final class OpenMage
{
    /**
     * @return ReplaceArgumentDefaultValue[]
     */
    public static function replaceStoreConfigPathsWithConstants(): array
    {
        $map = [];
        // @phpcs:disable Generic.Arrays.DuplicateArrayKey.Found
        $paths = [
            'admin/dashboard/chart_type'                                    => 'Mage_Adminhtml_Helper_Dashboard_Data::XML_PATH_CHART_TYPE',
            'admin/dashboard/enable_charts'                                 => 'Mage_Adminhtml_Helper_Dashboard_Data::XML_PATH_ENABLE_CHARTS',
            'admin/emails/admin_notification_email_template'                => 'Mage_Admin_Model_User::XML_PATH_NOTIFICATION_EMAILS_TEMPLATE',
            'admin/emails/forgot_email_identity'                            => 'Mage_Admin_Model_User::XML_PATH_FORGOT_EMAIL_IDENTITY',
            'admin/emails/forgot_email_template'                            => 'Mage_Admin_Model_User::XML_PATH_FORGOT_EMAIL_TEMPLATE',
            'admin/security/forgot_password_email_times'                    => 'Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_FORGOT_PASSWORD_EMAIL_TIMES',
            'admin/security/forgot_password_flow_secure'                    => 'Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_FORGOT_PASSWORD_FLOW_SECURE',
            'admin/security/forgot_password_ip_times'                       => 'Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_FORGOT_PASSWORD_IP_TIMES',
            'admin/security/min_admin_password_length'                      => 'Mage_Admin_Model_User::XML_PATH_MIN_ADMIN_PASSWORD_LENGTH',
            'admin/security/use_form_key'                                   => 'Mage_Adminhtml_Helper_Data::XML_PATH_ADMINHTML_SECURITY_USE_FORM_KEY',
            'admin/startup/page'                                            => 'Mage_Admin_Model_User::XML_PATH_STARTUP_PAGE',
            'api/config/compliance_wsi'                                     => 'Mage_Api_Helper_Data::XML_PATH_API_WSI',
            'catalog/category/root_id'                                      => 'Mage_Catalog_Helper_Category::XML_PATH_CATEGORY_ROOT_ID',
            'catalog/downloadable/content_disposition'                      => 'Mage_Downloadable_Helper_Download::XML_PATH_CONTENT_DISPOSITION',
            'catalog/downloadable/disable_guest_checkout'                   => 'Mage_Downloadable_Model_Observer::XML_PATH_DISABLE_GUEST_CHECKOUT',
            'catalog/downloadable/downloads_number'                         => 'Mage_Downloadable_Model_Link::XML_PATH_DEFAULT_DOWNLOADS_NUMBER',
            'catalog/downloadable/links_target_new_window'                  => 'Mage_Downloadable_Model_Link::XML_PATH_TARGET_NEW_WINDOW',
            'catalog/downloadable/links_title'                              => 'Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE',
            'catalog/downloadable/order_item_status'                        => 'Mage_Downloadable_Model_Link_Purchased_Item::XML_PATH_ORDER_ITEM_STATUS',
            'catalog/downloadable/samples_title'                            => 'Mage_Downloadable_Model_Sample::XML_PATH_SAMPLES_TITLE',
            'catalog/downloadable/shareable'                                => 'Mage_Downloadable_Model_Link::XML_PATH_CONFIG_IS_SHAREABLE',
            'catalog/frontend/default_sort_by'                              => 'Mage_Catalog_Model_Config::XML_PATH_LIST_DEFAULT_SORT_BY',
            'catalog/frontend/flat_catalog_category'                        => 'Mage_Catalog_Helper_Category_Flat::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY',
            'catalog/frontend/flat_catalog_product'                         => 'Mage_Catalog_Helper_Product_Flat::XML_PATH_USE_PRODUCT_FLAT',
            'catalog/layered_navigation/display_product_count'              => 'Mage_Catalog_Helper_Data::XML_PATH_DISPLAY_PRODUCT_COUNT',
            'catalog/layered_navigation/interval_division_limit'            => 'Mage_Catalog_Model_Layer_Filter_Price::XML_PATH_INTERVAL_DIVISION_LIMIT',
            'catalog/layered_navigation/one_price_interval'                 => 'Mage_Catalog_Model_Layer_Filter_Price::XML_PATH_ONE_PRICE_INTERVAL',
            'catalog/layered_navigation/price_range_calculation'            => 'Mage_Catalog_Model_Layer_Filter_Price::XML_PATH_RANGE_CALCULATION',
            'catalog/layered_navigation/price_range_max_intervals'          => 'Mage_Catalog_Model_Layer_Filter_Price::XML_PATH_RANGE_MAX_INTERVALS',
            'catalog/layered_navigation/price_range_step'                   => 'Mage_Catalog_Model_Layer_Filter_Price::XML_PATH_RANGE_STEP',
            'catalog/price/rounding_precision'                              => 'Mage_Catalog_Helper_Price::XML_PATH_ROUNDING_PRECISION',
            'catalog/price/scope'                                           => 'Mage_Catalog_Helper_Data::XML_PATH_PRICE_SCOPE',
            'catalog/productalert/allow_price'                              => 'Mage_ProductAlert_Model_Observer::XML_PATH_PRICE_ALLOW',
            'catalog/productalert/allow_stock'                              => 'Mage_ProductAlert_Model_Observer::XML_PATH_STOCK_ALLOW',
            'catalog/productalert/email_identity'                           => 'Mage_ProductAlert_Model_Email::XML_PATH_EMAIL_IDENTITY',
            'catalog/productalert/email_price_template'                     => 'Mage_ProductAlert_Model_Email::XML_PATH_EMAIL_PRICE_TEMPLATE',
            'catalog/productalert/email_stock_template'                     => 'Mage_ProductAlert_Model_Email::XML_PATH_EMAIL_STOCK_TEMPLATE',
            'catalog/productalert_cron/error_email'                         => 'Mage_ProductAlert_Model_Observer::XML_PATH_ERROR_RECIPIENT',
            'catalog/productalert_cron/error_email_identity'                => 'Mage_ProductAlert_Model_Observer::XML_PATH_ERROR_IDENTITY',
            'catalog/productalert_cron/error_email_template'                => 'Mage_ProductAlert_Model_Observer::XML_PATH_ERROR_TEMPLATE',
            'catalog/recently_products/compared_count'                      => 'Mage_Reports_Block_Product_Compared::XML_PATH_RECENTLY_COMPARED_COUNT',
            'catalog/recently_products/viewed_count'                        => 'Mage_Reports_Block_Product_Viewed::XML_PATH_RECENTLY_VIEWED_COUNT',
            'catalog/search/max_query_length'                               => 'Mage_CatalogSearch_Model_Query::XML_PATH_MAX_QUERY_LENGTH',
            'catalog/search/max_query_words'                                => 'Mage_CatalogSearch_Model_Query::XML_PATH_MAX_QUERY_WORDS',
            'catalog/search/min_query_length'                               => 'Mage_CatalogSearch_Model_Query::XML_PATH_MIN_QUERY_LENGTH',
            'catalog/search/search_separator'                               => 'Mage_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_SEPARATOR',
            'catalog/search/search_type'                                    => 'Mage_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE',
            'catalog/search/show_autocomplete_results_count'                => 'Mage_CatalogSearch_Model_Query::XML_PATH_AJAX_SUGGESTION_COUNT',
            'catalog/search/use_layered_navigation_count'                   => 'Mage_CatalogSearch_Model_Layer::XML_PATH_DISPLAY_LAYER_COUNT',
            'catalog/seo/category_canonical_tag'                            => 'Mage_Catalog_Helper_Category::XML_PATH_USE_CATEGORY_CANONICAL_TAG',
            'catalog/seo/category_url_suffix'                               => 'Mage_Catalog_Helper_Category::XML_PATH_CATEGORY_URL_SUFFIX',
            'catalog/seo/product_canonical_tag'                             => 'Mage_Catalog_Helper_Product::XML_PATH_USE_PRODUCT_CANONICAL_TAG',
            'catalog/seo/product_url_suffix'                                => 'Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_SUFFIX',
            'catalog/seo/product_use_categories'                            => 'Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY',
            'catalog/seo/save_rewrites_history'                             => 'Mage_Catalog_Helper_Data::XML_PATH_SEO_SAVE_HISTORY',
            'catalog/sitemap/lines_perpage'                                 => 'Mage_Catalog_Block_Seo_Sitemap_Tree_Category::XML_PATH_LINES_PER_PAGE',
            'catalog/sitemap/tree_mode'                                     => 'Mage_Catalog_Helper_Map::XML_PATH_USE_TREE_MODE',
            'cataloginventory/item_options/auto_return'                     => 'Mage_CatalogInventory_Helper_Data::XML_PATH_ITEM_AUTO_RETURN',
            'cataloginventory/item_options/backorders'                      => 'Mage_CatalogInventory_Model_Stock_Item::XML_PATH_BACKORDERS',
            'cataloginventory/item_options/enable_qty_increments'           => 'Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ENABLE_QTY_INCREMENTS',
            'cataloginventory/item_options/manage_stock'                    => 'Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK',
            'cataloginventory/item_options/max_sale_qty'                    => 'Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MAX_SALE_QTY',
            'cataloginventory/item_options/min_qty'                         => 'Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_QTY',
            'cataloginventory/item_options/min_sale_qty'                    => 'Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_SALE_QTY',
            'cataloginventory/item_options/notify_stock_qty'                => 'Mage_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY',
            'cataloginventory/item_options/qty_increments'                  => 'Mage_CatalogInventory_Model_Stock_Item::XML_PATH_QTY_INCREMENTS',
            'cataloginventory/options/can_back_in_stock'                    => 'Mage_CatalogInventory_Model_Stock_Item::XML_PATH_CAN_BACK_IN_STOCK',
            'cataloginventory/options/can_subtract'                         => 'Mage_CatalogInventory_Model_Stock_Item::XML_PATH_CAN_SUBTRACT',
            'cataloginventory/options/display_product_stock_status'         => 'Mage_CatalogInventory_Helper_Data::XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS',
            'cataloginventory/options/show_out_of_stock'                    => 'Mage_CatalogInventory_Helper_Data::XML_PATH_SHOW_OUT_OF_STOCK',
            'cataloginventory/options/stock_threshold_qty'                  => 'Mage_CatalogInventory_Block_Stockqty_Abstract::XML_PATH_STOCK_THRESHOLD_QTY',
            'checkout/cart/minicart_visible_items'                          => 'Mage_Checkout_Block_Cart_Sidebar::XML_PATH_CHECKOUT_MINICART_VISIBLE_ITEMS_COUNT',
            'checkout/cart/redirect_to_cart'                                => 'Mage_Checkout_Helper_Cart::XML_PATH_REDIRECT_TO_CART',
            'checkout/options/customer_must_be_logged'                      => 'Mage_Checkout_Helper_Data::XML_PATH_CUSTOMER_MUST_BE_LOGGED',
            'checkout/options/guest_checkout'                               => 'Mage_Checkout_Helper_Data::XML_PATH_GUEST_CHECKOUT',
            'checkout/sidebar/count'                                        => 'Mage_Checkout_Block_Cart_Sidebar::XML_PATH_CHECKOUT_SIDEBAR_COUNT',
            'configswatches/general/product_list_price_change'              => 'Mage_ConfigurableSwatches_Helper_List_Price::XML_PATH_SWATCH_PRICE',
            'contacts/auto_reply/email_template'                            => 'Mage_Contacts_IndexController::XML_PATH_AUTO_REPLY_EMAIL_TEMPLATE',
            'contacts/auto_reply/enabled'                                   => 'Mage_Contacts_IndexController::XML_PATH_AUTO_REPLY_ENABLED',
            'contacts/contacts/enabled'                                     => 'Mage_Contacts_Helper_Data::XML_PATH_ENABLED',
            'contacts/email/email_template'                                 => 'Mage_Contacts_IndexController::XML_PATH_EMAIL_TEMPLATE',
            'contacts/email/recipient_email'                                => 'Mage_Contacts_IndexController::XML_PATH_EMAIL_RECIPIENT',
            'contacts/email/sender_email_identity'                          => 'Mage_Contacts_IndexController::XML_PATH_EMAIL_SENDER',
            'currency/currencyconverterapi/api_key'                         => 'Mage_Directory_Model_Currency_Import_Currencyconverterapi::XML_PATH_CURRENCY_CONVERTER_API_KEY',
            'currency/currencyconverterapi/timeout'                         => 'Mage_Directory_Model_Currency_Import_Currencyconverterapi::XML_PATH_CURRENCY_CONVERTER_TIMEOUT',
            'currency/fixerio/api_key'                                      => 'Mage_Directory_Model_Currency_Import_Fixerio::XML_PATH_FIXERIO_API_KEY',
            'currency/fixerio/timeout'                                      => 'Mage_Directory_Model_Currency_Import_Fixerio::XML_PATH_FIXERIO_TIMEOUT',
            'currency/import/error_email'                                   => 'Mage_Directory_Model_Observer::XML_PATH_ERROR_RECIPIENT',
            'currency/import/error_email_identity'                          => 'Mage_Directory_Model_Observer::XML_PATH_ERROR_IDENTITY',
            'currency/import/error_email_template'                          => 'Mage_Directory_Model_Observer::XML_PATH_ERROR_TEMPLATE',
            'currency/options/allow'                                        => 'Mage_Directory_Model_Currency::XML_PATH_CURRENCY_ALLOW',
            'currency/options/base'                                         => 'Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE',
            'currency/options/customsymbol'                                 => 'Mage_CurrencySymbol_Model_System_Currencysymbol::XML_PATH_CUSTOM_CURRENCY_SYMBOL',
            'currency/options/default'                                      => 'Mage_Directory_Model_Currency::XML_PATH_CURRENCY_DEFAULT',
            'customer/account_share/scope'                                  => 'Mage_Customer_Model_Config_Share::XML_PATH_CUSTOMER_ACCOUNT_SHARE',
            'customer/changed_account/password_or_email_identity'           => 'Mage_Customer_Model_Customer::XML_PATH_CHANGED_PASSWORD_OR_EMAIL_IDENTITY',
            'customer/changed_account/password_or_email_template'           => 'Mage_Customer_Model_Customer::XML_PATH_CHANGED_PASSWORD_OR_EMAIL_TEMPLATE',
            'customer/create_account/auto_group_assign'                     => 'Mage_Customer_Helper_Address::XML_PATH_VAT_VALIDATION_ENABLED',
            'customer/create_account/confirm'                               => 'Mage_Customer_Model_Customer::XML_PATH_IS_CONFIRM',
            'customer/create_account/default_group'                         => 'Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID',
            'customer/create_account/email_confirmation_template'           => 'Mage_Customer_Model_Customer::XML_PATH_CONFIRM_EMAIL_TEMPLATE',
            'customer/create_account/email_confirmed_template'              => 'Mage_Customer_Model_Customer::XML_PATH_CONFIRMED_EMAIL_TEMPLATE',
            'customer/create_account/email_domain'                          => 'Mage_Customer_Model_Customer::XML_PATH_DEFAULT_EMAIL_DOMAIN',
            'customer/create_account/email_identity'                        => 'Mage_Customer_Model_Customer::XML_PATH_REGISTER_EMAIL_IDENTITY',
            'customer/create_account/email_template'                        => 'Mage_Customer_Model_Customer::XML_PATH_REGISTER_EMAIL_TEMPLATE',
            'customer/create_account/generate_human_friendly_id'            => 'Mage_Customer_Model_Customer::XML_PATH_GENERATE_HUMAN_FRIENDLY_ID',
            'customer/create_account/tax_calculation_address_type'          => 'Mage_Customer_Helper_Address::XML_PATH_VIV_TAX_CALCULATION_ADDRESS_TYPE',
            'customer/create_account/vat_frontend_visibility'               => 'Mage_Customer_Helper_Address::XML_PATH_VAT_FRONTEND_VISIBILITY',
            'customer/create_account/viv_disable_auto_group_assign_default' => 'Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_VIV_GROUP_AUTO_ASSIGN',
            'customer/create_account/viv_domestic_group'                    => 'Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_VIV_DOMESTIC_GROUP',
            'customer/create_account/viv_error_group'                       => 'Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_VIV_ERROR_GROUP',
            'customer/create_account/viv_intra_union_group'                 => 'Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_VIV_INTRA_UNION_GROUP',
            'customer/create_account/viv_invalid_group'                     => 'Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_VIV_INVALID_GROUP',
            'customer/create_account/viv_on_each_transaction'               => 'Mage_Customer_Helper_Address::XML_PATH_VIV_ON_EACH_TRANSACTION',
            'customer/online_customers/online_minutes_interval'             => 'Mage_Log_Model_Visitor_Online::XML_PATH_ONLINE_INTERVAL',
            'customer/password/forgot_email_identity'                       => 'Mage_Customer_Model_Customer::XML_PATH_FORGOT_EMAIL_IDENTITY',
            'customer/password/forgot_email_template'                       => 'Mage_Customer_Model_Customer::XML_PATH_FORGOT_EMAIL_TEMPLATE',
            'customer/password/min_password_length'                         => 'Mage_Customer_Model_Customer::XML_PATH_MIN_PASSWORD_LENGTH',
            'customer/password/remind_email_template'                       => 'Mage_Customer_Model_Customer::XML_PATH_REMIND_EMAIL_TEMPLATE',
            'customer/password/require_admin_user_to_change_user_password'  => 'Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_REQUIRE_ADMIN_USER_TO_CHANGE_USER_PASSWORD',
            'customer/password_link/account_new_email_template'             => 'Mage_Customer_Model_Customer::XML_PATH_PASSWORD_LINK_ACCOUNT_NEW_EMAIL_TEMPLATE',
            'customer/password_link/email_identity'                         => 'Mage_Customer_Model_Customer::XML_PATH_PASSWORD_LINK_EMAIL_IDENTITY',
            'customer/password_link/email_template'                         => 'Mage_Customer_Model_Customer::XML_PATH_PASSWORD_LINK_EMAIL_TEMPLATE',
            'customer/startup/redirect_dashboard'                           => 'Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD',
            'design/email/css_non_inline'                                   => 'Mage_Core_Model_Email_Template_Abstract::XML_PATH_CSS_NON_INLINE_FILES',
            'design/email/logo'                                             => 'Mage_Core_Model_Email_Template_Abstract::XML_PATH_DESIGN_EMAIL_LOGO',
            'design/email/logo_alt'                                         => 'Mage_Core_Model_Email_Template_Abstract::XML_PATH_DESIGN_EMAIL_LOGO_ALT',
            'design/email/logo_height'                                      => 'Mage_Core_Model_Email_Template_Abstract::XML_PATH_DESIGN_EMAIL_LOGO_HEIGHT',
            'design/email/logo_width'                                       => 'Mage_Core_Model_Email_Template_Abstract::XML_PATH_DESIGN_EMAIL_LOGO_WIDTH',
            'design/header/logo_src'                                        => 'Mage_Page_Helper_Data::XML_PATH_LOGO_SRC',
            'design/header/logo_src_small'                                  => 'Mage_Page_Helper_Data::XML_PATH_LOGO_SRC_SMALL',
            'design/header/logo_src_small_same_as_main'                     => 'Mage_Page_Helper_Data::XML_PATH_LOGO_SRC_SMALL_SAME_AS_MAIN',
            'dev/debug/template_hints'                                      => 'Mage_Core_Block_Template::XML_PATH_DEBUG_TEMPLATE_HINTS',
            'dev/debug/template_hints_admin'                                => 'Mage_Core_Block_Template::XML_PATH_DEBUG_TEMPLATE_HINTS_ADMIN',
            'dev/debug/template_hints_blocks'                               => 'Mage_Core_Block_Template::XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS',
            'dev/debug/template_hints_blocks_admin'                         => 'Mage_Core_Block_Template::XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS_ADMIN',
            'dev/log/active'                                                => 'Mage_Core_Helper_Data::XML_PATH_DEV_LOG_ENABLED',
            'dev/log/allowedFileExtensions'                                 => 'Mage_Core_Helper_Data::XML_PATH_DEV_LOG_ALLOWED_EXTENSIONS',
            'dev/log/exception_file'                                        => 'Mage_Core_Helper_Data::XML_PATH_DEV_LOG_EXCEPTION_FILE',
            'dev/log/file'                                                  => 'Mage_Core_Helper_Data::XML_PATH_DEV_LOG_FILE',
            'dev/log/max_level'                                             => 'Mage_Core_Helper_Data::XML_PATH_DEV_LOG_MAX_LEVEL',
            'dev/restrict/allow_ips'                                        => 'Mage_Core_Helper_Data::XML_PATH_DEV_ALLOW_IPS',
            'dev/template/allow_symlink'                                    => 'Mage_Core_Block_Template::XML_PATH_TEMPLATE_ALLOW_SYMLINK',
            'dev/translate_inline/invalid_caches'                           => 'Mage_Adminhtml_Model_System_Config_Backend_Translate::XML_PATH_INVALID_CACHES',
            'general/additional_notification_emails/admin_user_create'      => 'Mage_Admin_Model_User::XML_PATH_ADDITIONAL_EMAILS',
            'general/country/default'                                       => 'Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY',
            'general/country/eu_countries'                                  => 'Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST',
            'general/file/bunch_size'                                       => 'Mage_ImportExport_Helper_Data::XML_PATH_BUNCH_SIZE',
            'general/file/importexport_local_valid_paths'                   => 'Mage_ImportExport_Helper_Data::XML_PATH_EXPORT_LOCAL_VALID_PATH',
            'general/file/protected_extensions'                             => 'Mage_Core_Helper_Data::XML_PATH_PROTECTED_FILE_EXTENSIONS',
            'general/file/public_files_valid_paths'                         => 'Mage_Core_Helper_Data::XML_PATH_PUBLIC_FILES_VALID_PATHS',
            'general/file/sitemap_generate_valid_paths'                     => 'Mage_Adminhtml_Helper_Catalog::XML_PATH_SITEMAP_VALID_PATHS',
            'general/locale/code'                                           => 'Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE',
            'general/locale/timezone'                                       => 'Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE',
            'general/region/display_all'                                    => 'Mage_Directory_Helper_Data::XML_PATH_DISPLAY_ALL_STATES',
            'general/region/state_required'                                 => 'Mage_Directory_Helper_Data::XML_PATH_STATES_REQUIRED',
            'general/store_information/hours'                               => 'Mage_Core_Model_Store::XML_PATH_STORE_STORE_HOURS',
            'general/store_information/merchant_country'                    => 'Mage_Core_Helper_Data::XML_PATH_MERCHANT_COUNTRY_CODE',
            'general/store_information/merchant_vat_number'                 => 'Mage_Core_Helper_Data::XML_PATH_MERCHANT_VAT_NUMBER',
            'general/store_information/name'                                => 'Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME',
            'general/store_information/phone'                               => 'Mage_Core_Model_Store::XML_PATH_STORE_STORE_PHONE',
            'general/validator_data/input_types'                            => 'Mage_Eav_Helper_Data::XML_PATH_VALIDATOR_DATA_INPUT_TYPES',
            'google/analytics/account'                                      => 'Mage_GoogleAnalytics_Helper_Data::XML_PATH_ACCOUNT',
            'google/analytics/active'                                       => 'Mage_GoogleAnalytics_Helper_Data::XML_PATH_ACTIVE',
            'google/analytics/debug'                                        => 'Mage_GoogleAnalytics_Helper_Data::XML_PATH_DEBUG',
            'google/analytics/type'                                         => 'Mage_GoogleAnalytics_Helper_Data::XML_PATH_TYPE',
            'google/analytics/user_id'                                      => 'Mage_GoogleAnalytics_Helper_Data::XML_PATH_USERID',
            'google/gtm/active'                                             => 'Mage_GoogleAnalytics_Helper_Data::XML_PATH_GTM_ACTIVE',
            'google/gtm/container_id'                                       => 'Mage_GoogleAnalytics_Helper_Data::XML_PATH_GTM_CONTAINER_ID',
            'log/visitor/online_update_frequency'                           => 'Mage_Log_Model_Visitor_Online::XML_PATH_UPDATE_FREQUENCY',
            'newsletter/subscription/allow_guest_subscribe'                 => 'Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG',
            'newsletter/subscription/confirm'                               => 'Mage_Newsletter_Model_Subscriber::XML_PATH_CONFIRMATION_FLAG',
            'newsletter/subscription/confirm_email_identity'                => 'Mage_Newsletter_Model_Subscriber::XML_PATH_CONFIRM_EMAIL_IDENTITY',
            'newsletter/subscription/confirm_email_template'                => 'Mage_Newsletter_Model_Subscriber::XML_PATH_CONFIRM_EMAIL_TEMPLATE',
            'newsletter/subscription/success_email_identity'                => 'Mage_Newsletter_Model_Subscriber::XML_PATH_SUCCESS_EMAIL_IDENTITY',
            'newsletter/subscription/success_email_template'                => 'Mage_Newsletter_Model_Subscriber::XML_PATH_SUCCESS_EMAIL_TEMPLATE',
            'newsletter/subscription/un_email_identity'                     => 'Mage_Newsletter_Model_Subscriber::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY',
            'newsletter/subscription/un_email_template'                     => 'Mage_Newsletter_Model_Subscriber::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE',
            'oauth/cleanup/cleanup_probability'                             => 'Mage_Oauth_Helper_Data::XML_PATH_CLEANUP_PROBABILITY',
            'oauth/cleanup/expiration_period'                               => 'Mage_Oauth_Helper_Data::XML_PATH_CLEANUP_EXPIRATION_PERIOD',
            'oauth/email/identity'                                          => 'Mage_Oauth_Helper_Data::XML_PATH_EMAIL_IDENTITY',
            'oauth/email/template'                                          => 'Mage_Oauth_Helper_Data::XML_PATH_EMAIL_TEMPLATE',
            'payment/free/active'                                           => 'Mage_Payment_Model_Method_Free::XML_PATH_PAYMENT_FREE_ACTIVE',
            'payment/free/order_status'                                     => 'Mage_Payment_Model_Method_Free::XML_PATH_PAYMENT_FREE_ORDER_STATUS',
            'payment/free/payment_action'                                   => 'Mage_Payment_Model_Method_Free::XML_PATH_PAYMENT_FREE_PAYMENT_ACTION',
            'payment/paypal_express/skip_order_review_step'                 => 'Mage_Paypal_Model_Config::XML_PATH_PAYPAL_EXPRESS_SKIP_ORDER_REVIEW_STEP_FLAG',
            'persistent/options/enabled'                                    => 'Mage_Persistent_Helper_Data::XML_PATH_ENABLED',
            'persistent/options/lifetime'                                   => 'Mage_Persistent_Helper_Data::XML_PATH_LIFE_TIME',
            'persistent/options/logout_clear'                               => 'Mage_Persistent_Helper_Data::XML_PATH_LOGOUT_CLEAR',
            'persistent/options/remember_default'                           => 'Mage_Persistent_Helper_Data::XML_PATH_REMEMBER_ME_DEFAULT',
            'persistent/options/remember_enabled'                           => 'Mage_Persistent_Helper_Data::XML_PATH_REMEMBER_ME_ENABLED',
            'persistent/options/shopping_cart'                              => 'Mage_Persistent_Helper_Data::XML_PATH_PERSIST_SHOPPING_CART',
            'promo/auto_generated_coupon_codes/dash'                        => 'Mage_SalesRule_Helper_Coupon::XML_PATH_SALES_RULE_COUPON_DASH_INTERVAL',
            'promo/auto_generated_coupon_codes/format'                      => 'Mage_SalesRule_Helper_Coupon::XML_PATH_SALES_RULE_COUPON_FORMAT',
            'promo/auto_generated_coupon_codes/length'                      => 'Mage_SalesRule_Helper_Coupon::XML_PATH_SALES_RULE_COUPON_LENGTH',
            'promo/auto_generated_coupon_codes/prefix'                      => 'Mage_SalesRule_Helper_Coupon::XML_PATH_SALES_RULE_COUPON_PREFIX',
            'promo/auto_generated_coupon_codes/suffix'                      => 'Mage_SalesRule_Helper_Coupon::XML_PATH_SALES_RULE_COUPON_SUFFIX',
            'reports/general/enabled'                                       => 'Mage_Reports_Helper_Data::XML_PATH_REPORTS_ENABLED',
            'rss/admin_catalog/notifystock'                                 => 'Mage_Rss_Helper_Data::XML_PATH_RSS_ADMIN_CATALOG_NOTIFYSTOCK',
            'rss/admin_catalog/review'                                      => 'Mage_Rss_Helper_Data::XML_PATH_RSS_ADMIN_CATALOG_REVIEW',
            'rss/admin_order/new'                                           => 'Mage_Rss_Helper_Data::XML_PATH_RSS_ADMIN_ORDER_NEW',
            'rss/admin_order/new_period'                                    => 'Mage_Rss_Helper_Data::XML_PATH_RSS_ADMIN_ORDER_NEW_PERIOD',
            'rss/config/active'                                             => 'Mage_Rss_Helper_Data::XML_PATH_RSS_ACTIVE',
            'sales/msrp/apply_for_all'                                      => 'Mage_Catalog_Helper_Data::XML_PATH_MSRP_APPLY_TO_ALL',
            'sales/msrp/display_price_type'                                 => 'Mage_Catalog_Helper_Data::XML_PATH_MSRP_DISPLAY_ACTUAL_PRICE_TYPE',
            'sales/msrp/enabled'                                            => 'Mage_Catalog_Helper_Data::XML_PATH_MSRP_ENABLED',
            'sales/msrp/explanation_message'                                => 'Mage_Catalog_Helper_Data::XML_PATH_MSRP_EXPLANATION_MESSAGE',
            'sales/msrp/explanation_message_whats_this'                     => 'Mage_Catalog_Helper_Data::XML_PATH_MSRP_EXPLANATION_MESSAGE_WHATS_THIS',
            'sales/reorder/allow'                                           => 'Mage_Sales_Helper_Reorder::XML_PATH_SALES_REORDER_ALLOW',
            'sales_email/creditmemo/copy_method'                            => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_COPY_METHOD',
            'sales_email/creditmemo/copy_to'                                => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_COPY_TO',
            'sales_email/creditmemo/enabled'                                => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_ENABLED',
            'sales_email/creditmemo/guest_template'                         => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_GUEST_TEMPLATE',
            'sales_email/creditmemo/identity'                               => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_IDENTITY',
            'sales_email/creditmemo/template'                               => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_TEMPLATE',
            'sales_email/creditmemo_comment/copy_method'                    => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_COPY_METHOD',
            'sales_email/creditmemo_comment/copy_to'                        => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_COPY_TO',
            'sales_email/creditmemo_comment/enabled'                        => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_ENABLED',
            'sales_email/creditmemo_comment/guest_template'                 => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE',
            'sales_email/creditmemo_comment/identity'                       => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_IDENTITY',
            'sales_email/creditmemo_comment/template'                       => 'Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_TEMPLATE',
            'sales_email/invoice/copy_method'                               => 'Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_METHOD',
            'sales_email/invoice/copy_to'                                   => 'Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_COPY_TO',
            'sales_email/invoice/enabled'                                   => 'Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_ENABLED',
            'sales_email/invoice/guest_template'                            => 'Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_GUEST_TEMPLATE',
            'sales_email/invoice/identity'                                  => 'Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_IDENTITY',
            'sales_email/invoice/template'                                  => 'Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_TEMPLATE',
            'sales_email/invoice_comment/copy_method'                       => 'Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_COPY_METHOD',
            'sales_email/invoice_comment/copy_to'                           => 'Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_COPY_TO',
            'sales_email/invoice_comment/enabled'                           => 'Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_ENABLED',
            'sales_email/invoice_comment/guest_template'                    => 'Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE',
            'sales_email/invoice_comment/identity'                          => 'Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_IDENTITY',
            'sales_email/invoice_comment/template'                          => 'Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_TEMPLATE',
            'sales_email/order/copy_method'                                 => 'Mage_Sales_Model_Order::XML_PATH_EMAIL_COPY_METHOD',
            'sales_email/order/copy_to'                                     => 'Mage_Sales_Model_Order::XML_PATH_EMAIL_COPY_TO',
            'sales_email/order/enabled'                                     => 'Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED',
            'sales_email/order/guest_template'                              => 'Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE',
            'sales_email/order/identity'                                    => 'Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY',
            'sales_email/order/template'                                    => 'Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE',
            'sales_email/order_comment/copy_method'                         => 'Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_COPY_METHOD',
            'sales_email/order_comment/copy_to'                             => 'Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_COPY_TO',
            'sales_email/order_comment/enabled'                             => 'Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_ENABLED',
            'sales_email/order_comment/guest_template'                      => 'Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE',
            'sales_email/order_comment/identity'                            => 'Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_IDENTITY',
            'sales_email/order_comment/template'                            => 'Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_TEMPLATE',
            'sales_email/shipment/copy_method'                              => 'Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_COPY_METHOD',
            'sales_email/shipment/copy_to'                                  => 'Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_COPY_TO',
            'sales_email/shipment/enabled'                                  => 'Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_ENABLED',
            'sales_email/shipment/guest_template'                           => 'Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_GUEST_TEMPLATE',
            'sales_email/shipment/identity'                                 => 'Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_IDENTITY',
            'sales_email/shipment/template'                                 => 'Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_TEMPLATE',
            'sales_email/shipment_comment/copy_method'                      => 'Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_COPY_METHOD',
            'sales_email/shipment_comment/copy_to'                          => 'Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_COPY_TO',
            'sales_email/shipment_comment/enabled'                          => 'Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_ENABLED',
            'sales_email/shipment_comment/guest_template'                   => 'Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE',
            'sales_email/shipment_comment/identity'                         => 'Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_IDENTITY',
            'sales_email/shipment_comment/template'                         => 'Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_TEMPLATE',
            'sales_pdf/creditmemo/put_order_id'                             => 'Mage_Sales_Model_Order_Pdf_Abstract::XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID',
            'sales_pdf/invoice/put_order_id'                                => 'Mage_Sales_Model_Order_Pdf_Abstract::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID',
            'sales_pdf/shipment/put_order_id'                               => 'Mage_Sales_Model_Order_Pdf_Abstract::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID',
            'shipping/origin/city'                                          => 'Mage_Shipping_Model_Config::XML_PATH_ORIGIN_CITY',
            'shipping/origin/country_id'                                    => 'Mage_Shipping_Model_Config::XML_PATH_ORIGIN_COUNTRY_ID',
            'shipping/origin/postcode'                                      => 'Mage_Shipping_Model_Config::XML_PATH_ORIGIN_POSTCODE',
            'shipping/origin/region_id'                                     => 'Mage_Shipping_Model_Config::XML_PATH_ORIGIN_REGION_ID',
            'shipping/origin/street_line1'                                  => 'Mage_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS1',
            'shipping/origin/street_line2'                                  => 'Mage_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS2',
            'sitemap/generate/enabled'                                      => 'Mage_Sitemap_Model_Observer::XML_PATH_GENERATION_ENABLED',
            'sitemap/generate/error_email'                                  => 'Mage_Sitemap_Model_Observer::XML_PATH_ERROR_RECIPIENT',
            'sitemap/generate/error_email_identity'                         => 'Mage_Sitemap_Model_Observer::XML_PATH_ERROR_IDENTITY',
            'sitemap/generate/error_email_template'                         => 'Mage_Sitemap_Model_Observer::XML_PATH_ERROR_TEMPLATE',
            'system/cron/history_cleanup_every'                             => 'Mage_Cron_Model_Observer::XML_PATH_HISTORY_CLEANUP_EVERY',
            'system/cron/history_failure_lifetime'                          => 'Mage_Cron_Model_Observer::XML_PATH_HISTORY_FAILURE',
            'system/cron/history_success_lifetime'                          => 'Mage_Cron_Model_Observer::XML_PATH_HISTORY_SUCCESS',
            'system/cron/schedule_ahead_for'                                => 'Mage_Cron_Model_Observer::XML_PATH_SCHEDULE_AHEAD_FOR',
            'system/cron/schedule_generate_every'                           => 'Mage_Cron_Model_Observer::XML_PATH_SCHEDULE_GENERATE_EVERY',
            'system/cron/schedule_lifetime'                                 => 'Mage_Cron_Model_Observer::XML_PATH_SCHEDULE_LIFETIME',
            'system/currency/installed'                                     => 'Mage_Core_Model_Locale::XML_PATH_ALLOW_CURRENCIES_INSTALLED',
            'system/import_csv/configurable_page_size'                      => 'Mage_ImportExport_Helper_Data::XML_PATH_IMPORT_CONFIGURABLE_PAGE_SIZE',
            'system/log/enable_log'                                         => 'Mage_Log_Helper_Data::XML_PATH_LOG_ENABLED',
            'system/log/enabled'                                            => 'Mage_Log_Model_Cron::XML_PATH_LOG_CLEAN_ENABLED',
            'system/log/error_email'                                        => 'Mage_Log_Model_Cron::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT',
            'system/log/error_email_identity'                               => 'Mage_Log_Model_Cron::XML_PATH_EMAIL_LOG_CLEAN_IDENTITY',
            'system/log/error_email_template'                               => 'Mage_Log_Model_Cron::XML_PATH_EMAIL_LOG_CLEAN_TEMPLATE',
            'system/media_storage_configuration/configuration_update_time'  => 'Mage_Core_Model_File_Storage::XML_PATH_MEDIA_UPDATE_TIME',
            'system/smtp/return_path_email'                                 => 'Mage_Core_Model_Email_Template::XML_PATH_SENDING_RETURN_PATH_EMAIL',
            'system/smtp/set_return_path'                                   => 'Mage_Core_Model_Email_Template::XML_PATH_SENDING_SET_RETURN_PATH',
            'tax/calculation/algorithm'                                     => 'Mage_Tax_Model_Config::XML_PATH_ALGORITHM',
            'tax/cart_display/discount'                                     => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_CART_DISCOUNT',
            'tax/cart_display/full_summary'                                 => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_CART_FULL_SUMMARY',
            'tax/cart_display/grandtotal'                                   => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_CART_GRANDTOTAL',
            'tax/cart_display/price'                                        => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_CART_PRICE',
            'tax/cart_display/shipping'                                     => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_CART_SHIPPING',
            'tax/cart_display/subtotal'                                     => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_CART_SUBTOTAL',
            'tax/cart_display/zero_tax'                                     => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_CART_ZERO_TAX',
            'tax/ignore_notification/discount'                              => 'Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_DISCOUNT',
            'tax/ignore_notification/fpt_configuration'                     => 'Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_FPT_CONFIGURATION',
            'tax/ignore_notification/price_display'                         => 'Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_PRICE_DISPLAY',
            'tax/notification/url'                                          => 'Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_URL',
            'tax/sales_display/discount'                                    => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_SALES_DISCOUNT',
            'tax/sales_display/full_summary'                                => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_SALES_FULL_SUMMARY',
            'tax/sales_display/grandtotal'                                  => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_SALES_GRANDTOTAL',
            'tax/sales_display/price'                                       => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_SALES_PRICE',
            'tax/sales_display/shipping'                                    => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_SALES_SHIPPING',
            'tax/sales_display/subtotal'                                    => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_SALES_SUBTOTAL',
            'tax/sales_display/zero_tax'                                    => 'Mage_Tax_Model_Config::XML_PATH_DISPLAY_SALES_ZERO_TAX',
            'tax/weee/enable'                                               => 'Mage_Weee_Helper_Data::XML_PATH_FPT_ENABLED',
            'trans_email/ident_support/email'                               => 'Mage_Customer_Helper_Data::XML_PATH_SUPPORT_EMAIL',
            'validators/custom_layout/disallowed_block'                     => 'Mage_Core_Model_Layout_Validator::XML_PATH_LAYOUT_DISALLOWED_BLOCKS',
            'web/cookie/cookie_domain'                                      => 'Mage_Core_Model_Cookie::XML_PATH_COOKIE_DOMAIN',
            'web/cookie/cookie_httponly'                                    => 'Mage_Core_Model_Cookie::XML_PATH_COOKIE_HTTPONLY',
            'web/cookie/cookie_lifetime'                                    => 'Mage_Core_Model_Cookie::XML_PATH_COOKIE_LIFETIME',
            'web/cookie/cookie_path'                                        => 'Mage_Core_Model_Cookie::XML_PATH_COOKIE_PATH',
            'web/cookie/cookie_restriction'                                 => 'Mage_Core_Helper_Cookie::XML_PATH_COOKIE_RESTRICTION',
            'web/cookie/cookie_restriction_lifetime'                        => 'Mage_Core_Helper_Cookie::XML_PATH_COOKIE_RESTRICTION_LIFETIME',
            'web/cookie/cookie_samesite'                                    => 'Mage_Core_Model_Cookie::XML_PATH_COOKIE_SAMESITE',
            'web/default/cms_home_page'                                     => 'Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE',
            'web/default/cms_no_cookies'                                    => 'Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE',
            'web/default/cms_no_route'                                      => 'Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE',
            'web/secure/base_js_url'                                        => 'Mage_Core_Model_Store::XML_PATH_SECURE_BASE_JS_URL',
            'web/secure/base_link_url'                                      => 'Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL',
            'web/secure/base_media_url'                                     => 'Mage_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL',
            'web/secure/base_skin_url'                                      => 'Mage_Core_Model_Store::XML_PATH_SECURE_BASE_SKIN_URL',
            'web/secure/base_url'                                           => 'Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL',
            'web/secure/offloader_header'                                   => 'Mage_Core_Model_Store::XML_PATH_OFFLOADER_HEADER',
            'web/secure/use_in_adminhtml'                                   => 'Mage_Core_Model_Store::XML_PATH_SECURE_IN_ADMINHTML',
            'web/secure/use_in_frontend'                                    => 'Mage_Core_Model_Store::XML_PATH_SECURE_IN_FRONTEND',
            'web/seo/cms_canonical_tag'                                     => 'Mage_Cms_Helper_Data::XML_PATH_USE_CMS_CANONICAL_TAG',
            'web/seo/use_rewrites'                                          => 'Mage_Core_Model_Store::XML_PATH_USE_REWRITES',
            'web/session/use_admin_sid'                                     => 'Mage_Admin_Model_Session::XML_PATH_ALLOW_SID_FOR_ADMIN_AREA',
            'web/session/use_frontend_sid'                                  => 'Mage_Core_Model_Session_Abstract::XML_PATH_USE_FRONTEND_SID',
            'web/session/use_http_user_agent'                               => 'Mage_Core_Model_Session_Abstract::XML_PATH_USE_USER_AGENT',
            'web/session/use_http_via'                                      => 'Mage_Core_Model_Session_Abstract::XML_PATH_USE_HTTP_VIA',
            'web/session/use_http_x_forwarded_for'                          => 'Mage_Core_Model_Session_Abstract::XML_PATH_USE_X_FORWARDED',
            'web/session/use_remote_addr'                                   => 'Mage_Core_Model_Session_Abstract::XML_PATH_USE_REMOTE_ADDR',
            'web/unsecure/base_js_url'                                      => 'Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_JS_URL',
            'web/unsecure/base_link_url'                                    => 'Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL',
            'web/unsecure/base_media_url'                                   => 'Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_MEDIA_URL',
            'web/unsecure/base_skin_url'                                    => 'Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_SKIN_URL',
            'web/unsecure/base_url'                                         => 'Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL',
            'web/url/use_store'                                             => 'Mage_Core_Model_Store::XML_PATH_STORE_IN_URL',
            'wishlist/wishlist_link/use_qty'                                => 'Mage_Wishlist_Helper_Data::XML_PATH_WISHLIST_LINK_USE_QTY',
        ];
        // @phpcs:enable Generic.Arrays.DuplicateArrayKey.Found

        foreach ($paths as $old => $new) {
            $map[] = new ReplaceArgumentDefaultValue(Mage::class, 'getStoreConfig', 0, $old, $new);
        }

        return $map;
    }
}
