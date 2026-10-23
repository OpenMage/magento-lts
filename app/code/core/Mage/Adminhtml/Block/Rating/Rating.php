<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Ratings grid
 *
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
