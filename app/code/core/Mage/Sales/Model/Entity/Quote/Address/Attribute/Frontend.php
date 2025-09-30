<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
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
