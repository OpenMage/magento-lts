<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source model for DHL shipping methods
 *
 * @category   Mage
 * @package    Mage_Usa
 */
abstract class Mage_Usa_Model_Shipping_Carrier_Dhl_International_Source_Method_Abstract
{
    /**
     * Carrier Product Type Indicator
     *
     * @var string $_contentType
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
