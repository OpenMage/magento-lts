<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Export controller
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Adminhtml_ExportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/convert/export';

    /**
     * Custom constructor.
     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Mage_ImportExport');
    }

    /**
     * Initialize layout.
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_title($this->__('Import/Export'))
            ->loadLayout()
            ->_setActiveMenu('system/convert/export');

        return $this;
    }

    /**
     * Load data with filter applying and create file for download.
     *
     * @return void
     */
    public function exportAction()
    {
        if ($this->getRequest()->getPost(Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP)) {
            try {
                /** @var Mage_ImportExport_Model_Export $model */
                $model = Mage::getModel('importexport/export');
                $model->setData($this->getRequest()->getParams());

                $result         = $model->exportFile();
                $result['type'] = 'filename';

                return $this->_prepareDownloadResponse(
                    $model->getFileName(),
                    $result,
                    $model->getContentType(),
                );
            } catch (Mage_Core_Exception $mageCoreException) {
                $this->_getSession()->addError($mageCoreException->getMessage());
            } catch (Exception $exception) {
                Mage::logException($exception);
                $this->_getSession()->addError($this->__('No valid data sent'));
            }
        } else {
            $this->_getSession()->addError($this->__('No valid data sent'));
        }

        $this->_redirect('*/*/index');
        return;
    }

    /**
     * Index action.
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_title($this->__('Export'))
            ->_addBreadcrumb($this->__('Export'), $this->__('Export'));

        $this->renderLayout();
    }

    /**
     * Get grid-filter of entity attributes action.
     * @return void
     */
    public function getFilterAction()
    {
        $data = $this->getRequest()->getParams();
        if ($this->getRequest()->isXmlHttpRequest() && $data) {
            try {
                $this->loadLayout();

                /** @var Mage_ImportExport_Block_Adminhtml_Export_Filter $attrFilterBlock */
                $attrFilterBlock = $this->getLayout()->getBlock('export.filter');
                /** @var Mage_ImportExport_Model_Export $export */
                $export = Mage::getModel('importexport/export');

                $export->filterAttributeCollection(
                    $attrFilterBlock->prepareCollection(
                        $export->setData($data)->getEntityAttributeCollection(),
                    ),
                );
                return $this->renderLayout();
            } catch (Exception $exception) {
                $this->_getSession()->addError($exception->getMessage());
            }
        } else {
            $this->_getSession()->addError($this->__('No valid data sent'));
        }

        $this->_redirect('*/*/index');
    }
}
