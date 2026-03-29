<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml cms blocks content block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Block extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'cms_block';
        $this->_headerText = Mage::helper('cms')->__('Static Blocks');
        $this->_addButtonLabel = Mage::helper('cms')->__('Add New Block');
        parent::__construct();

        if (!$this->_isAllowedAction('save')) {
            $this->_removeButton('add');
        }
    }

    /**
     * Check permission for passed action
     */
    protected function _isAllowedAction(string $action): bool
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/block/' . $action);
    }
}
