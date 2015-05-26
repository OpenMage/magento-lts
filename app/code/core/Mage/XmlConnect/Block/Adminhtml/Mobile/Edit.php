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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Application edit block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Setting app action buttons for application
     */
    public function __construct()
    {
        $this->_objectId    = 'application_id';
        $this->_controller  = 'adminhtml_mobile';
        $this->_blockGroup  = 'xmlconnect';
        parent::__construct();
        if ((bool)!Mage::getSingleton('adminhtml/session')->getNewApplication()) {
            $app = Mage::helper('xmlconnect')->getApplication();
            $this->_updateButton('save', 'label', $this->__('Save'));
            $this->_updateButton('save', 'onclick', 'if (editForm.submit()) {disableElements(\'save\')}');

            $this->_addButton('save_and_continue', array(
                'label'     => $this->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save',
            ), -5);

            if ($app->getId()) {
                $this->_addButton('submit_application_button', array(
                    'label' =>  $this->__('Save and Submit App'),
                    'onclick'    => 'saveAndSubmitApp()',
                    'class'     => 'save'
                ), -10);
            }

            $this->_formScripts[] = 'function saveAndContinueEdit() {'
                .'if (editForm.submit($(\'edit_form\').action + \'back/edit/\')) {disableElements(\'save\')};}';
            if ($app->getId()) {
                $this->_formScripts[] = 'function saveAndSubmitApp() {'
                    .'if (editForm.submit($(\'edit_form\').action + \'submitapp/' . $app->getId() . '\')) {'
                    .'disableElements(\'save\')};}';
            }
        } else {
            $this->removeButton('save');
            $this->removeButton('delete');
        }

        if (isset($app) && $app->getIsSubmitted()) {
            $this->removeButton('delete');
        }
        $this->removeButton('reset');
    }

    /**
     * Adding JS scripts and styles to block
     *
     * @throws Mage_Core_Exception
     * @return Mage_Adminhtml_Block_Widget_Form_Container
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addJs('jscolor/jscolor.js');
        $this->getLayout()->getBlock('head')->addJs('scriptaculous/scriptaculous.js');

        if ((bool)!Mage::getSingleton('adminhtml/session')->getNewApplication()) {
            $deviceType = Mage::helper('xmlconnect')->getDeviceType();
            switch ($deviceType) {
                case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE:
                    $this->getLayout()->getBlock('head')->addItem('skin_css', 'xmlconnect/mobile-home.css');
                    $this->getLayout()->getBlock('head')->addItem('skin_css', 'xmlconnect/mobile-catalog.css');
                    break;
                case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD:
                    $this->getLayout()->getBlock('head')->addItem('skin_css', 'xmlconnect/mobile-ipad-home.css');
                    $this->getLayout()->getBlock('head')->addItem('skin_css', 'xmlconnect/mobile-ipad-catalog.css');
                    break;
                case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID:
                    $this->getLayout()->getBlock('head')->addItem('skin_css', 'xmlconnect/mobile-android.css');
                    break;
                default:
                    Mage::throwException(
                        $this->__('Device doesn\'t recognized: "%s". Unable to load preview model.', $deviceType)
                    );
                    break;
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Get form header title
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ((bool)!Mage::getSingleton('adminhtml/session')->getNewApplication()) {
            $app = Mage::helper('xmlconnect')->getApplication();
        }

        if (isset($app) && $app->getId()) {
            return $this->__('Edit App "%s"', $this->escapeHtml($app->getName()));
        } else {
            return $this->__('New App');
        }
    }
}
