<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * @return $this|array
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        return [];
    }
}
