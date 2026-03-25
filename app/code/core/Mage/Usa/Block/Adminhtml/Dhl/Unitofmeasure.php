<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * Frontend model for DHL shipping methods for documentation
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Block_Adminhtml_Dhl_Unitofmeasure extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _construct()
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
            $this->jsQuoteEscape($this->__('Allows breaking total order weight into smaller pieces if it exeeds %s %s to ensure accurate calculation of shipping charges.', $kgWeight, 'kg')),
        );

        $weight = round(
            (float) Mage::helper('usa')->convertMeasureWeight(
                $kgWeight,
                Mage_Core_Helper_Measure_Weight::KILOGRAM,
                Mage_Core_Helper_Measure_Weight::POUND,
            ),
            3,
        );

        $this->setDivideOrderWeightNoteLbp(
            $this->jsQuoteEscape($this->__('Allows breaking total order weight into smaller pieces if it exeeds %s %s to ensure accurate calculation of shipping charges.', $weight, 'pounds')),
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
