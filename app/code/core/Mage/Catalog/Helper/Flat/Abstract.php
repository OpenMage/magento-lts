<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog flat abstract helper
 *
 * @package    Mage_Catalog
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
     * @var null|Mage_Index_Model_Process
     */
    protected $_process = null;

    /**
     * Flag for accessibility
     *
     * @var null|bool
     */
    protected $_isAccessible = null;

    /**
     * Flag for availability
     *
     * @var null|bool
     */
    protected $_isAvailable = null;

    /**
     * Check if Catalog Flat Data has been initialized
     *
     * @param  null|bool|int|Mage_Core_Model_Store $store Store(id) for which the value is checked
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
