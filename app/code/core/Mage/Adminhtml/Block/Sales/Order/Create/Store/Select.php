<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create select store block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Store_Select extends Mage_Adminhtml_Block_Store_Switcher
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sc_store_select');
    }
}
