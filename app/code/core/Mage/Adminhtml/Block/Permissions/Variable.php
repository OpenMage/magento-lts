<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Variables block
 *
 * @category   Mage
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
