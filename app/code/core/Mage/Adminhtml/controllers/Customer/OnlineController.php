<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Customer_OnlineController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'customer/online';

    public function indexAction()
    {
        $this->_title($this->__('Customers'))->_title($this->__('Online Customers'));

        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();

        $this->_setActiveMenu('customer/online');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/customer_online', 'customers'));

        $this->_addBreadcrumb(Mage::helper('customer')->__('Customers'), Mage::helper('customer')->__('Customers'));
        $this->_addBreadcrumb(Mage::helper('customer')->__('Online Customers'), Mage::helper('customer')->__('Online Customers'));

        $this->renderLayout();
    }
}
