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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert IO adapter
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Convert_Adapter_Io extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    /**
     * @return Varien_Io_Abstract
     */
    public function getResource($forWrite = false)
    {
        if (!$this->_resource) {
            $type = $this->getVar('type', 'file');
            $className = 'Varien_Io_'.ucwords($type);
            $this->_resource = new $className();

            $isError = false;

            $ioConfig = $this->getVars();
            switch ($this->getVar('type', 'file')) {
                case 'file':
                    if (preg_match('#^'.preg_quote(DS, '#').'#', $this->getVar('path')) ||
                        preg_match('#^[a-z]:'.preg_quote(DS, '#') .'#i', $this->getVar('path'))) {

                        $path = $this->_resource->getCleanPath($this->getVar('path'));
                    }
                    else {
                        $baseDir = Mage::getBaseDir();
                        $path = $this->_resource->getCleanPath($baseDir . DS . trim($this->getVar('path'), DS));
                    }

                    $this->_resource->checkAndCreateFolder($path);

                    $realPath = realpath($path);

                    if (!$isError && $realPath === false) {
                        $message = Mage::helper('dataflow')->__('The destination folder "%s" does not exist or there is no access to create it.', $ioConfig['path']);
                        Mage::throwException($message);
                    }
                    elseif (!$isError && !is_dir($realPath)) {
                        $message = Mage::helper('dataflow')->__('Destination folder "%s" is not a directory.', $realPath);
                        Mage::throwException($message);
                    }
                    elseif (!$isError) {
                        if ($forWrite && !is_writeable($realPath)) {
                            $message = Mage::helper('dataflow')->__('Destination folder "%s" is not writable.', $realPath);
                            Mage::throwException($message);
                        }
                        else {
                            $ioConfig['path'] = rtrim($realPath, DS);
                        }
                    }

//                    $baseDir = Mage::getBaseDir();
//                    $path = $this->_resource->getCleanPath($baseDir . '/' . trim($this->getVar('path'), '/'));
//                    $basePath = $this->_resource->getCleanPath($baseDir);
//
//                    if (strpos($path, $basePath) !== 0) {
//                        $message = Mage::helper('dataflow')->__('Access denied to destination folder "%s".', $path);
//                        Mage::throwException($message);
//                    } else {
//                        $this->_resource->checkAndCreateFolder($path);
//                    }
//
//                    $realPath = realpath($path);
//                    if (!$isError && $realPath === false) {
//                        $message = Mage::helper('dataflow')->__('Destination folder "%s" does not exist or there is no access to create it.', $ioConfig['path']);
//                        Mage::throwException($message);
//                    }
//                    elseif (!$isError && !is_dir($realPath)) {
//                        $message = Mage::helper('dataflow')->__('Destination folder "%s" is not a directory.', $realPath);
//                        Mage::throwException($message);
//                    }
//                    elseif (!$isError) {
//                        if ($forWrite && !is_writeable($realPath)) {
//                            $message = Mage::helper('dataflow')->__('Destination folder "%s" is not writable.', $realPath);
//                            Mage::throwException($message);
//                        }
//                        else {
//                            $ioConfig['path'] = rtrim($realPath, '/');
//                        }
//                    }
                    break;
                default:
                    $ioConfig['path'] = rtrim($this->getVar('path'), '/');
                    break;
            }

            if ($isError) {
                return false;
            }
            try {
                $this->_resource->open($ioConfig);
            } catch (Exception $e) {
                $message = Mage::helper('dataflow')->__('An error occurred while opening file: "%s".', $e->getMessage());
                Mage::throwException($message);
            }
        }
        return $this->_resource;
    }

    /**
     * Load data
     *
     * @return Mage_Dataflow_Model_Convert_Adapter_Io
     */
    public function load()
    {
        if (!$this->getResource()) {
            return $this;
        }

        $batchModel = Mage::getSingleton('dataflow/batch');
        $destFile = $batchModel->getIoAdapter()->getFile(true);

        $result = $this->getResource()->read($this->getVar('filename'), $destFile);
        $filename = $this->getResource()->pwd() . '/' . $this->getVar('filename');
        if (false === $result) {
            $message = Mage::helper('dataflow')->__('Could not load file: "%s".', $filename);
            Mage::throwException($message);
        } else {
            $message = Mage::helper('dataflow')->__('Loaded successfully: "%s".', $filename);
            $this->addException($message);
        }

        $this->setData($result);
        return $this;
    }

    /**
     * Save result to destionation file from temporary
     *
     * @return Mage_Dataflow_Model_Convert_Adapter_Io
     */
    public function save()
    {
        if (!$this->getResource(true)) {
            return $this;
        }

        $batchModel = Mage::getSingleton('dataflow/batch');

        $dataFile = $batchModel->getIoAdapter()->getFile(true);

        $filename = $this->getVar('filename');

        $result   = $this->getResource()->write($filename, $dataFile, 0777);

        if (false === $result) {
            $message = Mage::helper('dataflow')->__('Could not save file: %s.', $filename);
            Mage::throwException($message);
        } else {
            $message = Mage::helper('dataflow')->__('Saved successfully: "%s" [%d byte(s)].', $filename, $batchModel->getIoAdapter()->getFileSize());
            if ($this->getVar('link')) {
                $message .= Mage::helper('dataflow')->__('<a href="%s" target="_blank">Link</a>', $this->getVar('link'));
            }
            $this->addException($message);
        }
        return $this;
    }
}
