<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Payment_Cctype
{
    public function toOptionArray()
    {
        $options =  [];

        foreach (Mage::getSingleton('payment/config')->getCcTypes() as $code => $name) {
            $options[] = [
                'value' => $code,
                'label' => $name,
            ];
        }

        return $options;
    }
}
