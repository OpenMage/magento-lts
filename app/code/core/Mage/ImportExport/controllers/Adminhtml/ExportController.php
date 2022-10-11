<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export controller
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Adminhtml_ExportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    const ADMIN_RESOURCE = 'system/convert/export';

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
            ->_setActiveMenu('system/importexport');

        return $this;
    }

    /**
     * Load data with filter applying and create file for download.
     *
     * @return Mage_Adminhtml_Controller_Action
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
                    $model->getContentType()
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('No valid data sent'));
            }
        } else {
            $this->_getSession()->addError($this->__('No valid data sent'));
        }
        return $this->_redirect('*/*/index');
    }

    /**
     * Index action.
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
                        $export->setData($data)->getEntityAttributeCollection()
                    )
                );
                return $this->renderLayout();
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        } else {
            $this->_getSession()->addError($this->__('No valid data sent'));
        }
        $this->_redirect('*/*/index');
    }
}
