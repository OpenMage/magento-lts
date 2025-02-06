<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Adminhtml sales orders block
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Instructions to create child grid
     *
     * @var string
     */
    protected $_blockGroup = 'sales';
    protected $_controller = 'adminhtml_recurring_profile';

    /**
     * Set header text and remove "add" btn
     */
    public function __construct()
    {
        $this->_headerText = Mage::helper('sales')->__('Recurring Profiles (beta)');
        parent::__construct();
        $this->_removeButton('add');
    }
}
