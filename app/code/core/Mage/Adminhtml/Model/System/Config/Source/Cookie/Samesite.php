<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Cookie_Samesite
{
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'None', 'label' => Mage::helper('adminhtml')->__('None')],
            ['value' => 'Strict', 'label' => Mage::helper('adminhtml')->__('Strict')],
            ['value' => 'Lax', 'label' => Mage::helper('adminhtml')->__('Lax')],
        ];
    }
}
