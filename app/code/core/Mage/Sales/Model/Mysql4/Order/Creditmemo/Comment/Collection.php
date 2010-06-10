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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order creditmemo comments collection
 *
 */
class Mage_Sales_Model_Mysql4_Order_Creditmemo_Comment_Collection extends Mage_Sales_Model_Mysql4_Collection_Abstract
{
    protected $_eventPrefix = 'sales_order_creditmemo_comment_collection';
    protected $_eventObject = 'order_creditmemo_comment_collection';

    protected function _construct()
    {
        $this->_init('sales/order_creditmemo_comment');
    }

    /**
     * Set creditmemo filter
     *
     * @param int $creditmemoId
     * @return Mage_Sales_Model_Mysql4_Order_Creditmemo_Comment_Collection
     */
    public function setCreditmemoFilter($creditmemoId)
    {
        $this->addFieldToFilter('parent_id', $creditmemoId);
        return $this;
    }

    /**
     * Set created_at sort order
     *
     * @param string $direction
     * @return Mage_Sales_Model_Mysql4_Order_Creditmemo_Comment_Collection
     */
    public function setCreatedAtOrder($direction='desc')
    {
        $this->setOrder('created_at', $direction);
        return $this;
    }
}
