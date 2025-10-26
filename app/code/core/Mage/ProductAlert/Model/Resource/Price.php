<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
 */

/**
 * Product alert for changed price resource model
 *
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_Model_Resource_Price extends Mage_ProductAlert_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('productalert/price', 'alert_price_id');
    }

    /**
     * Before save process, check exists the same alert
     *
     * @param Mage_ProductAlert_Model_Price $object
     * @inheritDoc
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (is_null($object->getId()) && $object->getCustomerId()
                && $object->getProductId() && $object->getWebsiteId()
        ) {
            if ($row = $this->_getAlertRow($object)) {
                $price = $object->getPrice();
                $object->addData($row);
                if ($price) {
                    $object->setPrice($price);
                }

                $object->setStatus(0);
            }
        }

        if (is_null($object->getAddDate())) {
            $object->setAddDate(Mage::getModel('core/date')->gmtDate());
        }

        return parent::_beforeSave($object);
    }
}
