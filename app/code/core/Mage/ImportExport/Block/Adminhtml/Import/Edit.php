<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Import edit block
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Block_Adminhtml_Import_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->removeButton('back')
            ->removeButton('reset')
            ->_updateButton('save', 'label', $this->__('Check Data'))
            ->_updateButton('save', 'id', 'upload_button')
            ->_updateButton('save', 'onclick', 'editForm.postToFrame();');
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId   = 'import_id';
        $this->_blockGroup = 'importexport';
        $this->_controller = 'adminhtml_import';
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('importexport')->__('Import');
    }
}
