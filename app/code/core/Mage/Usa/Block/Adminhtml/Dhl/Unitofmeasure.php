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
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Frontend model for DHL shipping methods for documentation
 *
 * @category   Mage
 * @package    Mage_Usa
 */
class Mage_Usa_Block_Adminhtml_Dhl_Unitofmeasure extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function _construct()
    {
        parent::_construct();

        $carrierModel = Mage::getSingleton('usa/shipping_carrier_dhl_international');

        $this->setInch($this->jsQuoteEscape($carrierModel->getCode('unit_of_dimension_cut', 'I')));
        $this->setCm($this->jsQuoteEscape($carrierModel->getCode('unit_of_dimension_cut', 'C')));

        $this->setHeight($this->jsQuoteEscape($carrierModel->getCode('dimensions', 'height')));
        $this->setDepth($this->jsQuoteEscape($carrierModel->getCode('dimensions', 'depth')));
        $this->setWidth($this->jsQuoteEscape($carrierModel->getCode('dimensions', 'width')));

        $kgWeight = 70;

        $this->setDivideOrderWeightNoteKg(
            $this->jsQuoteEscape($this->__('Allows breaking total order weight into smaller pieces if it exeeds %s %s to ensure accurate calculation of shipping charges.', $kgWeight, 'kg'))
        );

        $weight = round(
            (float) Mage::helper('usa')->convertMeasureWeight(
                $kgWeight,
                Zend_Measure_Weight::KILOGRAM,
                Zend_Measure_Weight::POUND
            ),
            3
        );

        $this->setDivideOrderWeightNoteLbp(
            $this->jsQuoteEscape($this->__('Allows breaking total order weight into smaller pieces if it exeeds %s %s to ensure accurate calculation of shipping charges.', $weight, 'pounds'))
        );

        $this->setTemplate('usa/dhl/unitofmeasure.phtml');
    }

    /**
     * Retrieve Element HTML fragment
     *
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return parent::_getElementHtml($element) . $this->renderView();
    }
}
