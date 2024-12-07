<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wysiwyg Images content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Wysiwyg_Images_Content extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Block construction
     */
    public function __construct()
    {
        parent::__construct();
        $this->_headerText = $this->helper('cms')->__('Media Storage');
        $this->_removeButton('back')->_removeButton('edit');
        $this->_addButton('newfolder', [
            'class'   => 'save',
            'label'   => $this->helper('cms')->__('Create Folder...'),
            'type'    => 'button',
            'onclick' => 'MediabrowserInstance.newFolder();',
        ]);

        $this->_addButton('delete_folder', [
            'class'   => 'delete no-display',
            'label'   => $this->helper('cms')->__('Delete Folder'),
            'type'    => 'button',
            'onclick' => 'MediabrowserInstance.deleteFolder();',
            'id'      => 'button_delete_folder',
        ]);

        $this->_addButton('delete_files', [
            'class'   => 'delete no-display',
            'label'   => $this->helper('cms')->__('Delete File'),
            'type'    => 'button',
            'onclick' => 'MediabrowserInstance.deleteFiles();',
            'id'      => 'button_delete_files',
        ]);

        $this->_addButton('insert_files', [
            'class'   => 'save no-display',
            'label'   => $this->helper('cms')->__('Insert File'),
            'type'    => 'button',
            'onclick' => 'MediabrowserInstance.insert();',
            'id'      => 'button_insert_files',
        ]);
    }

    /**
     * Files action source URL
     *
     * @return string
     */
    public function getContentsUrl()
    {
        return $this->getUrl('*/*/contents', ['type' => $this->getRequest()->getParam('type')]);
    }

    /**
     * Javascript setup object for filebrowser instance
     *
     * @return string
     */
    public function getFilebrowserSetupObject()
    {
        $setupObject = new Varien_Object();

        $setupObject->setData([
            'newFolderPrompt'                 => $this->helper('cms')->__('New Folder Name:'),
            'deleteFolderConfirmationMessage' => $this->helper('cms')->__('Are you sure you want to delete current folder?'),
            'deleteFileConfirmationMessage'   => $this->helper('cms')->__('Are you sure you want to delete the selected file?'),
            'targetElementId' => $this->getTargetElementId(),
            'contentsUrl'     => $this->getContentsUrl(),
            'onInsertUrl'     => $this->getOnInsertUrl(),
            'newFolderUrl'    => $this->getNewfolderUrl(),
            'deleteFolderUrl' => $this->getDeletefolderUrl(),
            'deleteFilesUrl'  => $this->getDeleteFilesUrl(),
            'headerText'      => $this->getHeaderText(),
        ]);

        return Mage::helper('core')->jsonEncode($setupObject);
    }

    /**
     * New directory action target URL
     *
     * @return string
     */
    public function getNewfolderUrl()
    {
        return $this->getUrl('*/*/newFolder');
    }

    /**
     * Delete directory action target URL
     *
     * @return string
     */
    protected function getDeletefolderUrl()
    {
        return $this->getUrl('*/*/deleteFolder');
    }

    /**
     * @return string
     */
    public function getDeleteFilesUrl()
    {
        return $this->getUrl('*/*/deleteFiles');
    }

    /**
     * New directory action target URL
     *
     * @return string
     */
    public function getOnInsertUrl()
    {
        return $this->getUrl('*/*/onInsert');
    }

    /**
     * Target element ID getter
     *
     * @return string
     */
    public function getTargetElementId()
    {
        return $this->getRequest()->getParam('target_element_id');
    }
}
