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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Grid widget column renderer massaction
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox
{
    protected $_defaultWidth = 20;

    public function renderHeader()
    {
        return '&nbsp;';
    }

    public function renderProperty()
    {
        $out = parent::renderProperty();
        $out = preg_replace('/class=".*?"/i', '', $out);
        $out .= ' class="a-center"';
        return $out;
    }

    public function render(Varien_Object $row)
    {
        if ($this->getColumn()->getGrid()->getMassactionIdFieldOnlyIndexValue()){
        $this->setNoObjectId(true);
    }
        return parent::render($row);
    }
    //

    protected function _getCheckboxHtml($value, $checked)
    {
        return '<input type="checkbox" name="'.$this->getColumn()->getName().'" value="' . $value . '" class="massaction-checkbox"'.$checked.'/>';
    }

}
