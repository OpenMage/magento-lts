<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend grid controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_GridController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'catalog/products';

    /**
     * Save grid columns order action
     */
    public function saveColumnOrderAction()
    {
        $gridId = $this->getRequest()->getPost('gridId');
        $data = $this->getRequest()->getPost('data');
        $reset = $this->getRequest()->getPost('reset');
        if (!$gridId || (!$data && !$reset)) {
            return false;
        }

        /** @var Mage_Adminhtml_Helper_Widget_Grid_Config $_advancedGridHelper  */
        $_advancedGridHelper = Mage::helper('adminhtml/widget_grid_config');
        $_advancedGridHelper
            ->setGridId($gridId)
            ->saveOrderColumns($data);
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed(static::ADMIN_RESOURCE);
    }
}
