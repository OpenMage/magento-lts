<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create totals table block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Totals_Table extends Mage_Adminhtml_Block_Template
{
    protected $_websiteCollection = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_totals_table');
    }
}
