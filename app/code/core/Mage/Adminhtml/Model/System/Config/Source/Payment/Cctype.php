<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
