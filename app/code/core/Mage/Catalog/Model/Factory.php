<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Factory extends Mage_Core_Model_Factory
{
    /**
     * Xml path to the category url rewrite helper class alias
     */
    public const XML_PATH_CATEGORY_URL_REWRITE_HELPER_CLASS = 'global/catalog/category/url_rewrite/helper';

    /**
     * Xml path to the product url rewrite helper class alias
     */
    public const XML_PATH_PRODUCT_URL_REWRITE_HELPER_CLASS = 'global/catalog/product/url_rewrite/helper';

    /**
     * Path to product_url model alias
     */
    public const XML_PATH_PRODUCT_URL_MODEL = 'global/catalog/product/url/model';

    /**
     * Path to category_url model alias
     */
    public const XML_PATH_CATEGORY_URL_MODEL = 'global/catalog/category/url/model';

    /**
     * Returns category url rewrite helper instance
     *
     * @return Mage_Catalog_Helper_Category_Url_Rewrite_Interface
     */
    public function getCategoryUrlRewriteHelper()
    {
        /** @var Mage_Catalog_Helper_Category_Url_Rewrite_Interface $model */
        $model = $this->getHelper(
            (string) $this->_config->getNode(self::XML_PATH_CATEGORY_URL_REWRITE_HELPER_CLASS),
        );
        return $model;
    }

    /**
     * Returns product url rewrite helper instance
     *
     * @return Mage_Catalog_Helper_Product_Url_Rewrite_Interface
     */
    public function getProductUrlRewriteHelper()
    {
        /** @var Mage_Catalog_Helper_Product_Url_Rewrite_Interface $model */
        $model = $this->getHelper(
            (string) $this->_config->getNode(self::XML_PATH_PRODUCT_URL_REWRITE_HELPER_CLASS),
        );
        return $model;
    }

    /**
     * Retrieve product_url instance
     *
     * @return Mage_Catalog_Model_Product_Url
     */
    public function getProductUrlInstance()
    {
        /** @var Mage_Catalog_Model_Product_Url $model */
        $model = $this->getModel(
            (string) $this->_config->getNode(self::XML_PATH_PRODUCT_URL_MODEL),
        );
        return $model;
    }

    /**
     * Retrieve category_url instance
     *
     * @return Mage_Catalog_Model_Category_Url
     */
    public function getCategoryUrlInstance()
    {
        /** @var Mage_Catalog_Model_Category_Url $model */
        $model = $this->getModel(
            (string) $this->_config->getNode(self::XML_PATH_CATEGORY_URL_MODEL),
        );
        return $model;
    }
}
