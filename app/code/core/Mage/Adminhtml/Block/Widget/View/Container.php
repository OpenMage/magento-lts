<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml view container block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_View_Container extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_objectId = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('widget/view/container.phtml');

        $this->_addButton('back', [
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'window.location.href=\'' . $this->getUrl('*/*/') . '\'',
            'class'     => 'back',
        ]);

        $this->_addButton('edit', [
            'label'     => Mage::helper('adminhtml')->__('Edit'),
            'class'     => 'edit',
            'onclick'   => 'window.location.href=\'' . $this->getEditUrl() . '\'',
        ]);
    }

    protected function _prepareLayout()
    {
        $this->setChild('plane', $this->getLayout()->createBlock('adminhtml/' . $this->_controller . '_view_plane'));
        return parent::_prepareLayout();
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/*/edit', [$this->_objectId => $this->getRequest()->getParam($this->_objectId)]);
    }

    public function getViewHtml()
    {
        return $this->getChildHtml('plane');
    }
}
