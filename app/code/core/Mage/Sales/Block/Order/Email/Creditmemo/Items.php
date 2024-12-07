<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
