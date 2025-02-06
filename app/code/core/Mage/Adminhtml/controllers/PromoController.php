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
class Mage_Adminhtml_PromoController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'promo';

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('promo');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Promotions'), Mage::helper('adminhtml')->__('Promo'));
        $this->renderLayout();
    }
}
