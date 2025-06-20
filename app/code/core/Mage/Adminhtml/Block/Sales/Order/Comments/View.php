<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Invoice view  comments form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Comments_View extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block.'));
        }
        $this->setEntity($this->getParentBlock()->getSource());
        return parent::_beforeToHtml();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'id'      => 'submit_comment_button',
                'label'   => Mage::helper('sales')->__('Submit Comment'),
                'class'   => 'save',
            ]);
        $this->setChild('submit_button', $button);

        return parent::_prepareLayout();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', ['id' => $this->getEntity()->getId()]);
    }

    public function canSendCommentEmail()
    {
        return match ($this->getParentType()) {
            'invoice' => Mage::helper('sales')->canSendInvoiceCommentEmail(
                $this->getEntity()->getOrder()->getStore()->getId(),
            ),
            'shipment' => Mage::helper('sales')->canSendShipmentCommentEmail(
                $this->getEntity()->getOrder()->getStore()->getId(),
            ),
            'creditmemo' => Mage::helper('sales')->canSendCreditmemoCommentEmail(
                $this->getEntity()->getOrder()->getStore()->getId(),
            ),
            default => true,
        };
    }

    /**
     * Replace links in string
     *
     * @param string|string[] $data
     * @param array|null $allowedTags
     * @return null|string|string[]
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        return Mage::helper('adminhtml/sales')->escapeHtmlWithLinks($data, $allowedTags);
    }
}
