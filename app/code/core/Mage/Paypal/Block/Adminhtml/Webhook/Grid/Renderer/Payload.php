<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Block_Adminhtml_Webhook_Grid_Renderer_Payload extends Mage_Paypal_Block_Adminhtml_Grid_Renderer_Json
{
    /**
     * Render a redacted webhook payload with copy support.
     */
    #[Override]
    public function render(Varien_Object $row): string
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if (empty($value)) {
            return '';
        }

        return '<div data-copy-text="' . $this->escapeHtml((string) $value) . '">'
            . $this->_formatJson((string) $value)
            . '</div>';
    }
}
