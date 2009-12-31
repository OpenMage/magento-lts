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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wysiwyg Images content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Cms_Wysiwyg_Images_Content extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Block construction
     */
    public function __construct()
    {
        parent::__construct();
        $this->_headerText = $this->helper('cms')->__('Image Gallery');
        $this->_removeButton('back')->_removeButton('edit');
        $this->_addButton('newfolder', array(
            'class'   => 'save',
            'label'   => $this->helper('cms')->__('Create Folder...'),
            'type'    => 'button',
            'onclick' => 'Images.newFolder();'
        ));
        $this->_addButton('upload', array(
            'class'   => 'add',
            'label'   => $this->helper('cms')->__('Upload Files...'),
            'type'    => 'button',
            'onclick' => 'Images.upload();'
        ));

        $this->_addButton('delete_folder', array(
            'class'   => 'delete no-display',
            'label'   => $this->helper('cms')->__('Delete Folder'),
            'type'    => 'button',
            'onclick' => 'Images.deleteFolder();',
            'id'      => 'button_delete_folder'
        ));

        $this->_addButton('delete_files', array(
            'class'   => 'delete no-display',
            'label'   => $this->helper('cms')->__('Delete File'),
            'type'    => 'button',
            'onclick' => 'Images.deleteFiles();',
            'id'      => 'button_delete_files'
        ));

        $this->_addButton('insert_files', array(
            'class'   => 'save no-display',
            'label'   => $this->helper('cms')->__('Insert File'),
            'type'    => 'button',
            'onclick' => 'Images.insert();',
            'id'      => 'button_insert_files'
        ));
    }

    /**
     * Files action source URL
     *
     * @return string
     */
    public function getContentsUrl()
    {
        return $this->getUrl('*/*/contents', array('type' => $this->getRequest()->getParam('type')));
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
     * Description goes here...
     *
     * @param none
     * @return void
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
}
