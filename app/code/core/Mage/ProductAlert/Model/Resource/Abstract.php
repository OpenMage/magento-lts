<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product alert for back in abstract resource model
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 */
abstract class Mage_ProductAlert_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Retrieve alert row by object parameters
     *
     * @param Mage_Core_Model_Abstract $object
     * @return array|false
     */
    protected function _getAlertRow(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getReadAdapter();
        if ($object->getCustomerId() && $object->getProductId() && $object->getWebsiteId()) {
            $select = $adapter->select()
                ->from($this->getMainTable())
                ->where('customer_id = :customer_id')
                ->where('product_id  = :product_id')
                ->where('website_id  = :website_id');
            $bind = [
                ':customer_id' => $object->getCustomerId(),
                ':product_id'  => $object->getProductId(),
                ':website_id'  => $object->getWebsiteId()
            ];
            return $adapter->fetchRow($select, $bind);
        }
        return false;
    }

    /**
     * Load object data by parameters
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_ProductAlert_Model_Resource_Abstract
     */
    public function loadByParam(Mage_Core_Model_Abstract $object)
    {
        $row = $this->_getAlertRow($object);
        if ($row) {
            $object->setData($row);
        }
        return $this;
    }

    /**
     * Delete all customer alerts on website
     *
     * @param Mage_Core_Model_Abstract $object
     * @param int $customerId
     * @param int $websiteId
     * @return Mage_ProductAlert_Model_Resource_Abstract
     */
    public function deleteCustomer(Mage_Core_Model_Abstract $object, $customerId, $websiteId = null)
    {
        $adapter = $this->_getWriteAdapter();
        $where   = [];
        $where[] = $adapter->quoteInto('customer_id=?', $customerId);
        if ($websiteId) {
            $where[] = $adapter->quoteInto('website_id=?', $websiteId);
        }
        $adapter->delete($this->getMainTable(), $where);
        return $this;
    }
}
