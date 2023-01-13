<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkbox grid column filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
                'value' => ''
            ],
            [
                'label' => Mage::helper('adminhtml')->__('Yes'),
                'value' => 1
            ],
            [
                'label' => Mage::helper('adminhtml')->__('No'),
                'value' => 0
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
                ['is' => new Zend_Db_Expr('NULL')]
            ];
        }
    }
}
