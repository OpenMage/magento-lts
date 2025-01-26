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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product alert for back in stock resource model
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_Model_Resource_Stock extends Mage_ProductAlert_Model_Resource_Abstract
{
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
