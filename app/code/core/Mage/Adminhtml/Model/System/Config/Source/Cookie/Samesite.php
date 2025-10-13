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
class Mage_Adminhtml_Model_System_Config_Source_Cookie_Samesite
{
    public const NONE = 'None';

    public const STRICT = 'Strict';

    public const LAX = 'Lax';

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::NONE, 'label' => Mage::helper('adminhtml')->__('None')],
            ['value' => self::STRICT, 'label' => Mage::helper('adminhtml')->__('Strict')],
            ['value' => self::LAX, 'label' => Mage::helper('adminhtml')->__('Lax')],
        ];
    }
}
