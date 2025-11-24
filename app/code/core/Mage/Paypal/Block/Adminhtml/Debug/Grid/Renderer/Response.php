<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Renderer for response_body column with copy functionality
 */
class Mage_Paypal_Block_Adminhtml_Debug_Grid_Renderer_Response extends Mage_Paypal_Block_Adminhtml_Grid_Renderer_Json
{
    /**
     * Renders the response_body column with copy functionality
     */
    public function render(Varien_Object $row): string
    {
        $value = $row->getData($this->getColumn()->getIndex());

        if (empty($value)) {
            return '';
        }

        $formattedValue = $this->_formatJson($value);
        $rawValue = $this->escapeHtml($value);

        return '<div data-copy-text="' . $rawValue . '">' . $formattedValue . '</div>';
    }
}
