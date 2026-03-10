<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
 */

/**
 * Product alert for back in stock resource model
 *
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_Model_Resource_Stock extends Mage_ProductAlert_Model_Resource_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('productalert/stock', 'alert_stock_id');
    }

    /**
     * Before save action
     *
     * @param Mage_ProductAlert_Model_Stock $object
     * @inheritDoc
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (is_null($object->getId()) && $object->getCustomerId()
                && $object->getProductId() && $object->getWebsiteId()
        ) {
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
}
