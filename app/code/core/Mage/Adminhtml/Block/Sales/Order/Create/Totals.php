<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create totals block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Totals extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    protected $_totalRenderers;

    protected $_defaultRenderer = 'adminhtml/sales_order_create_totals_default';

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_totals');
    }

    public function getTotals()
    {
        return $this->getQuote()->getTotals();
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Order Totals');
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-money';
    }

    protected function _getTotalRenderer($code)
    {
        $blockName = $code . '_total_renderer';
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            $block = $this->_defaultRenderer;
            $config = Mage::getConfig()->getNode("global/sales/quote/totals/{$code}/admin_renderer");
            if ($config) {
                $block = (string) $config;
            }

            $block = $this->getLayout()->createBlock($block, $blockName);
        }

        /**
         * Transfer totals to renderer
         */
        $block->setTotals($this->getTotals());
        return $block;
    }

    public function renderTotal($total, $area = null, $colspan = 1)
    {
        return $this->_getTotalRenderer($total->getCode())
            ->setTotal($total)
            ->setColspan($colspan)
            ->setRenderingArea(is_null($area) ? -1 : $area)
            ->toHtml();
    }

    public function renderTotals($area = null, $colspan = 1)
    {
        $html = '';
        foreach ($this->getTotals() as $total) {
            if ($total->getArea() != $area && $area != -1) {
                continue;
            }

            $html .= $this->renderTotal($total, $area, $colspan);
        }

        return $html;
    }

    /**
     * @return bool
     */
    public function canSendNewOrderConfirmationEmail()
    {
        return Mage::helper('sales')->canSendNewOrderConfirmationEmail($this->getQuote()->getStoreId());
    }
}
