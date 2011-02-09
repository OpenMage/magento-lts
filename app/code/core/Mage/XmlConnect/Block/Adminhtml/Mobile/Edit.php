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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $app = $this->getApplication();

        if ((bool)!Mage::getSingleton('adminhtml/session')->getNewApplication()) {
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
                    .'if (editForm.submit($(\'edit_form\').action+\'submitapp/' . $app->getId() . '\')) {'
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
     * Retrieve currently edited application object
     *
     * @return Mage_XmlConnect_Model_Application
     */
    public function getApplication()
    {
        return Mage::registry('current_app');
    }

    /**
     * Adding JS scripts to block
     *
     * @return Mage_Adminhtml_Block_Widget_Form_Container
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addJs('jscolor/jscolor.js');
        $this->getLayout()->getBlock('head')->addJs('scriptaculous/scriptaculous.js');
        return parent::_prepareLayout();
    }

    /**
     * Get form header title
     *
     * @return string
     */
    public function getHeaderText()
    {
        $app = $this->getApplication();
        if ($app && $app->getId()) {
            return $this->__('Edit App "%s"', $this->htmlEscape($app->getName()));
        } else {
            return $this->__('New App');
        }
    }
}
