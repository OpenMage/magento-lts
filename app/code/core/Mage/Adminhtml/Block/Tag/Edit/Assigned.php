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
class Mage_Adminhtml_Block_Tag_Edit_Assigned extends Mage_Adminhtml_Block_Widget_Accordion
{
    /**
     * Add Assigned products accordion to layout
     *
     */
    protected function _prepareLayout()
    {
        if (is_null(Mage::registry('current_tag')->getId())) {
            return $this;
        }

        $tagModel = Mage::registry('current_tag');

        $this->setId('tag_assigned_grid');

        $this->addItem('tag_assign', [
            'title'         => Mage::helper('tag')->__('Products Tagged by Administrators'),
            'ajax'          => true,
            'content_url'   => $this->getUrl('*/*/assigned', ['ret' => 'all', 'tag_id' => $tagModel->getId(), 'store' => $tagModel->getStoreId()]),
        ]);
        return parent::_prepareLayout();
    }
}
