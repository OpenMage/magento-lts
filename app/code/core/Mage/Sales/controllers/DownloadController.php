<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales controller for download purposes
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
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
            $this->_prepareDownloadResponse($info['title'], array(
               'value' => $filePath,
               'type'  => 'filename'
            ));
        } catch (Exception $e) {
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
        if (strpos($info['order_path'], $optionStoragePath) !== 0) {
            throw new Exception('Unexpected file path');
        }
    }

    /**
     * Check file in database storage if needed and place it on file system
     *
     * @param string $filePath
     * @return bool
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
            $request = unserialize($orderItemInfo['info_buyRequest']);

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
        } catch (Exception $e) {
            $this->_forward('noRoute');
        }
    }

    /**
     * Custom options download action
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
        if (strpos($option->getCode(), Mage_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX) === 0) {
            $optionId = str_replace(Mage_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX, '', $option->getCode());
            if ((int)$optionId != $optionId) {
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
        } catch (Exception $e) {
            $this->_forward('noRoute');
        }
        exit(0);
    }
}
