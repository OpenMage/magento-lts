<?php
class Mage_Paypal_Block_Adminhtml_Grid_Renderer_Json extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Format JSON data for display
     *
     * @param string $json
     * @return string
     */
    protected function _formatJson($json)
    {
        if (!$json) {
            return '';
        }

        try {
            // Decode JSON
            $data = Mage::helper('core')->jsonDecode($json);
            
            // Format with proper indentation and spacing
            $formattedJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            
            // Escape HTML entities
            $formattedJson = $this->escapeHtml($formattedJson);
            
            return sprintf('<pre class="paypal-json" style="max-height: 300px; overflow: auto; white-space: pre-wrap;">%s</pre>', $formattedJson);
        } catch (Exception $e) {
            return $this->escapeHtml($json);
        }
    }

    /**
     * Render cell content
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        return $this->_formatJson($row->getData($this->getColumn()->getIndex()));
    }
}
