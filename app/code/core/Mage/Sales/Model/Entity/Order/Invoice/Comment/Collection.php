<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Invoice comments collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Invoice_Comment_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_invoice_comment');
    }

    /**
     * @param int $invoiceId
     * @return $this
     */
    public function setInvoiceFilter($invoiceId)
    {
        $this->addAttributeToFilter('parent_id', $invoiceId);
        return $this;
    }

    /**
     * @param string $order
     * @return $this
     */
    public function setCreatedAtOrder($order = 'desc')
    {
        $this->setOrder('created_at', $order);
        return $this;
    }
}
