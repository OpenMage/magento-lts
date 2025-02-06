<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/**
 * Tax discount totals calculation model
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Sales_Total_Quote_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Calculate discount tac amount
     *
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
}
