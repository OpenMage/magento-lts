<?php

/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Catalog_TimeFormat
{
    public function toOptionArray()
    {
        return [
            ['value' => '12h', 'label' => Mage::helper('adminhtml')->__('12h AM/PM')],
            ['value' => '24h', 'label' => Mage::helper('adminhtml')->__('24h')],
        ];
    }
}
