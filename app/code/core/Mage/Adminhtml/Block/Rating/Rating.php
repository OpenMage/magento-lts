<?php
/**
 * Ratings grid
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Rating_Rating extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'rating';
        $this->_headerText = Mage::helper('rating')->__('Manage Ratings');
        $this->_addButtonLabel = Mage::helper('rating')->__('Add New Rating');
        parent::__construct();
    }
}
