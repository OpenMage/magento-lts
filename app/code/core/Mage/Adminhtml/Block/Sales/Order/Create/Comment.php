<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Create order comment form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Comment extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    protected $_form;

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-comment';
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Order Comment');
    }

    /**
     * @return string
     */
    public function getCommentNote()
    {
        return $this->escapeHtml($this->getQuote()->getCustomerNote());
    }

    /**
     * @return bool
     */
    public function getNoteNotify()
    {
        $notify = $this->getQuote()->getCustomerNoteNotify();
        if (is_null($notify) || $notify) {
            return true;
        }
        return false;
    }
}
