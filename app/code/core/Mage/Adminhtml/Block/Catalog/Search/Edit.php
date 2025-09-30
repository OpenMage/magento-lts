<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Admin tag edit block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Search_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'catalog_search';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('catalog')->__('Save Search'));
        $this->_updateButton('delete', 'label', Mage::helper('catalog')->__('Delete Search'));
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_catalog_search')->getId()) {
            return Mage::helper('catalog')->__("Edit Search '%s'", $this->escapeHtml(Mage::registry('current_catalog_search')->getQueryText()));
        }
        return Mage::helper('catalog')->__('New Search');
    }
}
