<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Adminhtml_Permissions_OrphanedResourceController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/acl/orphaned_resources';

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
            ->_title($this->__('Permissions'))
            ->_title($this->__('Orphaned Role Resources'));

        /** @var Mage_Adminhtml_Block_Permissions_OrphanedResource $block */
        $block = $this->getLayout()->createBlock('adminhtml/permissions_orphanedResource');
        $this->_initAction()
            ->_addContent($block)
            ->renderLayout();
    }

    /**
     * Mass delete action
     */
    public function massDeleteAction()
    {
        $resourceIds = $this->getRequest()->getParam('resource_id');
        try {
            $deletedRows = Mage::getResourceSingleton('admin/rules')->deleteOrphanedResources($resourceIds);
            $this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', $deletedRows));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $error = Mage::getIsDeveloperMode()
                ? $e->getMessage()
                : $this->__('An error occurred while deleting record(s).');
            $this->_getSession()->addError($error);
            Mage::logException($e);
        }

        $this->_redirect('*/*/');
    }

    /**
     * @inheritdoc
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions('massDelete');
        return parent::preDispatch();
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/acl/orphaned_resources')
            ->_addBreadcrumb($this->__('System'), $this->__('System'))
            ->_addBreadcrumb($this->__('Permissions'), $this->__('Permissions'))
            ->_addBreadcrumb($this->__('Orphaned Resources'), $this->__('Orphaned Role Resources'));
        return $this;
    }
}
