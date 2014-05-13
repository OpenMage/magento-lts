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
 * @package     Mage_Compiler
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Compiler process controller
 *
 * @category    Mage
 * @package     Mage_Compiler
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Compiler_Adminhtml_Compiler_ProcessController extends Mage_Adminhtml_Controller_Action
{
    protected $_compiler = null;

    public function preDispatch()
    {
        parent::preDispatch();
    }

    /**
     * Get compiler process object
     *
     * @return Mage_Compiler_Model_Process
     */
    protected function _getCompiler()
    {
        if ($this->_compiler === null) {
            $this->_compiler = Mage::getModel('compiler/process');
        }
        return $this->_compiler;
    }
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Tools'))->_title($this->__('Compilation'));

        $this->loadLayout();
        $this->_setActiveMenu('system/tools');
        $this->renderLayout();
    }

    public function runAction()
    {
        try {
            $this->_getCompiler()->run();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('compiler')->__('The compilation has completed.')
            );
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('compiler')->__('Compilation error')
            );
        }
        $this->_redirect('*/*/');
    }

    public function recompileAction()
    {
        /**
         * Add redirect heades before clear compiled sources
         */
        $this->_redirect('*/*/run');
        $this->_getCompiler()->clear();
        $this->getResponse()->sendHeaders();
        exit;
    }

    public function disableAction()
    {
        $this->_getCompiler()->registerIncludePath(false);
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('compiler')->__('Compiler include path is disabled.')
        );
        $this->_redirect('*/*/');
    }

    public function enableAction()
    {
        $this->_getCompiler()->registerIncludePath();
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('compiler')->__('Compiler include path is enabled.')
        );
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/tools/compiler');
    }
}
