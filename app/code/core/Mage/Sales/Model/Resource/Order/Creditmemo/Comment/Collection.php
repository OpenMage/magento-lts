<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order creditmemo comments collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Creditmemo_Comment_Collection extends Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_creditmemo_comment_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_creditmemo_comment_collection';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('sales/order_creditmemo_comment');
    }

    /**
     * Set creditmemo filter
     *
     * @param  int   $creditmemoId
     * @return $this
     */
    public function setCreditmemoFilter($creditmemoId)
    {
        return $this->setParentFilter($creditmemoId);
    }
}
