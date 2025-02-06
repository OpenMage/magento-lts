<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * config controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Customer_ConfigController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'customer/config';

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('customer/config');
        $this->_addBreadcrumb(Mage::helper('customer')->__('Customer'), Mage::helper('customer')->__('Customer'));
        $this->_addBreadcrumb(Mage::helper('customer')->__('Config'), Mage::helper('customer')->__('Config'));
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/customer_config'),
        );
        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/customer_config_tabs'));

        $this->renderLayout();
    }
}
