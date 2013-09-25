<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog factory
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Factory extends Mage_Core_Model_Factory
{
    /**
     * Xml path to the category url rewrite helper class alias
     */
    const XML_PATH_CATEGORY_URL_REWRITE_HELPER_CLASS = 'global/catalog/category/url_rewrite/helper';

    /**
     * Xml path to the product url rewrite helper class alias
     */
    const XML_PATH_PRODUCT_URL_REWRITE_HELPER_CLASS = 'global/catalog/product/url_rewrite/helper';

    /**
     * Path to product_url model alias
     */
    const XML_PATH_PRODUCT_URL_MODEL = 'global/catalog/product/url/model';

    /**
     * Path to category_url model alias
     */
    const XML_PATH_CATEGORY_URL_MODEL = 'global/catalog/category/url/model';

    /**
     * Returns category url rewrite helper instance
     *
     * @return Mage_Catalog_Helper_Category_Url_Rewrite_Interface
     */
    public function getCategoryUrlRewriteHelper()
    {
        return $this->getHelper(
            (string)$this->_config->getNode(self::XML_PATH_CATEGORY_URL_REWRITE_HELPER_CLASS)
        );
    }

    /**
     * Returns product url rewrite helper instance
     *
     * @return Mage_Catalog_Helper_Product_Url_Rewrite_Interface
     */
    public function getProductUrlRewriteHelper()
    {
        return $this->getHelper(
            (string)$this->_config->getNode(self::XML_PATH_PRODUCT_URL_REWRITE_HELPER_CLASS)
        );
    }

    /**
     * Retrieve product_url instance
     *
     * @return Mage_Catalog_Model_Product_Url
     */
    public function getProductUrlInstance()
    {
        return $this->getModel(
            (string)$this->_config->getNode(self::XML_PATH_PRODUCT_URL_MODEL)
        );
    }

    /**
     * Retrieve category_url instance
     *
     * @return Mage_Catalog_Model_Category_Url
     */
    public function getCategoryUrlInstance()
    {
        return $this->getModel(
            (string)$this->_config->getNode(self::XML_PATH_CATEGORY_URL_MODEL)
        );
    }
}
