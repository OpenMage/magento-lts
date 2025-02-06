<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Index
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
