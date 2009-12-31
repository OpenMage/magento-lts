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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Select grid column filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
    protected function _getOptions()
    {
        $colOptions = $this->getColumn()->getOptions();
        if ( !empty($colOptions) && is_array($colOptions) ) {
            $options = array(array('value' => null, 'label' => ''));
            foreach ($colOptions as $value => $label) {
                $options[] = array('value' => $value, 'label' => $label);
            }
            return $options;
        }
        return array();
    }

    public function getHtml()
    {
        $html = '<select name="'.$this->_getHtmlName().'" id="'.$this->_getHtmlId().'" class="no-changes">';
        $value = $this->getValue();
        foreach ($this->_getOptions() as $option){
        	$selected = ( ($option['value'] == $value && (!is_null($value))) ? ' selected="selected"' : '' );
            $html.= '<option value="'.$option['value'].'"'.$selected.'>'.$option['label'].'</option>';
        }
        $html.='</select>';
        return $html;
    }

	public function getCondition()
	{
		if (is_null($this->getValue())) {
			return null;
		}
		return array('eq' => $this->getValue());
	}

}