<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Product Downloads Purchases Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Product_Downloads_Renderer_Purchases extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders Purchases value
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if (($value = $row->getData($this->getColumn()->getIndex())) > 0) {
            return $value;
        }
        return $this->__('Unlimited');
    }
}
