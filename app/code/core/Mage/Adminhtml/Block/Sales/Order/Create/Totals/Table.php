<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create totals table block
 *
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
