<?php
/**
 * Quote entity resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
