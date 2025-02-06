<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Creditmemo entity resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Creditmemo extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('creditmemo')->setConnection(
            $resource->getConnection('sales_read'),
            $resource->getConnection('sales_write'),
        );
    }
}
