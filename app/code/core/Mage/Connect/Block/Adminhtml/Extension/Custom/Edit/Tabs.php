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
 * @package     Mage_Connect
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for tabs in extension info
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Connect_Block_Adminhtml_Extension_Custom_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
    * Constructor
    */
    public function __construct()
    {
        parent::__construct();
        $this->setId('connect_extension_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('connect')->__('Create Extension Package'));
    }

    /**
    * Set tabs
    *
    * @return $this
    */
    protected function _beforeToHtml()
    {
//        $this->addTab('package', array(
//            'label'     => Mage::helper('connect')->__('Package Info'),
//            'content'   => $this->_getTabHtml('package'),
//            'active'    => true,
//        ));
//
//        $this->addTab('release', array(
//            'label'     => Mage::helper('connect')->__('Release Info'),
//            'content'   => $this->_getTabHtml('release'),
//        ));
//
//        $this->addTab('maintainers', array(
//            'label'     => Mage::helper('connect')->__('Authors'),
//            'content'   => $this->_getTabHtml('authors'),
//        ));
//
//        $this->addTab('depends', array(
//            'label'     => Mage::helper('connect')->__('Dependencies'),
//            'content'   => $this->_getTabHtml('depends'),
//        ));
//
//        $this->addTab('contents', array(
//            'label'     => Mage::helper('connect')->__('Contents'),
//            'content'   => $this->_getTabHtml('contents'),
//        ));
//
//        $this->addTab('load', array(
//            'label'     => Mage::helper('connect')->__('Load local Package'),
//            'class'     => 'ajax',
//            'url'       => $this->getUrl('*/*/loadtab', array('_current' => true)),
//        ));

        return parent::_beforeToHtml();
    }

    /**
    * Retrieve HTML for tab
    *
    * @param string $tab
    * @return string
    */
    protected function _getTabHtml($tab)
    {
//        return $this->getLayout()
//            ->createBlock('connect/adminhtml_extension_custom_edit_tab_'.$tab)
//            ->initForm()
//            ->toHtml();
    }

}
