<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @return $this
     */
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
}
