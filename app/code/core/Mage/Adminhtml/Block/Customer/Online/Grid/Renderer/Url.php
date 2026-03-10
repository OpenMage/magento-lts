<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Online Customer last URL renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Online_Grid_Renderer_Url extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        return empty($value) ? '' : htmlspecialchars($value);
    }
}
