<?php
/**
 * sales admin controller
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
