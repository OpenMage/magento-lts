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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog flat abstract helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Helper_Flat_Abstract extends Mage_Core_Helper_Abstract
{
    /**
     * Catalog Flat index process code
     *
     * @var null|string
     */
    protected $_indexerCode = null;

    /**
     * Store catalog Flat index process instance
     *
     * @var Mage_Index_Model_Process|null
     */
    protected $_process = null;

    /**
     * Flag for accessibility
     *
     * @var bool
     */
    protected $_isAccessible = null;

    /**
     * Flag for availability
     *
     * @var bool
     */
    protected $_isAvailable = null;

    /**
     * Check if Catalog Flat Data has been initialized
     *
     * @param null|bool|int|Mage_Core_Model_Store $store Store(id) for which the value is checked
     * @return bool
     */
    abstract public function isBuilt($store = null);

    /**
     * Check if Catalog Category Flat Data is enabled
     *
     * @param mixed $deprecatedParam this parameter is deprecated and no longer in use
     *
     * @return bool
     */
    abstract public function isEnabled($deprecatedParam = false);

    /**
     * Check if Catalog Category Flat Data is available
     * without lock check
     *
     * @return bool
     */
    public function isAccessible()
    {
        if (is_null($this->_isAccessible)) {
            $this->_isAccessible = $this->isEnabled()
                && $this->getProcess()->getStatus() != Mage_Index_Model_Process::STATUS_RUNNING;
        }
        return $this->_isAccessible;
    }

    /**
     * Check if Catalog Category Flat Data is available for use
     *
     * @return bool
     */
    public function isAvailable()
    {
        if (is_null($this->_isAvailable)) {
            $this->_isAvailable = $this->isAccessible() && !$this->getProcess()->isLocked();
        }
        return $this->_isAvailable;
    }

    /**
     * Retrieve Catalog Flat index process
     *
     * @return Mage_Index_Model_Process
     */
    public function getProcess()
    {
        if (is_null($this->_process)) {
            $this->_process = Mage::getModel('index/process')
                ->load($this->_indexerCode, 'indexer_code');
        }
        return $this->_process;
    }
}
