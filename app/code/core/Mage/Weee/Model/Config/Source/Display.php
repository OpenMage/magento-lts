<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Weee
 */
class Mage_Weee_Model_Config_Source_Display
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        /**
         * VAT is not applicable to FPT separately (we can't have FPT incl/excl VAT)
         */
        return [
            [
                'value' => 0,
                'label' => Mage::helper('weee')->__('Including FPT only'),
            ],
            [
                'value' => 1,
                'label' => Mage::helper('weee')->__('Including FPT and FPT description'),
            ],
            //array('value'=>4, 'label'=>Mage::helper('weee')->__('Including FPT and FPT description [incl. FPT VAT]')),
            [
                'value' => 2,
                'label' => Mage::helper('weee')->__('Excluding FPT, FPT description, final price'),
            ],
            [
                'value' => 3,
                'label' => Mage::helper('weee')->__('Excluding FPT'),
            ],
        ];
    }
}
