<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tag accordion
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tag_Edit_Accordion extends Mage_Adminhtml_Block_Widget_Accordion
{
    /**
     * Add products and customers accordion to layout
     *
     */
    protected function _prepareLayout()
    {
        if (is_null(Mage::registry('current_tag')->getId())) {
            return $this;
        }

        $tagModel = Mage::registry('current_tag');

        $this->setId('tag_customer_and_product_accordion');

        $this->addItem('tag_customer', [
            'title'         => Mage::helper('tag')->__('Customers Submitted this Tag'),
            'ajax'          => true,
            'content_url'   => $this->getUrl('*/*/customer', ['ret' => 'all', 'tag_id' => $tagModel->getId(), 'store' => $tagModel->getStoreId()]),
        ]);

        $this->addItem('tag_product', [
            'title'         => Mage::helper('tag')->__('Products Tagged by Customers'),
            'ajax'          => true,
            'content_url'   => $this->getUrl('*/*/product', ['ret' => 'all', 'tag_id' => $tagModel->getId(), 'store' => $tagModel->getStoreId()]),
        ]);
        return parent::_prepareLayout();
    }
}
