<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Checkbox grid column filter
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Radio extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
        return [
            [
                'label' => Mage::helper('adminhtml')->__('Any'),
                'value' => '',
            ],
            [
                'label' => Mage::helper('adminhtml')->__('Yes'),
                'value' => 1,
            ],
            [
                'label' => Mage::helper('adminhtml')->__('No'),
                'value' => 0,
            ],
        ];
    }

    public function getCondition()
    {
        if ($this->getValue()) {
            return $this->getColumn()->getValue();
        }

        return [
            ['neq' => $this->getColumn()->getValue()],
            ['is' => new Zend_Db_Expr('NULL')],
        ];
    }
}
