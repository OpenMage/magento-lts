<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Import controller
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/convert/import';

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
            ->_setActiveMenu('system/convert/import');

        return $this;
    }

    /**
     * Index action.
     */
    public function indexAction()
    {
        $maxUploadSize = Mage::helper('importexport')->getMaxUploadSize();
        $this->_getSession()->addNotice(
            $this->__('Total size of uploadable files must not exceed %s', $maxUploadSize),
        );
        $this->_initAction()
            ->_title($this->__('Import'))
            ->_addBreadcrumb($this->__('Import'), $this->__('Import'));

        $this->renderLayout();
    }

    /**
     * Start import process action.
     */
    public function startAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->loadLayout(false);

            /** @var Mage_ImportExport_Block_Adminhtml_Import_Frame_Result $resultBlock */
            $resultBlock = $this->getLayout()->getBlock('import.frame.result');
            /** @var Mage_ImportExport_Model_Import $importModel */
            $importModel = Mage::getModel('importexport/import');

            try {
                $importModel->importSource();
                $importModel->invalidateIndex();
                $resultBlock->addAction('show', 'import_validation_container')
                    ->addAction('innerHTML', 'import_validation_container_header', $this->__('Status'));
            } catch (Exception $e) {
                $resultBlock->addError($e->getMessage());
                $this->renderLayout();
                return;
            }

            $resultBlock->addAction('hide', ['edit_form', 'upload_button', 'messages'])
                ->addSuccess($this->__('Import successfully done.'));
            $this->renderLayout();
        } else {
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Validate uploaded files action.
     *
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function validateAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->loadLayout(false);
            /** @var Mage_ImportExport_Block_Adminhtml_Import_Frame_Result $resultBlock */
            $resultBlock = $this->getLayout()->getBlock('import.frame.result');
            // common actions
            $resultBlock->addAction('show', 'import_validation_container')
                ->addAction('clear', [
                    Mage_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE,
                    Mage_ImportExport_Model_Import::FIELD_NAME_IMG_ARCHIVE_FILE]);

            try {
                /** @var Mage_ImportExport_Model_Import $import */
                $import = Mage::getModel('importexport/import');
                $validationResult = $import->validateSource($import->setData($data)->uploadSource());

                if (!$import->getProcessedRowsCount()) {
                    $resultBlock->addError($this->__('File does not contain data. Please upload another one'));
                } else {
                    if (!$validationResult) {
                        if ($import->getProcessedRowsCount() == $import->getInvalidRowsCount()) {
                            $resultBlock->addNotice(
                                $this->__('File is totally invalid. Please fix errors and re-upload file'),
                            );
                        } elseif ($import->getErrorsCount() >= $import->getErrorsLimit()) {
                            $resultBlock->addNotice(
                                $this->__('Errors limit (%d) reached. Please fix errors and re-upload file', $import->getErrorsLimit()),
                            );
                        } elseif ($import->isImportAllowed()) {
                            $resultBlock->addNotice(
                                $this->__('Please fix errors and re-upload file or simply press "Import" button to skip rows with errors'),
                                true,
                            );
                        } else {
                            $resultBlock->addNotice(
                                $this->__('File is partially valid, but import is not possible'),
                                false,
                            );
                        }

                        // errors info
                        foreach ($import->getErrors() as $errorCode => $rows) {
                            $error = $errorCode . ' ' . $this->__('in rows:') . ' ' . implode(', ', $rows);
                            $resultBlock->addError($error);
                        }
                    } elseif ($import->isImportAllowed()) {
                        $resultBlock->addSuccess(
                            $this->__('File is valid! To start import process press "Import" button'),
                            true,
                        );
                    } else {
                        $resultBlock->addError(
                            $this->__('File is valid, but import is not possible'),
                        );
                    }

                    $resultBlock->addNotice($import->getNotices());
                    $resultBlock->addNotice($this->__('Checked rows: %d, checked entities: %d, invalid rows: %d, total errors: %d', $import->getProcessedRowsCount(), $import->getProcessedEntitiesCount(), $import->getInvalidRowsCount(), $import->getErrorsCount()));
                }
            } catch (Exception $e) {
                $resultBlock->addNotice($this->__('Please fix errors and re-upload file'))
                    ->addError($e->getMessage());
            }

            $this->renderLayout();
        } elseif ($this->getRequest()->isPost() && empty($_FILES)) {
            $this->loadLayout(false);
            /** @var Mage_ImportExport_Block_Adminhtml_Import_Frame_Result $resultBlock */
            $resultBlock = $this->getLayout()->getBlock('import.frame.result');
            $resultBlock->addError($this->__('File was not uploaded'));
            $this->renderLayout();
        } else {
            $this->_getSession()->addError($this->__('Data is invalid or file is not uploaded'));
            $this->_redirect('*/*/index');
        }
    }
}
