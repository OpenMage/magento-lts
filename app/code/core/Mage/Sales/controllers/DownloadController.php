<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales controller for download purposes
 *
 * @package    Mage_Sales
 */
class Mage_Sales_DownloadController extends Mage_Core_Controller_Front_Action
{
    /**
     * Custom options downloader
     *
     * @param mixed $info
     */
    protected function _downloadFileAction($info)
    {
        $secretKey = $this->getRequest()->getParam('key');
        try {
            if ($secretKey != $info['secret_key']) {
                throw new Exception();
            }

            $this->_validateFilePath($info);

            $filePath = Mage::getBaseDir() . $info['order_path'];
            if ((!is_file($filePath) || !is_readable($filePath)) && !$this->_processDatabaseFile($filePath)) {
                //try get file from quote
                $filePath = Mage::getBaseDir() . $info['quote_path'];
                if ((!is_file($filePath) || !is_readable($filePath)) && !$this->_processDatabaseFile($filePath)) {
                    throw new Exception();
                }
            }

            $this->_prepareDownloadResponse($info['title'], [
                'value' => $filePath,
                'type'  => 'filename',
            ]);
        } catch (Exception) {
            $this->_forward('noRoute');
        }
    }

    /**
     * @param array $info
     * @throws Exception
     */
    protected function _validateFilePath($info)
    {
        $optionFile = Mage::getModel('catalog/product_option_type_file');
        $optionStoragePath = $optionFile->getOrderTargetDir(true);
        if (!str_starts_with($info['order_path'], $optionStoragePath)) {
            throw new Exception('Unexpected file path');
        }
    }

    /**
     * Check file in database storage if needed and place it on file system
     *
     * @param string $filePath
     * @return bool
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    protected function _processDatabaseFile($filePath)
    {
        if (!Mage::helper('core/file_storage_database')->checkDbUsage()) {
            return false;
        }

        $relativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($filePath);
        $file = Mage::getModel('core/file_storage_database')->loadByFilename($relativePath);

        if (!$file->getId()) {
            return false;
        }

        $directory = dirname($filePath);
        @mkdir($directory, 0777, true);

        $io = new Varien_Io_File();
        $io->cd($directory);

        $io->streamOpen($filePath);
        $io->streamLock(true);
        $io->streamWrite($file->getContent());
        $io->streamUnlock();
        $io->streamClose();

        return true;
    }

    /**
     * Profile custom options download action
     */
    public function downloadProfileCustomOptionAction()
    {
        $recurringProfile = Mage::getModel('sales/recurring_profile')->load($this->getRequest()->getParam('id'));

        if (!$recurringProfile->getId()) {
            $this->_forward('noRoute');
        }

        $orderItemInfo = $recurringProfile->getData('order_item_info');
        try {
            $request = unserialize($orderItemInfo['info_buyRequest'], ['allowed_classes' => false]);

            if ($request['product'] != $orderItemInfo['product_id']) {
                $this->_forward('noRoute');
                return;
            }

            $optionId = $this->getRequest()->getParam('option_id');
            if (!isset($request['options'][$optionId])) {
                $this->_forward('noRoute');
                return;
            }

            // Check if the product exists
            $product = Mage::getModel('catalog/product')->load($request['product']);
            if (!$product || !$product->getId()) {
                $this->_forward('noRoute');
                return;
            }

            // Try to load the option
            $option = $product->getOptionById($optionId);
            if (!$option || !$option->getId() || $option->getType() != 'file') {
                $this->_forward('noRoute');
                return;
            }

            $this->_downloadFileAction($request['options'][$this->getRequest()->getParam('option_id')]);
        } catch (Exception) {
            $this->_forward('noRoute');
        }
    }

    /**
     * Custom options download action
     *
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    public function downloadCustomOptionAction()
    {
        $quoteItemOptionId = $this->getRequest()->getParam('id');
        /** @var Mage_Sales_Model_Quote_Item_Option $option */
        $option = Mage::getModel('sales/quote_item_option')->load($quoteItemOptionId);

        if (!$option->getId()) {
            $this->_forward('noRoute');
            return;
        }

        $optionId = null;
        if (str_starts_with($option->getCode(), Mage_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX)) {
            $optionId = str_replace(Mage_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX, '', $option->getCode());
            if (!is_numeric($optionId)) {
                $optionId = null;
            }
        }

        $productOption = null;
        if ($optionId) {
            /** @var Mage_Catalog_Model_Product_Option $productOption */
            $productOption = Mage::getModel('catalog/product_option')->load($optionId);
        }

        if (!$productOption || !$productOption->getId()
            || $productOption->getProductId() != $option->getProductId() || $productOption->getType() != 'file'
        ) {
            $this->_forward('noRoute');
            return;
        }

        try {
            $info = Mage::helper('core/unserializeArray')->unserialize($option->getValue());
            $this->_downloadFileAction($info);
        } catch (Exception) {
            $this->_forward('noRoute');
        }

        exit(0);
    }
}
