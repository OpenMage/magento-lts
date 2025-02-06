<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Sales Order Email creditmemo items
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Order_Email_Creditmemo_Items extends Mage_Sales_Block_Items_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _prepareItem(Mage_Core_Block_Abstract $renderer)
    {
        $renderer->getItem()->setOrder($this->getOrder());
        $renderer->getItem()->setSource($this->getCreditmemo());

        return parent::_prepareItem($renderer);
    }
}
