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
class Mage_Adminhtml_Model_System_Config_Source_Shipping_Tablerate
{
    public function toOptionArray()
    {
        $tableRate = Mage::getSingleton('shipping/carrier_tablerate');
        $arr = [];
        foreach ($tableRate->getCode('condition_name') as $k => $v) {
            $arr[] = ['value' => $k, 'label' => $v];
        }

        return $arr;
    }
}
