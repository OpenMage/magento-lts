<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 */
class Mage_Core_Model_Factory
{
    /**
     * Xml path to url rewrite model class alias
     */
    public const XML_PATH_URL_REWRITE_MODEL = 'global/url_rewrite/model';

    public const XML_PATH_INDEX_INDEX_MODEL = 'global/index/index_model';

    /**
     * Config instance
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Initialize factory
     */
    public function __construct(array $arguments = [])
    {
        $this->_config = empty($arguments['config']) ? Mage::getConfig() : $arguments['config'];
    }

    /**
     * Retrieve model object
     *
     * @param  string                        $modelClass
     * @param  array|object                  $arguments
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getModel($modelClass = '', $arguments = [])
    {
        return Mage::getModel($modelClass, $arguments);
    }

    /**
     * Retrieve model object singleton
     *
     * @param  string                   $modelClass
     * @return Mage_Core_Model_Abstract
     */
    public function getSingleton($modelClass = '', array $arguments = [])
    {
        return Mage::getSingleton($modelClass, $arguments);
    }

    /**
     * Retrieve object of resource model
     *
     * @param  string $modelClass
     * @param  array  $arguments
     * @return Object
     */
    public function getResourceModel($modelClass, $arguments = [])
    {
        return Mage::getResourceModel($modelClass, $arguments);
    }

    /**
     * Retrieve helper instance
     *
     * @param  string                    $helperClass
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
        /** @var Mage_Core_Model_Url_Rewrite $model */
        $model = $this->getModel($this->getUrlRewriteClassAlias());
        return $model;
    }

    /**
     * Retrieve alias for url_rewrite model
     *
     * @return string
     */
    public function getUrlRewriteClassAlias()
    {
        return (string) $this->_config->getNode(self::XML_PATH_URL_REWRITE_MODEL);
    }

    /**
     * @return string
     */
    public function getIndexClassAlias()
    {
        return (string) $this->_config->getNode(self::XML_PATH_INDEX_INDEX_MODEL);
    }
}
