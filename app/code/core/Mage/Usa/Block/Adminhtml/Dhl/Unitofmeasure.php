<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Frontend model for DHL shipping methods for documentation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return parent::_getElementHtml($element) . $this->renderView();
    }
}
