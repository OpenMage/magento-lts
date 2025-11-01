<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml system template grid type filter
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Email_Template_Grid_Filter_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected static $_types = [
        null                                =>  null,
        Mage_Core_Model_Template::TYPE_HTML => 'HTML',
        Mage_Core_Model_Template::TYPE_TEXT => 'Text',
    ];

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $result = [];
        foreach (self::$_types as $code => $label) {
            $result[] = ['value' => $code, 'label' => Mage::helper('adminhtml')->__($label)];
        }

        return $result;
    }

    /**
     * @return null|array
     */
    public function getCondition()
    {
        if (is_null($this->getValue())) {
            return null;
        }

        return ['eq' => $this->getValue()];
    }
}
