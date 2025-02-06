<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Widget
 */

/**
 * Widget Instance grid container
 *
 * @category   Mage
 * @package    Mage_Widget
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'widget';
        $this->_controller = 'adminhtml_widget_instance';
        $this->_headerText = Mage::helper('widget')->__('Manage Widget Instances');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('widget')->__('Add New Widget Instance'));
    }
}
