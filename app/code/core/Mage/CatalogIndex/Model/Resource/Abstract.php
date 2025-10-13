<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Index resource model abstraction
 *
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_storeId    = 0;

    protected $_websiteId  = null;

    /**
     * Initialize model
     *
     */
    protected function _construct() {}

    /**
     * storeId setter
     *
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
    }

    /**
     * storeId getter
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * websiteId getter
     *
     * @return int
     */
    public function getWebsiteId()
    {
        if (is_null($this->_websiteId)) {
            $result = Mage::app()->getStore($this->getStoreId())->getWebsiteId();
            $this->_websiteId = $result;
        }

        return $this->_websiteId;
    }
}
