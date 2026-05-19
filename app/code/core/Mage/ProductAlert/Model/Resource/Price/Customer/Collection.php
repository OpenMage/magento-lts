<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
 */

/**
 * ProductAlert Price Customer collection
 *
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_Model_Resource_Price_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection
{
    /**
     * join productalert price data to customer collection
     *
     * @param  int   $productId
     * @param  int   $websiteId
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
