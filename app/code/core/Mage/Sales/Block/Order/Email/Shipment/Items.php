<?php
/**
 * Sales Order Email Shipment items
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Order_Email_Shipment_Items extends Mage_Sales_Block_Items_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _prepareItem(Mage_Core_Block_Abstract $renderer)
    {
        $renderer->getItem()->setOrder($this->getOrder());
        $renderer->getItem()->setSource($this->getShipment());

        return parent::_prepareItem($renderer);
    }
}
