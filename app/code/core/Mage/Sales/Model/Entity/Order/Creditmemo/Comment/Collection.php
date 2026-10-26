<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Creditmemo comments collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Creditmemo_Comment_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_creditmemo_comment');
    }

    /**
     * @param  int   $creditmemoId
     * @return $this
     */
    public function setCreditmemoFilter($creditmemoId)
    {
        $this->addAttributeToFilter('parent_id', $creditmemoId);
        return $this;
    }

    /**
     * @param  string $order
     * @return $this
     */
    public function setCreatedAtOrder($order = 'desc')
    {
        $this->setOrder('created_at', $order);
        return $this;
    }
}
