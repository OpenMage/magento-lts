<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Renderer for exception_message column with copy functionality
 */
class Mage_Paypal_Block_Adminhtml_Debug_Grid_Renderer_Exception extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders the exception_message column with copy functionality
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if (empty($value)) {
            return '';
        }

        $escapedValue = $this->escapeHtml($value);

        return '<span data-copy-text="' . $escapedValue . '">' . $escapedValue . '</span>';
    }
}
