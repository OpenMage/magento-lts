<?php
/**
 * Order invoice configuration model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Invoice_Config extends Mage_Sales_Model_Order_Total_Config_Base
{
    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_order_invoice_collectors';

    public function __construct()
    {
        parent::__construct(Mage::getConfig()->getNode('global/sales/order_invoice'));
    }
}
