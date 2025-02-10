<?php
/**
 * Catalog price rules
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Search extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'catalog_search';
        $this->_headerText = Mage::helper('catalog')->__('Search');
        $this->_addButtonLabel = Mage::helper('catalog')->__('Add New Search Term');
        parent::__construct();
    }
}
