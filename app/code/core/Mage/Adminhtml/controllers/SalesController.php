<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * sales admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_SalesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'sales';

    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('sales');

        $block = $this->getLayout()->createBlock('adminhtml/sales', 'sales');
        $this->_addContent($block);

        $this->_addBreadcrumb($this->__('Sales'), $this->__('Sales'));
        $this->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        $this->renderLayout();
    }
}
