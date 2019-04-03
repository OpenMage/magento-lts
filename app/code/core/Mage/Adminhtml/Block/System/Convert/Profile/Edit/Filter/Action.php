<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * System Convert History action filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Filter_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
    public function getHtml()
    {
        $values = array(
            ''       => '',
            'create' => Mage::helper('adminhtml')->__('Create'),
            'run'    => Mage::helper('adminhtml')->__('Run'),
            'update' => Mage::helper('adminhtml')->__('Update'),
        );
        $value = $this->getValue();

        $html  = '<select name="' . ($this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId()) . '" ' . $this->getColumn()->getValidateClass() . '>';
        foreach ($values as $k => $v) {
            $html .= '<option value="'.$k.'"' . ($value == $k ? ' selected="selected"' : '') . '>'.$v.'</option>';
        }
        $html .= '</select>';
        return $html;
    }
}
