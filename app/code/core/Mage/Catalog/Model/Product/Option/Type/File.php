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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product option file type
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Option_Type_File extends Mage_Catalog_Model_Product_Option_Type_Default
{
    /**
     * Validate user input for option
     *
     * @throws Mage_Core_Exception
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return Mage_Catalog_Model_Product_Option_Type_Default
     */
    public function validateUserValue($values)
    {
        Mage::getSingleton('checkout/session')->setUseNotice(false);

        $this->setIsValid(true);
        $option = $this->getOption();

        // Set option value from request (Admin/Front reorders)
        if (isset($values[$option->getId()]) && is_array($values[$option->getId()])) {
            if (isset($values[$option->getId()]['order_path'])) {
                $orderFileFullPath = Mage::getBaseDir() . $values[$option->getId()]['order_path'];
            } else {
                $this->setUserValue(null);
                return $this;
            }

            $ok = is_file($orderFileFullPath) && is_readable($orderFileFullPath)
                && isset($values[$option->getId()]['secret_key'])
                && substr(md5(file_get_contents($orderFileFullPath)), 0, 20) == $values[$option->getId()]['secret_key'];

            $this->setUserValue($ok ? $values[$option->getId()] : null);
            return $this;
        } elseif ($this->getProduct()->getSkipCheckRequiredOption()) {
            $this->setUserValue(null);
            return $this;
        }

        /**
         * Upload init
         */
        $upload = new Zend_File_Transfer_Adapter_Http();
        $file = 'options_' . $option->getId() . '_file';

        try {
            $runValidation = $option->getIsRequire() || $upload->isUploaded($file);
            if (!$runValidation) {
                $this->setUserValue(null);
                return $this;
            }

            $fileInfo = $upload->getFileInfo($file);
            $fileInfo = $fileInfo[$file];

        } catch (Exception $e) {
            $this->setIsValid(false);
            Mage::throwException(Mage::helper('catalog')->__("Files upload failed"));
        }

        /**
         * Option Validations
         */

        // Image dimensions
        $_dimentions = array();
        if ($option->getImageSizeX() > 0) {
            $_dimentions['maxwidth'] = $option->getImageSizeX();
        }
        if ($option->getImageSizeY() > 0) {
            $_dimentions['maxheight'] = $option->getImageSizeY();
        }
        if (count($_dimentions) > 0) {
            $upload->addValidator('ImageSize', false, $_dimentions);
        }

        // File extension
        $_allowed = $this->_parseExtensionsString($option->getFileExtension());
        if ($_allowed !== null) {
            $upload->addValidator('Extension', false, $_allowed);
        } else {
            $_forbidden = $this->_parseExtensionsString($this->getConfigData('forbidden_extensions'));
            if ($_forbidden !== null) {
                $upload->addValidator('ExcludeExtension', false, $_forbidden);
            }
        }

        /**
         * Upload process
         */

        $this->_initFilesystem();

        if ($upload->isUploaded($file) && $upload->isValid($file)) {

            $extension = pathinfo(strtolower($fileInfo['name']), PATHINFO_EXTENSION);

            $fileName = Varien_File_Uploader::getCorrectFileName($fileInfo['name']);
            $dispersion = Varien_File_Uploader::getDispretionPath($fileName);

            $filePath = $dispersion;
            $destination = $this->getQuoteTargetDir() . $filePath;
            $this->_createWriteableDir($destination);
            $upload->setDestination($destination);

            $fileHash = md5(file_get_contents($fileInfo['tmp_name']));
            $filePath .= DS . $fileHash . '.' . $extension;

            $fileFullPath = $this->getQuoteTargetDir() . $filePath;

            $upload->addFilter('Rename', array(
                'target' => $fileFullPath,
                'overwrite' => true
            ));
            if (!$upload->receive()) {
                $this->setIsValid(false);
                Mage::throwException(Mage::helper('catalog')->__("File upload failed"));
            }

            $_imageSize = @getimagesize($fileFullPath);
            if (is_array($_imageSize) && count($_imageSize) > 0) {
                $_width = $_imageSize[0];
                $_height = $_imageSize[1];
            } else {
                $_width = 0;
                $_height = 0;
            }

            $this->setUserValue(array(
                'type'          => $fileInfo['type'],
                'title'         => $fileInfo['name'],
                'quote_path'    => $this->getQuoteTargetDir(true) . $filePath,
                'order_path'    => $this->getOrderTargetDir(true) . $filePath,
                'fullpath'      => $fileFullPath,
                'size'          => $fileInfo['size'],
                'width'         => $_width,
                'height'        => $_height,
                'secret_key'    => substr($fileHash, 0, 20)
            ));

        } elseif ($upload->getErrors()) {
            $errors = array();
            foreach ($upload->getErrors() as $errorCode) {
                if ($errorCode == Zend_Validate_File_ExcludeExtension::FALSE_EXTENSION) {
                    $errors[] = Mage::helper('catalog')->__("The file '%s' for '%s' has an invalid extension",
                        $fileInfo['name'],
                        $option->getTitle()
                    );
                } elseif ($errorCode == Zend_Validate_File_Extension::FALSE_EXTENSION) {
                    $errors[] = Mage::helper('catalog')->__("The file '%s' for '%s' has an invalid extension",
                        $fileInfo['name'],
                        $option->getTitle()
                    );
                } elseif ($errorCode == Zend_Validate_File_ImageSize::WIDTH_TOO_BIG
                    || $errorCode == Zend_Validate_File_ImageSize::WIDTH_TOO_BIG)
                {
                    $errors[] = Mage::helper('catalog')->__("Maximum allowed image size for '%s' is %sx%s px.",
                        $option->getTitle(),
                        $option->getImageSizeX(),
                        $option->getImageSizeY()
                    );
                }
            }
            if (count($errors) > 0) {
                $this->setIsValid(false);
                Mage::throwException( implode("\n", $errors) );
            }
        } else {
            $this->setIsValid(false);
            Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option(s)'));
        }
        return $this;
    }

    /**
     * Prepare option value for cart
     *
     * @return mixed Prepared option value
     */
    public function prepareForCart()
    {
        if ($this->getIsValid() && $this->getUserValue() !== null) {
            $value = $this->getUserValue();
            // Save option in request, because we have no $_FILES['options']
            $requestOptions = $this->getRequest()->getOptions();
            $requestOptions[$this->getOption()->getId()] = $value;
            $this->getRequest()->setOptions($requestOptions);
            return serialize($value);
        } else {
            return null;
        }
    }

    /**
     * Return formatted option value for quote option
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getFormattedOptionValue($optionValue)
    {
        try {
            $value = unserialize($optionValue);
            if ($value !== false) {
                if ($value['width'] > 0 && $value['height'] > 0) {
                    $sizes = $value['width'] . ' x ' . $value['height'] . ' ' . Mage::helper('catalog')->__('px.');
                } else {
                    $sizes = '';
                }
                $result = sprintf('<a href="%s" target="_blank">%s</a> %s',
                    $this->_getOptionDownloadUrl($value['secret_key']),
                    Mage::helper('core')->htmlEscape($value['title']),
                    $sizes
                );
                return $result;
            }

            throw new Exception();

        } catch (Exception $e) {
            return $optionValue;
        }
    }

    /**
     * Return formatted option value ready to edit, ready to parse
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getEditableOptionValue($optionValue)
    {
        try {
            $value = unserialize($optionValue);
            if ($value !== false) {
                $result = sprintf('%s [%d]',
                    Mage::helper('core')->htmlEscape($value['title']),
                    $this->getQuoteItemOption()->getId()
                );
                return $result;
            }

            throw new Exception();

        } catch (Exception $e) {
            return $optionValue;
        }
    }

    /**
     * Parse user input value and return cart prepared value
     *
     * @param string $optionValue
     * @param array $productOptionValues Values for product option
     * @return string|null
     */
    public function parseOptionValue($optionValue, $productOptionValues)
    {
        // search quote item option Id in option value
        if (preg_match('/\[([0-9]+)\]/', $optionValue, $matches)) {
            $quoteItemOptionId = $matches[1];
            $option = Mage::getModel('sales/quote_item_option')->load($quoteItemOptionId);
            try {
                unserialize($option->getValue());
                return $option->getValue();
            } catch (Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Prepare option value for info buy request
     *
     * @param string $optionValue
     * @return mixed
     */
    public function prepareOptionValueForRequest($optionValue)
    {
        try {
            $result = unserialize($optionValue);
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Quote item to order item copy process
     *
     * @return Mage_Catalog_Model_Product_Option_Type_File
     */
    public function copyQuoteToOrder()
    {
        $quoteOption = $this->getQuoteItemOption();
        try {
            $value = unserialize($quoteOption->getValue());
            if (!isset($value['quote_path'])) {
                throw new Exception();
            }
            $quoteFileFullPath = Mage::getBaseDir() . $value['quote_path'];
            if (!is_file($quoteFileFullPath) || !is_readable($quoteFileFullPath)) {
                throw new Exception();
            }
            $orderFileFullPath = Mage::getBaseDir() . $value['order_path'];
            $dir = pathinfo($orderFileFullPath, PATHINFO_DIRNAME);
            $this->_createWriteableDir($dir);
            @copy($quoteFileFullPath, $orderFileFullPath);
        } catch (Exception $e) {
            return $this;
        }
        return $this;
    }

    /**
     * Main Destination directory
     *
     * @param boolean $relative If true - returns relative path to the webroot
     * @return string
     */
    public function getTargetDir($relative = false)
    {
        $fullPath = Mage::getBaseDir('media') . DS . 'custom_options';
        return $relative ? str_replace(Mage::getBaseDir(), '', $fullPath) : $fullPath;
    }

    /**
     * Quote items destination directory
     *
     * @param boolean $relative If true - returns relative path to the webroot
     * @return string
     */
    public function getQuoteTargetDir($relative = false)
    {
        return $this->getTargetDir($relative) . DS . 'quote';
    }

    /**
     * Order items destination directory
     *
     * @param boolean $relative If true - returns relative path to the webroot
     * @return string
     */
    public function getOrderTargetDir($relative = false)
    {
        return $this->getTargetDir($relative) . DS . 'order';
    }

    /**
     * Directory structure initializing
     */
    protected function _initFilesystem()
    {
        $this->_createWriteableDir($this->getTargetDir());
        $this->_createWriteableDir($this->getQuoteTargetDir());
        $this->_createWriteableDir($this->getOrderTargetDir());

        // Directory listing and hotlink secure
        $io = new Varien_Io_File();
        $io->cd($this->getTargetDir());
        if (!$io->fileExists($this->getTargetDir() . DS . '.htaccess')) {
            $io->streamOpen($this->getTargetDir() . DS . '.htaccess');
            $io->streamLock(true);
            $io->streamWrite("Order deny,allow\nDeny from all");
            $io->streamUnlock();
            $io->streamClose();
        }
    }

    /**
     * Create Writeable directory if it doesn't exist
     *
     * @param string Absolute directory path
     * @return void
     */
    protected function _createWriteableDir($path)
    {
        $io = new Varien_Io_File();
        if (!$io->isWriteable($path) && !$io->mkdir($path, 0777, true)) {
            Mage::throwException(Mage::helper('catalog')->__("Cannot create writeable directory '%s'", $path));
        }
    }

    /**
     * Return URL for option file download
     *
     * @return string
     */
    protected function _getOptionDownloadUrl($sekretKey)
    {
        return Mage::getUrl('sales/download/downloadCustomOption', array(
            'id'  => $this->getQuoteItemOption()->getId(),
            'key' => $sekretKey
        ));
    }

    /**
     * Parse file extensions string with various separators
     *
     * @param string $extensions String to parse
     * @return array|null
     */
    protected function _parseExtensionsString($extensions)
    {
        preg_match_all('/[a-z]+/si', strtolower($extensions), $matches);
        if (isset($matches[0]) && is_array($matches[0]) && count($matches[0]) > 0) {
            return $matches[0];
        }
        return null;
    }
}