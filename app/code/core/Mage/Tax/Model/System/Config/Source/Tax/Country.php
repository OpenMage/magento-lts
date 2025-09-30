<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
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
