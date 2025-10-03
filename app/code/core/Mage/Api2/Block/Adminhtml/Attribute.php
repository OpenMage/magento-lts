<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 attributes grid container block
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Block_Adminhtml_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'api2';
        $this->_controller = 'adminhtml_attribute';
        $this->_headerText = $this->__('REST Attributes');
        $this->_removeButton('add');
    }
}
