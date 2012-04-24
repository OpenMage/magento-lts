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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Manage currency block
 *
 * @category    Mage
 * @package     Mage_Compiler
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Compiler_Block_Process extends Mage_Adminhtml_Block_Template
{
    /**
     * Compilation process object
     *
     * @var Mage_Compiler_Model_Process
     */
    protected $_process;
    protected $_validationResult;


    protected function _construct()
    {
        $this->_process = Mage::getModel('compiler/process');
        $this->_validationResult = $this->_process->validate();
        return parent::_construct();
    }

    /**
     * Get compilation process object
     *
     * @return Mage_Compiler_Model_Process
     */
    public function getProcess()
    {
        return $this->_process;
    }

    protected function _prepareLayout()
    {
        $this->setChild('run_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('compiler')->__('Run Compilation Process'),
                    'onclick'   => 'compilationForm.submit();',
                    'class'     => 'save'
        )));

        if (defined('COMPILER_INCLUDE_PATH')) {
            $this->setChild('change_status_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('compiler')->__('Disable'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('*/compiler_process/disable') . '\')',
                        'class'     => 'save'
            )));
        } else {
            $this->setChild('change_status_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('compiler')->__('Enable'),
                        'onclick' => 'setLocation(\'' . $this->getUrl('*/compiler_process/enable') . '\')',
                        'class'     => 'save'
            )));
        }

        return parent::_prepareLayout();
    }

    /**
     * Get page header text
     *
     * @return string
     */
    protected function getHeader()
    {
        return Mage::helper('compiler')->__('Compilation');
    }

    public function getChangeStatusButtonHtml()
    {
        if ($this->getCollectedFilesCount()) {
            return $this->getChildHtml('change_status_button');
        }
        return '';
    }

    /**
     * Get html code of rum button
     *
     * @return string
     */
    protected function getRunButtonHtml()
    {
        return $this->getChildHtml('run_button');
    }

    /**
     * Get process run url
     *
     * @return string
     */
    public function getRunFormAction()
    {
        return $this->getUrl('*/compiler_process/recompile');
    }

    /**
     * Check if compilation proecss is allowed
     *
     * @return bool
     */
    public function canRunCompilation()
    {
        return empty($this->_validationResult);
    }

    /**
     * Get messages block
     *
     * @return Mage_Core_Block_Messages
     */
    public function getMessagesBlock()
    {
        $block = $this->getLayout()->createBlock('core/messages');
        foreach ($this->_validationResult as $message) {
            $block->addError($message);
        }
        return $block;
    }

    public function getCompilationList()
    {
        return $this->getProcess()->getCompileClassList();
    }

    public function arrToSting($arr)
    {
        return implode("\n", $arr);
    }

    public function getCompilerState()
    {
        if ($this->getCollectedFilesCount() > 0) {
            return $this->__('Compiled');
        } else {
            return $this->__('Not Compiled');
        }
    }

    public function getCompilerStatus()
    {
        if (defined('COMPILER_INCLUDE_PATH')) {
            return $this->__('Enabled');
        } else {
            return $this->__('Disabled');
        }
    }

    public function getCollectedFilesCount()
    {
        if (!$this->hasData('collected_files_count')) {
            $this->setData('collected_files_count', $this->getProcess()->getCollectedFilesCount());
        }
        return $this->_getData('collected_files_count');
    }

    public function getCompiledFilesCount()
    {
        if (!$this->hasData('compiled_files_count')) {
            $this->setData('compiled_files_count', $this->getProcess()->getCompiledFilesCount());
        }
        return $this->_getData('compiled_files_count');
    }
}
