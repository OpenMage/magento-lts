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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ProductAlert Price Customer collection
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_Model_Resource_Price_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection
{
    /**
     * join productalert price data to customer collection
     *
     * @param int $productId
     * @param int $websiteId
     * @return $this
     */
    public function join($productId, $websiteId)
    {
        $this->getSelect()->join(
            ['alert' => $this->getTable('productalert/price')],
            'e.entity_id=alert.customer_id',
            ['alert_price_id', 'price', 'add_date', 'last_send_date', 'send_count', 'status'],
        );

        $this->getSelect()->where('alert.product_id=?', $productId);
        if ($websiteId) {
            $this->getSelect()->where('alert.website_id=?', $websiteId);
        }
        $this->_setIdFieldName('alert_price_id');
        $this->addAttributeToSelect('*');

        return $this;
    }
}
