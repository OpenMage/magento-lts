<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/**
 * @package    Mage_Index
 */
class Mage_Index_Block_Adminhtml_Process_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Mage_Index_Block_Adminhtml_Process_Edit_Tabs constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('process_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('index')->__('Index'));
    }
}
