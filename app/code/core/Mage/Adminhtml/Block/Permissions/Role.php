<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml permissioms role block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Role extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'permissions_role';
        $this->_headerText = Mage::helper('adminhtml')->__('Roles');
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add New Role');
        parent::__construct();
    }
}
