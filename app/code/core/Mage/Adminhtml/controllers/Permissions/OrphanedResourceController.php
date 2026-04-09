<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Permissions_OrphanedResourceController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/acl/orphaned_resources';

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

    /**
     * Index action
     * @return void
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
     * @return void
     */
    public function massDeleteAction()
    {
        $resourceIds = $this->getRequest()->getParam('resource_id');
        try {
            $deletedRows = Mage::getResourceSingleton('admin/rules')->deleteOrphanedResources($resourceIds);
            $this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', $deletedRows));
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getSession()->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            $error = Mage::getIsDeveloperMode()
                ? $exception->getMessage()
                : $this->__('An error occurred while deleting record(s).');
            $this->_getSession()->addError($error);
            Mage::logException($exception);
        }

        $this->_redirect('*/*/');
    }

    /**
     * @inheritDoc
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions('massDelete');
        return parent::preDispatch();
    }
}
