<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Nominal subtotal total
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Quote_Address_Total_Nominal_Subtotal extends Mage_Sales_Model_Quote_Address_Total_Subtotal
{
    /**
     * Don't add amounts to address
     *
     * @var bool
     */
    protected $_canAddAmountToAddress = false;

    /**
     * Custom row total key
     *
     * @var string
     */
    protected $_itemRowTotalKey = 'row_total';

    /**
     * Don't fetch anything
     *
     * @return array|Mage_Sales_Model_Quote_Address_Total_Abstract
     */
    #[Override]
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return Mage_Sales_Model_Quote_Address_Total_Abstract::fetch($address);
    }

    /**
     * Get regular payment label
     *
     * @return string
     */
    #[Override]
    public function getLabel()
    {
        return Mage::helper('sales')->__('Regular Payment');
    }

    /**
     * Get nominal items only
     *
     * @return array
     */
    #[Override]
    protected function _getAddressItems(Mage_Sales_Model_Quote_Address $address)
    {
        return $address->getAllNominalItems();
    }
}
