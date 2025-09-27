<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

include_once 'ProfileController.php';

/**
 * Convert GUI admin controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_System_Convert_GuiController extends Mage_Adminhtml_System_Convert_ProfileController
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'admin/system/convert/gui';

    /**
     * Profiles list action
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Import and Export'))
             ->_title($this->__('Profiles'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('system/convert/gui');

        /**
         * Append profiles block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_gui', 'convert_profile'),
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import/Export'), Mage::helper('adminhtml')->__('Import/Export'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Profiles'), Mage::helper('adminhtml')->__('Profiles'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/system_convert_gui_grid')->toHtml());
    }

    /**
     * Profile edit action
     */
    public function editAction()
    {
        $this->_initProfile();
        $this->loadLayout();

        $profile = Mage::registry('current_convert_profile');

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getConvertProfileData(true);

        if (!empty($data)) {
            $profile->addData($data);
        }

        $this->_title($profile->getId() ? $profile->getName() : $this->__('New Profile'));

        $this->_setActiveMenu('system/convert/gui');

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_gui_edit'),
        );

        /**
         * Append edit tabs to left block
         */
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/system_convert_gui_edit_tabs'));

        $this->renderLayout();
    }

    public function uploadAction()
    {
        $this->_initProfile();
        $profile = Mage::registry('current_convert_profile');
    }

    public function uploadPostAction()
    {
        $this->_initProfile();
        $profile = Mage::registry('current_convert_profile');
    }

    public function downloadAction()
    {
        $filename = $this->getRequest()->getParam('filename');
        if (!$filename || str_contains($filename, '..') || $filename[0] === '.') {
            return;
        }
        $this->_initProfile();
        $profile = Mage::registry('current_convert_profile');
    }
}
