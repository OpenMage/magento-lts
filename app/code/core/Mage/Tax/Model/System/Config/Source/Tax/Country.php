<?php

/**
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_System_Config_Source_Tax_Country extends Mage_Adminhtml_Model_System_Config_Source_Country
{
    protected $_options;

    /**
     * @param bool $noEmpty
     * @return array
     */
    public function toOptionArray($noEmpty = false)
    {
        $options = parent::toOptionArray($noEmpty);

        if (!$noEmpty) {
            if ($options) {
                $options[0]['label'] = Mage::helper('tax')->__('None');
            } else {
                $options = [['value' => '', 'label' => Mage::helper('tax')->__('None')]];
            }
        }

        return $options;
    }
}
