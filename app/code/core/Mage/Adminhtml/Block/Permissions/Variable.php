<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Variables block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Variable extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'permissions_variable';
        $this->_headerText = Mage::helper('adminhtml')->__('Variables');
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add new variable');
        parent::__construct();
    }

    /**
     * Prepare output HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('permissions_variable_html_before', ['block' => $this]);
        return parent::_toHtml();
    }
}
