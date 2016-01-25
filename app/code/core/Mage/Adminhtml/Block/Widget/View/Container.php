<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml view container block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Widget_View_Container extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_objectId = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('widget/view/container.phtml');

        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'window.location.href=\'' . $this->getUrl('*/*/') . '\'',
            'class'     => 'back',
        ));

        $this->_addButton('edit', array(
            'label'     => Mage::helper('adminhtml')->__('Edit'),
            'class'     => 'edit',
            'onclick'   => 'window.location.href=\'' . $this->getEditUrl() . '\'',
        ));

    }

    protected function _prepareLayout()
    {
        $this->setChild('plane', $this->getLayout()->createBlock('adminhtml/' . $this->_controller . '_view_plane'));
        return parent::_prepareLayout();
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/*/edit', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    public function getViewHtml()
    {
        return $this->getChildHtml('plane');
    }

}
