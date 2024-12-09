<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml permissions role block
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_Block_Adminhtml_Role extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'api_role';
        $this->_headerText = Mage::helper('adminhtml')->__('Roles');
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add New Role');
        parent::__construct();
    }
}
