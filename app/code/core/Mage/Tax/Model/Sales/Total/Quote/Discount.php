<?php
/**
 * Tax discount totals calculation model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
