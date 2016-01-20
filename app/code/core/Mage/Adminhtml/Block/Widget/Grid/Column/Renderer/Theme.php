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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml grid item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Theme
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $options = $this->getOptions();
        $value   = $this->_getValue($row);
        if ($value == '') {
            $value = 'all';
        }

        return $this->escapeHtml($this->_getValueLabel($options, $value));
    }

    /**
     * Retrieve options setted in column.
     * Or load if options was not set.
     *
     * @return array
     */
    public function getOptions()
    {
        if ($this->getColumn()->getFilter()) {
            $options = $this->getColumn()->getFilter()->getOptions();
        } else {
            $options = $this->getColumn()->getOptions();
        }
        if (empty($options) || !is_array($options)) {
            $options = Mage::getModel('core/design_source_design')
                ->setIsFullLabel(true)->getAllOptions(false);
        }

        return $options;
    }

    /**
     * Retrieve value label from options array
     *
     * @param array $options
     * @param string $value
     * @return mixed
     */
    protected function _getValueLabel($options, $value)
    {
        if (empty($options) || !is_array($options)) {
            return false;
        }

        foreach ($options as $option) {
            if (is_array($option['value'])) {
                $label = $this->_getValueLabel($option['value'], $value);
                if ($label) {
                    return $label;
                }
            } else {
                if ($option['value'] == $value) {
                    return $option['label'];
                }
            }
        }

        return false;
    }
}
