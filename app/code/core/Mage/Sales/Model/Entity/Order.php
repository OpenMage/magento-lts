<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order entity resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('order');
        $read = $resource->getConnection('sales_read');
        $write = $resource->getConnection('sales_write');
        $this->setConnection($read, $write);
    }
}
