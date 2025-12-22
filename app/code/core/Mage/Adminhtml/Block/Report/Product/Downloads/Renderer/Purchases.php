<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Product Downloads Purchases Renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Product_Downloads_Renderer_Purchases extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders Purchases value
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if (($value = $row->getData($this->getColumn()->getIndex())) > 0) {
            return $value;
        }

        return $this->__('Unlimited');
    }
}
