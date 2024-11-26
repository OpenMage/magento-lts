<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
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
        $this->_config = !empty($arguments['config']) ? $arguments['config'] : Mage::getConfig();
    }

    /**
     * Retrieve model object
     *
     * @param string $modelClass
     * @param array|object $arguments
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getModel($modelClass = '', $arguments = [])
    {
        return Mage::getModel($modelClass, $arguments);
    }

    /**
     * Retrieve model object singleton
     *
     * @param string $modelClass
     * @return Mage_Core_Model_Abstract
     */
    public function getSingleton($modelClass = '', array $arguments = [])
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
    public function getResourceModel($modelClass, $arguments = [])
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
