<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer attribute sets controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Customer_SetController extends Mage_Eav_Controller_Adminhtml_Set_Abstract
{
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->_entityCode = Mage_Customer_Model_Customer::ENTITY;
    }

    protected function _initAction()
    {
        parent::_initAction();

        $this->_title($this->__('Customers'))
             ->_title($this->__('Attributes'))
             ->_title($this->__('Manage Customer Attribute Sets'));

        $this->_setActiveMenu('customer/attributes')
             ->_addBreadcrumb(
                 $this->__('Customers'),
                 $this->__('Customers')
             )
             ->_addBreadcrumb(
                 $this->__('Manage Customer Attribute Sets'),
                 $this->__('Manage Customer Attribute Sets')
             );

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/attributes/customer_sets');
    }
}
