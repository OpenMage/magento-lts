<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml cms pages content block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Page extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'cms_page';
        $this->_headerText = Mage::helper('cms')->__('Manage Pages');
        $this->_addButtonLabel = Mage::helper('cms')->__('Add New Page');
        parent::__construct();

        if (!$this->_isAllowedAction('save')) {
            $this->_removeButton(self::BUTTON_TYPE_ADD);
        }
    }

    /**
     * Check permission for passed action
     *
     * @param  string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}
