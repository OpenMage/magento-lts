<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * Adminhtml store edit
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Store_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $backupAvailable
            = Mage::getSingleton('admin/session')->isAllowed('system/tools/backup')
            && $this->isModuleEnabled('Mage_Backup')
            && !Mage::getStoreConfigFlag('advanced/modules_disable_output/Mage_Backup');

        $saveLabel      = '';
        $deleteLabel    = '';
        $deleteUrl      = '';

        switch (Mage::registry('store_type')) {
            case 'website':
                $this->_objectId = 'website_id';
                $saveLabel   = Mage::helper('core')->__('Save Website');
                $deleteLabel = Mage::helper('core')->__('Delete Website');
                $deleteUrl   = $this->_getDeleteUrl(Mage::registry('store_type'), $backupAvailable);
                break;
            case 'group':
                $this->_objectId = 'group_id';
                $saveLabel   = Mage::helper('core')->__('Save Store');
                $deleteLabel = Mage::helper('core')->__('Delete Store');
                $deleteUrl   = $this->_getDeleteUrl(Mage::registry('store_type'), $backupAvailable);
                break;
            case 'store':
                $this->_objectId = 'store_id';
                $saveLabel   = Mage::helper('core')->__('Save Store View');
                $deleteLabel = Mage::helper('core')->__('Delete Store View');
                $deleteUrl   = $this->_getDeleteUrl(Mage::registry('store_type'), $backupAvailable);
                break;
        }

        $this->_controller = 'system_store';

        parent::__construct();

        $this->_updateButton('save', 'label', $saveLabel);
        $this->_updateButton('delete', 'label', $deleteLabel);
        $this->_updateButton('delete', 'onclick', Mage::helper('core/js')->getConfirmSetLocationJs($deleteUrl));

        if (!Mage::registry('store_data')->isCanDelete()) {
            $this->_removeButton('delete');
        }

        if (Mage::registry('store_data')->isReadOnly()) {
            $this->_removeButton('save')->_removeButton('reset');
        }
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $addLabel   = '';
        $editLabel  = '';

        switch (Mage::registry('store_type')) {
            case 'website':
                $editLabel = Mage::helper('core')->__('Edit Website');
                $addLabel  = Mage::helper('core')->__('New Website');
                break;
            case 'group':
                $editLabel = Mage::helper('core')->__('Edit Store');
                $addLabel  = Mage::helper('core')->__('New Store');
                break;
            case 'store':
                $editLabel = Mage::helper('core')->__('Edit Store View');
                $addLabel  = Mage::helper('core')->__('New Store View');
                break;
        }

        return Mage::registry('store_action') == 'add' ? $addLabel : $editLabel;
    }

    /**
     * Create URL depending on backups
     *
     * @param string $storeType
     * @param bool $backupAvailable
     * @return string
     */
    public function _getDeleteUrl($storeType, $backupAvailable = false)
    {
        $storeType = uc_words($storeType);
        if ($backupAvailable) {
            $deleteUrl   = $this->getUrl('*/*/delete' . $storeType, ['item_id' => Mage::registry('store_data')->getId()]);
        } else {
            $deleteUrl   = $this->getUrl(
                '*/*/delete' . $storeType . 'Post',
                [
                    'item_id' => Mage::registry('store_data')->getId(),
                    'form_key' => Mage::getSingleton('core/session')->getFormKey(),
                ],
            );
        }

        return $deleteUrl;
    }
}
