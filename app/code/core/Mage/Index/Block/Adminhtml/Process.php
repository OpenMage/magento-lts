<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Index
 */
class Mage_Index_Block_Adminhtml_Process extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'index';
        $this->_controller = 'adminhtml_process';
        $this->_headerText = Mage::helper('index')->__('Index Management');
        parent::__construct();
        $this->_removeButton('add');
    }
}
