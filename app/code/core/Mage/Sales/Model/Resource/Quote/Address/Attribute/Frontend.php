<?php
/**
 * Quote address attribute frontend resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * Fetch totals
     *
     * @return $this|array
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        return [];
    }
}
