<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Checkbox grid column filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * @return string
     */
    public function getHtml()
    {
        return '<span class="head-massaction">' . parent::getHtml() . '</span>';
    }

    /**
     * @return array[]
     */
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

    /**
     * @return array|null
     */
    public function getCondition()
    {
        if ($this->getValue()) {
            return $this->getColumn()->getValue();
        } else {
            return [
                ['neq' => $this->getColumn()->getValue()],
                ['is' => new Zend_Db_Expr('NULL')],
            ];
        }
    }
}
