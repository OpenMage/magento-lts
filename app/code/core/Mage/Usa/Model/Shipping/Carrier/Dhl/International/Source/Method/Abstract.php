<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * Source model for DHL shipping methods
 *
 * @package    Mage_Usa
 */
abstract class Mage_Usa_Model_Shipping_Carrier_Dhl_International_Source_Method_Abstract
{
    /**
     * Carrier Product Type Indicator
     *
     * @var string
     */
    protected $_contentType;

    /**
     * Show 'none' in methods list or not;
     *
     * @var bool
     */
    protected $_noneMethod = false;

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Mage_Usa_Model_Shipping_Carrier_Dhl_International $carrierModel */
        $carrierModel   = Mage::getSingleton('usa/shipping_carrier_dhl_international');
        $dhlProducts    = $carrierModel->getDhlProducts($this->_contentType);

        $options = [];
        foreach ($dhlProducts as $code => $title) {
            $options[] = ['value' => $code, 'label' => $title];
        }

        if ($this->_noneMethod) {
            array_unshift($options, ['value' => '', 'label' => Mage::helper('usa')->__('None')]);
        }

        return $options;
    }
}
