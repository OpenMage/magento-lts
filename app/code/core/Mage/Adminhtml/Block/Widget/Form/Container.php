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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml form container block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Widget_Form_Container extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_objectId = 'id';
    protected $_formScripts = array();
    protected $_formInitScripts = array();
    protected $_mode = 'edit';
    protected $_blockGroup = 'adminhtml';

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('widget/form/container.phtml');

        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class'     => 'back',
            'level'     => -1
        ));
        $this->_addButton('reset', array(
            'label'     => Mage::helper('adminhtml')->__('Reset'),
            'onclick'   => 'setLocation(window.location.href)',
            'level'     => -1
        ));

        $objId = $this->getRequest()->getParam($this->_objectId);

        if (! empty($objId)) {
            $this->_addButton('delete', array(
                'label'     => Mage::helper('adminhtml')->__('Delete'),
                'class'     => 'delete',
                'onclick'   => 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                    .'\', \'' . $this->getDeleteUrl() . '\')',
            ));
        }

        $this->_addButton('save', array(
            'label'     => Mage::helper('adminhtml')->__('Save'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'save',
        ), -100);
    }

    protected function _prepareLayout()
    {
        $this->setChild('form', $this->getLayout()->createBlock($this->_blockGroup.'/' . $this->_controller . '_' . $this->_mode . '_form'));
        return parent::_prepareLayout();
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/'.$this->_controller.'/save');
    }

    public function getFormHtml()
    {
        $this->getChild('form')->setData('action', $this->getSaveUrl());
        return $this->getChildHtml('form');
    }

    public function getFormInitScripts()
    {
        if ( !empty($this->_formInitScripts) && is_array($this->_formInitScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formInitScripts) . '</script>';
        }
        return '';
    }

    public function getFormScripts()
    {
        if ( !empty($this->_formScripts) && is_array($this->_formScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formScripts) . '</script>';
        }
        return '';
    }

    public function getHeaderWidth()
    {
        return '';
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-' . strtr($this->_controller, '_', '-');
    }

    public function getHeaderHtml()
    {
        return '<h3 class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }

}
