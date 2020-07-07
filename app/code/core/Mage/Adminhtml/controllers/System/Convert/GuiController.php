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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

include_once "ProfileController.php";

/**
 * Convert GUI admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_System_Convert_GuiController extends Mage_Adminhtml_System_Convert_ProfileController
{
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
        $this->_setActiveMenu('system/convert');

        /**
         * Append profiles block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_gui', 'convert_profile')
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

        $this->_setActiveMenu('system/convert');


        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_gui_edit')
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
        if (!$filename || strpos($filename, '..')!==false || $filename[0]==='.') {
            return;
        }
        $this->_initProfile();
        $profile = Mage::registry('current_convert_profile');
    }

    protected function _isAllowed()
    {
//        switch ($this->getRequest()->getActionName()) {
//            case 'index':
//                $aclResource = 'admin/system/convert/gui';
//                break;
//            case 'grid':
//                $aclResource = 'admin/system/convert/gui';
//                break;
//            case 'run':
//                $aclResource = 'admin/system/convert/gui/run';
//                break;
//            default:
//                $aclResource = 'admin/system/convert/gui/edit';
//                break;
//        }

        return Mage::getSingleton('admin/session')->isAllowed('admin/system/convert/gui');
    }
}
