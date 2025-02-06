<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Ratings grid
 *
 * @category   Mage
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
