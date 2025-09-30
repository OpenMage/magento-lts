<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote entity resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Quote_Item extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('quote_item')->setConnection(
            $resource->getConnection('sales_read'),
            $resource->getConnection('sales_write'),
        );
    }
}
