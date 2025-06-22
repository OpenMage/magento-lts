<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Block_Adminhtml_Grid_Renderer_Json extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Format JSON data for display
     *
     * @param string $json
     */
    protected function _formatJson(?string $json): string
    {
        if (empty($json)) {
            return '';
        }

        try {
            $data = Mage::helper('core')->jsonDecode($json);

            $formattedJson = json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            );

            return sprintf(
                '<pre class="paypal-json" style="max-height: 300px; overflow: auto; white-space: pre-wrap;">%s</pre>',
                $this->escapeHtml($formattedJson),
            );
        } catch (Exception) {
            return $this->escapeHtml($json);
        }
    }

    /**
     * Render cell content
     */
    public function render(Varien_Object $row): string
    {
        return $this->_formatJson($row->getData($this->getColumn()->getIndex()));
    }
}
