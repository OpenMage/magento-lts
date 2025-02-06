<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Order creditmemo configuration model
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Creditmemo_Config extends Mage_Sales_Model_Order_Total_Config_Base
{
    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_order_creditmemo_collectors';

    public function __construct()
    {
        parent::__construct(Mage::getConfig()->getNode('global/sales/order_creditmemo'));
    }
}
