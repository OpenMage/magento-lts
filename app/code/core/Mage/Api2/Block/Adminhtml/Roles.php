<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Roles grid container block
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Block_Adminhtml_Roles extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'api2';
        $this->_controller = 'adminhtml_roles';
        $this->_headerText = Mage::helper('adminhtml')->__('REST Roles');

        //check allow edit
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        if ($session->isAllowed('system/api/roles/add')) {
            $this->_updateButton('add', 'label', $this->__('Add Admin Role'));
        } else {
            $this->_removeButton('add');
        }
    }
}
