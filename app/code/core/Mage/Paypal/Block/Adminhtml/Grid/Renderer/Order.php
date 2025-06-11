<?php
class Mage_Paypal_Block_Adminhtml_Grid_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render order link
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $incrementId = $row->getData($this->getColumn()->getIndex());
        if (!$incrementId) {
            return '';
        }
        
        $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        if (!$order->getId()) {
            return $this->escapeHtml($incrementId);
        }
        
        return sprintf(
            '<a href="%s">%s</a>',
            $this->getUrl('*/sales_order/view', ['order_id' => $order->getId()]),
            $this->escapeHtml($incrementId)
        );
    }
} 