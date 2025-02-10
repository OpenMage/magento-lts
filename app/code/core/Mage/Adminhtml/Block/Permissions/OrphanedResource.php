<?php
/**
 * Adminhtml permissions orphaned resource block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
declare(strict_types=1);




class Mage_Adminhtml_Block_Permissions_OrphanedResource extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'permissions_orphanedResource';
        $this->_headerText = Mage::helper('adminhtml')->__('Orphaned Role Resources');
        parent::__construct();
        $this->_removeButton('add');
    }

    protected function _toHtml(): string
    {
        Mage::dispatchEvent('permissions_orphanedresource_html_before', ['block' => $this]);
        return parent::_toHtml();
    }
}
