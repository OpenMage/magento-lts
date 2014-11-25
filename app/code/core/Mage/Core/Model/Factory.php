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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Factory class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Factory
{
    /**
     * Xml path to url rewrite model class alias
     */
    const XML_PATH_URL_REWRITE_MODEL = 'global/url_rewrite/model';

    const XML_PATH_INDEX_INDEX_MODEL = 'global/index/index_model';

    /**
     * Config instance
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Initialize factory
     *
     * @param array $arguments
     */
    public function __construct(array $arguments = array())
    {
        $this->_config = !empty($arguments['config']) ? $arguments['config'] : Mage::getConfig();
    }

    /**
     * Retrieve model object
     *
     * @param string $modelClass
     * @param array|object $arguments
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getModel($modelClass = '', $arguments = array())
    {
        return Mage::getModel($modelClass, $arguments);
    }

    /**
     * Retrieve model object singleton
     *
     * @param string $modelClass
     * @param array $arguments
     * @return Mage_Core_Model_Abstract
     */
    public function getSingleton($modelClass = '', array $arguments = array())
    {
        return Mage::getSingleton($modelClass, $arguments);
    }

    /**
     * Retrieve object of resource model
     *
     * @param string $modelClass
     * @param array $arguments
     * @return Object
     */
    public function getResourceModel($modelClass, $arguments = array())
    {
        return Mage::getResourceModel($modelClass, $arguments);
    }

    /**
     * Retrieve helper instance
     *
     * @param string $helperClass
     * @return Mage_Core_Helper_Abstract
     */
    public function getHelper($helperClass)
    {
        return Mage::helper($helperClass);
    }

    /**
     * Get config instance
     *
     * @return Mage_Core_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Retrieve url_rewrite instance
     *
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function getUrlRewriteInstance()
    {
        return $this->getModel($this->getUrlRewriteClassAlias());
    }

    /**
     * Retrieve alias for url_rewrite model
     *
     * @return string
     */
    public function getUrlRewriteClassAlias()
    {
        return (string)$this->_config->getNode(self::XML_PATH_URL_REWRITE_MODEL);
    }

    /**
     * @return string
     */
    public function getIndexClassAlias()
    {
        return (string)$this->_config->getNode(self::XML_PATH_INDEX_INDEX_MODEL);
    }
}
