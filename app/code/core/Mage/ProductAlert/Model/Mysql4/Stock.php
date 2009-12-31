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
 * @category   Mage
 * @package    Mage_ProductAlert
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product alert for back in stock resource model
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Model_Mysql4_Stock extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('productalert/stock', 'alert_stock_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (is_null($object->getId()) && $object->getCustomerId() && $object->getProductId() && $object->getWebsiteId()) {
            if ($row = $this->_getAlertRow($object)) {
                $object->addData($row);
                $object->setStatus(0);
            }
        }
        if (is_null($object->getAddDate())) {
            $object->setAddDate(Mage::getModel('core/date')->gmtDate());
            $object->setStatus(0);
        }
        return parent::_beforeSave($object);
    }

    protected function _getAlertRow(Mage_Core_Model_Abstract $object)
    {
        if ($object->getCustomerId() && $object->getProductId() && $object->getWebsiteId()) {
            $sql = $this->_getWriteAdapter()->select()
                ->from($this->getMainTable())
                ->where('customer_id=?', $object->getCustomerId())
                ->where('product_id=?', $object->getProductId())
                ->where('website_id=?', $object->getWebsiteId());
            return $this->_getWriteAdapter()->fetchRow($sql);
        }
        return false;
    }

    public function loadByParam(Mage_Core_Model_Abstract $object)
    {
        $row = $this->_getAlertRow($object);
        if ($row) {
            $object->setData($row);
        }
        return $this;
    }

    public function deleteCustomer(Mage_Core_Model_Abstract $object, $customerId, $websiteId)
    {
        $where   = array();
        $where[] = $this->_getWriteAdapter()->quoteInto('customer_id=?', $customerId);
        if ($websiteId) {
            $where[] = $this->_getWriteAdapter()->quoteInto('website_id=?', $websiteId);
        }
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }
}