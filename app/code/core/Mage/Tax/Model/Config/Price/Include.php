<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Config_Price_Include extends Mage_Core_Model_Config_Data
{
    public function afterSave()
    {
        parent::afterSave();
        Mage::app()->cleanCache('checkout_quote');
    }
}
