<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product option file type
 *
 * @package    Mage_Catalog
 *
 * @method array getCustomOptionUrlParams()
 */
class Mage_Catalog_Model_Product_Option_Type_File extends Mage_Catalog_Model_Product_Option_Type_Default
{
    public const ERROR_EXCLUDE_EXTENSION_FALSE_EXTENSION    = 'fileExcludeExtensionFalse';

    public const ERROR_EXTENSION_FALSE_EXTENSION            = 'fileExtensionFalse';

    public const ERROR_IMAGESIZE_WIDTH_TOO_BIG              = 'fileImageSizeWidthTooBig';

    public const ERROR_IMAGESIZE_HEIGHT_TOO_BIG             = 'fileImageSizeHeightTooBig';

    public const ERROR_FILESIZE_TOO_BIG                     = 'fileFilesSizeTooBig';

    /**
     * Url for custom option download controller
     * @var string
     */
    protected $_customOptionDownloadUrl = 'sales/download/downloadCustomOption';

    /**
     * @return bool
     */
    public function isCustomizedView()
    {
        return true;
    }

    /**
     * Return option html
     *
     * @param  array  $optionInfo
     * @return string
     */
    public function getCustomizedView($optionInfo)
    {
        try {
            if (isset($optionInfo['option_value'])) {
                return $this->_getOptionHtml($optionInfo['option_value']);
            }

            if (isset($optionInfo['value'])) {
                return $optionInfo['value'];
            }
        } catch (Exception) {
            return $optionInfo['value'];
        }

        return '';
    }

    /**
     * Returns additional params for processing options
     *
     * @return Varien_Object
     */
    protected function _getProcessingParams()
    {
        $buyRequest = $this->getRequest();
        $params = $buyRequest->getData('_processing_params');
        /*
         * Notice check for params to be Varien_Object - by using object we protect from
         * params being forged and contain data from user frontend input
         */
        if ($params instanceof Varien_Object) {
            return $params;
        }

        return new Varien_Object();
    }

    /**
     * Returns file info array if we need to get file from already existing file.
     * Or returns null, if we need to get file from uploaded array.
     *
     * @return null|array
     * @throws Mage_Core_Exception
     */
    protected function _getCurrentConfigFileInfo()
    {
        $option = $this->getOption();
        $optionId = $option->getId();
        $processingParams = $this->_getProcessingParams();
        $buyRequest = $this->getRequest();

        // Check maybe restore file from config requested
        $optionActionKey = 'options_' . $optionId . '_file_action';
        if ($buyRequest->getData($optionActionKey) === 'save_old') {
            $fileInfo = [];
            $currentConfig = $processingParams->getCurrentConfig();
            if ($currentConfig) {
                return $currentConfig->getData('options/' . $optionId);
            }

            return $fileInfo;
        }

        return null;
    }

    /**
     * Validate user input for option
     *
     * @param  array               $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function validateUserValue($values)
    {
        Mage::getSingleton('checkout/session')->setUseNotice(false);

        $this->setIsValid(true);

        /*
         * Check whether we receive uploaded file or restore file by: reorder/edit configuration or
         * previous configuration with no newly uploaded file
         */

        $fileInfo = $this->_getCurrentConfigFileInfo();

        if ($fileInfo !== null) {
            if (is_array($fileInfo) && $this->_validateFile($fileInfo)) {
                $value = $fileInfo;
            } else {
                $value = null;
            }

            $this->setUserValue($value);
            return $this;
        }

        // Process new uploaded file
        try {
            $this->_validateUploadedFile();
        } catch (Exception $exception) {
            if ($this->getSkipCheckRequiredOption()) {
                $this->setUserValue(null);
                return $this;
            }

            Mage::throwException($exception->getMessage());
        }

        return $this;
    }

    /**
     * Validate uploaded file
     *
     * @return $this
     * @throws Mage_Core_Exception|Zend_File_Transfer_Exception
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    protected function _validateUploadedFile()
    {
        $option = $this->getOption();
        $processingParams = $this->_getProcessingParams();

        /**
         * Upload init
         */
        $upload   = new Zend_File_Transfer_Adapter_Http();
        $file = $processingParams->getFilesPrefix() . 'options_' . $option->getId() . '_file';
        try {
            $runValidation = $option->getIsRequire() || $upload->isUploaded($file);
            if (!$runValidation) {
                $this->setUserValue(null);
                return $this;
            }

            $fileInfo = $upload->getFileInfo($file);
            $fileInfo = $fileInfo[$file];
            $fileInfo['title'] = $fileInfo['name'];
        } catch (Exception) {
            // when file exceeds the upload_max_filesize, $_FILES is empty
            if (isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > $this->_getUploadMaxFilesize()) {
                $this->setIsValid(false);
                $value = $this->_bytesToMbytes($this->_getUploadMaxFilesize());
                Mage::throwException(
                    Mage::helper('catalog')->__('The file you uploaded is larger than %s Megabytes allowed by server', $value),
                );
            } else {
                switch ($this->getProcessMode()) {
                    case Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL:
                        Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option <em>%s</em>.', $option->getTitle()));
                        // exception thrown
                        // no break
                    default:
                        $this->setUserValue(null);
                        break;
                }

                return $this;
            }
        }

        /**
         * Option Validations
         */

        // Image dimensions
        $_dimentions = [];
        if ($option->getImageSizeX() > 0) {
            $_dimentions['maxwidth'] = $option->getImageSizeX();
        }

        if ($option->getImageSizeY() > 0) {
            $_dimentions['maxheight'] = $option->getImageSizeY();
        }

        if ($_dimentions !== []) {
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

        // Maximum filesize
        $upload->addValidator('FilesSize', false, ['max' => $this->_getUploadMaxFilesize()]);

        /**
         * Upload process
         */

        $this->_initFilesystem();

        if ($upload->isUploaded($file) && $upload->isValid($file)) {
            $extension = pathinfo(strtolower($fileInfo['name']), PATHINFO_EXTENSION);

            $fileName = Mage_Core_Model_File_Uploader::getCorrectFileName($fileInfo['name']);
            $dispersion = Mage_Core_Model_File_Uploader::getDispretionPath($fileName);

            $filePath = $dispersion;
            $fileHash = md5(file_get_contents($fileInfo['tmp_name']));
            $filePath .= DS . $fileHash . '.' . $extension;
            $fileFullPath = $this->getQuoteTargetDir() . $filePath;

            $upload->addFilter('Rename', [
                'target' => $fileFullPath,
                'overwrite' => true,
            ]);

            $this->getProduct()->getTypeInstance(true)->addFileQueue([
                'operation' => 'receive_uploaded_file',
                'src_name'  => $file,
                'dst_name'  => $fileFullPath,
                'uploader'  => $upload,
                'option'    => $this,
            ]);

            $_width = 0;
            $_height = 0;
            if (is_readable($fileInfo['tmp_name'])) {
                $_imageSize = getimagesize($fileInfo['tmp_name']);
                if ($_imageSize) {
                    $_width = $_imageSize[0];
                    $_height = $_imageSize[1];
                }
            }

            $this->setUserValue([
                'type'          => $fileInfo['type'],
                'title'         => $fileInfo['name'],
                'quote_path'    => $this->getQuoteTargetDir(true) . $filePath,
                'order_path'    => $this->getOrderTargetDir(true) . $filePath,
                'fullpath'      => $fileFullPath,
                'size'          => $fileInfo['size'],
                'width'         => $_width,
                'height'        => $_height,
                'secret_key'    => substr($fileHash, 0, 20),
            ]);
        } elseif ($upload->getErrors()) {
            $errors = $this->_getValidatorErrors($upload->getErrors(), $fileInfo);

            if (count($errors) > 0) {
                $this->setIsValid(false);
                Mage::throwException(implode("\n", $errors));
            }
        } else {
            $this->setIsValid(false);
            Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option <em>%s</em>.', $option->getTitle()));
        }

        return $this;
    }

    /**
     * Validate file
     *
     * @param  array               $optionValue
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateFile($optionValue)
    {
        $option = $this->getOption();
        /**
         * @see Mage_Catalog_Model_Product_Option_Type_File::_validateUploadFile()
         *              There setUserValue() sets correct fileFullPath only for
         *              quote_path. So we must form both full paths manually and
         *              check them.
         */
        $checkPaths = [];
        if (isset($optionValue['quote_path'])) {
            $checkPaths[] = Mage::getBaseDir() . $optionValue['quote_path'];
        }

        if (isset($optionValue['order_path']) && !$this->getUseQuotePath()) {
            $checkPaths[] = Mage::getBaseDir() . $optionValue['order_path'];
        }

        $fileFullPath = null;
        foreach ($checkPaths as $path) {
            if (!is_file($path)) {
                if (!Mage::helper('core/file_storage_database')->saveFileToFilesystem($fileFullPath)) {
                    continue;
                }
            }

            $fileFullPath = $path;
            break;
        }

        if ($fileFullPath === null) {
            return false;
        }

        /** @var Mage_Core_Helper_Validate $validator */
        $validator = Mage::helper('core/validate');
        $validatorChain = new ArrayObject();

        $_dimentions = [];

        if ($option->getImageSizeX() > 0) {
            $_dimentions['maxwidth'] = $option->getImageSizeX();
        }

        if ($option->getImageSizeY() > 0) {
            $_dimentions['maxheight'] = $option->getImageSizeY();
        }

        if ($_dimentions !== [] && !$this->_isImage($fileFullPath)) {
            return false;
        }

        if ($_dimentions !== []) {
            $validatorChain->append($validator->validateImage(
                value: $fileFullPath,
                maxWidth: $_dimentions['maxwidth'] ?? null,
                maxHeight: $_dimentions['maxheight'] ?? null,
                maxWidthMessage: Mage::helper('catalog')->__(
                    $this->getValidatorMessage(self::ERROR_IMAGESIZE_WIDTH_TOO_BIG),
                    $option->getTitle(),
                    $option->getImageSizeX(),
                    $option->getImageSizeY(),
                ),
                maxHeightMessage: Mage::helper('catalog')->__(
                    $this->getValidatorMessage(self::ERROR_IMAGESIZE_HEIGHT_TOO_BIG),
                    $option->getTitle(),
                    $option->getImageSizeX(),
                    $option->getImageSizeY(),
                ),
            ));
        }

        // Maximum filesize
        // File extension
        $_allowed = $this->_parseExtensionsString($option->getFileExtension());
        if ($_allowed !== null) {
            $validatorChain->append($validator->validateFile(
                value: $fileFullPath,
                maxSize: $this->_getUploadMaxFilesize(),
                maxSizeMessage: Mage::helper('catalog')->__(
                    "The file '%s' you uploaded is larger than %s Megabytes allowed by server",
                    $optionValue['title'],
                    $this->_bytesToMbytes($this->_getUploadMaxFilesize()),
                ),
                extensions: $_allowed,
                extensionsMessage: Mage::helper('catalog')->__(
                    $this->getValidatorMessage(self::ERROR_EXTENSION_FALSE_EXTENSION),
                    $optionValue['title'],
                    $option->getTitle(),
                ),
            ));
        } else {
            $_forbidden = $this->_parseExtensionsString($this->getConfigData('forbidden_extensions'));
            if ($_forbidden !== null) {
                $validatorChain->append($validator->validateChoice(
                    value: $_allowed,
                    choices: $_forbidden,
                    multiple: true,
                    message: Mage::helper('catalog')->__(
                        $this->getValidatorMessage(self::ERROR_EXTENSION_FALSE_EXTENSION),
                        $optionValue['title'],
                        $option->getTitle(),
                    ),
                    match: false,
                ));
            }
        }

        $errors = $validator->getErrorMessages($validatorChain);
        if (!$errors) {
            return is_readable($fileFullPath)
                && isset($optionValue['secret_key'])
                && substr(md5(file_get_contents($fileFullPath)), 0, 20) == $optionValue['secret_key'];
        }

        $this->setIsValid(false);
        Mage::throwException(implode("\n", iterator_to_array($errors)));
    }

    /**
     * Get Error messages for validator Errors
     * @param  array               $errors   Array of validation failure message codes
     * @param  array               $fileInfo File info
     * @return array               Array of error messages
     * @throws Mage_Core_Exception
     */
    protected function _getValidatorErrors($errors, $fileInfo)
    {
        $option = $this->getOption();
        $result = [];
        foreach ($errors as $errorCode) {
            if (in_array($errorCode, [self::ERROR_EXCLUDE_EXTENSION_FALSE_EXTENSION, self::ERROR_EXTENSION_FALSE_EXTENSION])) {
                $result[] = Mage::helper('catalog')->__(
                    $this->getValidatorMessage($errorCode),
                    $fileInfo['title'],
                    $option->getTitle(),
                );
            } elseif (in_array($errorCode, [self::ERROR_IMAGESIZE_HEIGHT_TOO_BIG, self::ERROR_IMAGESIZE_WIDTH_TOO_BIG])) {
                $result[] = Mage::helper('catalog')->__(
                    $this->getValidatorMessage($errorCode),
                    $option->getTitle(),
                    $option->getImageSizeX(),
                    $option->getImageSizeY(),
                );
            } elseif ($errorCode === self::ERROR_FILESIZE_TOO_BIG) {
                $result[] = Mage::helper('catalog')->__(
                    $this->getValidatorMessage($errorCode),
                    $fileInfo['title'],
                    $this->_bytesToMbytes($this->_getUploadMaxFilesize()),
                );
            }
        }

        return $result;
    }

    /**
     * @param self::ERROR_* $errorCode
     */
    protected function getValidatorMessage(string $errorCode): string
    {
        $messages = [
            self::ERROR_EXCLUDE_EXTENSION_FALSE_EXTENSION   => "The file '%s' for '%s' has an invalid extension",
            self::ERROR_EXTENSION_FALSE_EXTENSION           => "The file '%s' for '%s' has an invalid extension",
            self::ERROR_FILESIZE_TOO_BIG                    => "The file '%s' you uploaded is larger than %s Megabytes allowed by server",
            self::ERROR_IMAGESIZE_HEIGHT_TOO_BIG            => "Maximum allowed image size for '%s' is %sx%s px.",
            self::ERROR_IMAGESIZE_WIDTH_TOO_BIG             => "Maximum allowed image size for '%s' is %sx%s px.",
        ];

        return $messages[$errorCode] ?? '';
    }

    /**
     * Prepare option value for cart
     *
     * @return null|string         Prepared option value
     * @throws Mage_Core_Exception
     */
    public function prepareForCart()
    {
        $option = $this->getOption();
        $optionId = $option->getId();
        $buyRequest = $this->getRequest();

        // Prepare value and fill buyRequest with option
        $requestOptions = $buyRequest->getOptions();
        if ($this->getIsValid() && $this->getUserValue() !== null) {
            $value = $this->getUserValue();

            // Save option in request, because we have no $_FILES['options']
            $requestOptions[$this->getOption()->getId()] = $value;
            $result = serialize($value);
            try {
                Mage::helper('core/unserializeArray')->unserialize($result);
            } catch (Exception) {
                Mage::throwException(Mage::helper('catalog')->__('File options format is not valid.'));
            }
        } else {
            /*
             * Clear option info from request, so it won't be stored in our db upon
             * unsuccessful validation. Otherwise some bad file data can happen in buyRequest
             * and be used later in reorders and reconfigurations.
             */
            if (is_array($requestOptions)) {
                unset($requestOptions[$this->getOption()->getId()]);
            }

            $result = null;
        }

        $buyRequest->setOptions($requestOptions);

        // Clear action key from buy request - we won't need it anymore
        $optionActionKey = 'options_' . $optionId . '_file_action';
        $buyRequest->unsetData($optionActionKey);

        return $result;
    }

    /**
     * Return formatted option value for quote option
     *
     * @param  string $optionValue Prepared for cart option value
     * @return string
     */
    public function getFormattedOptionValue($optionValue)
    {
        if ($this->_formattedOptionValue === null) {
            try {
                $value = Mage::helper('core/unserializeArray')->unserialize($optionValue);

                $customOptionUrlParams = $this->getCustomOptionUrlParams() ?: [
                    'id'  => $this->getConfigurationItemOption()->getId(),
                    'key' => $value['secret_key'],
                ];

                $value['url'] = ['route' => $this->_customOptionDownloadUrl, 'params' => $customOptionUrlParams];

                $this->_formattedOptionValue = $this->_getOptionHtml($value);
                $this->getConfigurationItemOption()->setValue(serialize($value));
                return $this->_formattedOptionValue;
            } catch (Exception) {
                return $optionValue;
            }
        }

        return $this->_formattedOptionValue;
    }

    /**
     * Format File option html
     *
     * @param  array|string        $optionValue Serialized string of option data or its data array
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function _getOptionHtml($optionValue)
    {
        $value = $this->_unserializeValue($optionValue);
        try {
            if (isset($value) && isset($value['width']) && isset($value['height'])
                && $value['width'] > 0 && $value['height'] > 0
            ) {
                $sizes = $value['width'] . ' x ' . $value['height'] . ' ' . Mage::helper('catalog')->__('px.');
            } else {
                $sizes = '';
            }

            $urlRoute = empty($value['url']['route']) ? '' : $value['url']['route'];
            $urlParams = empty($value['url']['params']) ? '' : $value['url']['params'];
            $title = empty($value['title']) ? '' : $value['title'];

            return sprintf(
                '<a href="%s" target="_blank">%s</a> %s',
                $this->_getOptionDownloadUrl($urlRoute, $urlParams),
                Mage::helper('core')->escapeHtml($title),
                $sizes,
            );
        } catch (Exception) {
            Mage::throwException(Mage::helper('catalog')->__('File options format is not valid.'));
        }
    }

    /**
     * Create a value from a storable representation
     *
     * @param  mixed     $value
     * @return array
     * @throws Exception
     */
    protected function _unserializeValue($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && !empty($value)) {
            return Mage::helper('core/unserializeArray')->unserialize($value);
        }

        return [];
    }

    /**
     * Return printable option value
     *
     * @param  string $optionValue Prepared for cart option value
     * @return string
     */
    public function getPrintableOptionValue($optionValue)
    {
        $value = $this->getFormattedOptionValue($optionValue);
        return $value === null ? '' : strip_tags($value);
    }

    /**
     * Return formatted option value ready to edit, ready to parse
     *
     * @param  string $optionValue Prepared for cart option value
     * @return string
     */
    public function getEditableOptionValue($optionValue)
    {
        try {
            $value = Mage::helper('core/unserializeArray')->unserialize($optionValue);
            return sprintf(
                '%s [%d]',
                Mage::helper('core')->escapeHtml($value['title']),
                $this->getConfigurationItemOption()->getId(),
            );
        } catch (Exception) {
            return $optionValue;
        }
    }

    /**
     * Parse user input value and return cart prepared value
     *
     * @param  string      $optionValue
     * @param  array       $productOptionValues Values for product option
     * @return null|string
     */
    public function parseOptionValue($optionValue, $productOptionValues)
    {
        // search quote item option Id in option value
        if (preg_match('/\[(\d+)\]/', $optionValue, $matches)) {
            $confItemOptionId = $matches[1];
            $option = Mage::getModel('sales/quote_item_option')->load($confItemOptionId);
            try {
                return $option->getValue();
            } catch (Exception) {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Prepare option value for info buy request
     *
     * @param  string $optionValue
     * @return mixed
     */
    public function prepareOptionValueForRequest($optionValue)
    {
        try {
            return Mage::helper('core/unserializeArray')->unserialize($optionValue);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Quote item to order item copy process
     *
     * @return $this
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function copyQuoteToOrder()
    {
        $quoteOption = $this->getQuoteItemOption();
        try {
            $value = Mage::helper('core/unserializeArray')->unserialize($quoteOption->getValue());
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
            Mage::helper('core/file_storage_database')->copyFile($quoteFileFullPath, $orderFileFullPath);
            @copy($quoteFileFullPath, $orderFileFullPath);
        } catch (Exception) {
            return $this;
        }

        return $this;
    }

    /**
     * Main Destination directory
     *
     * @param  bool   $relative If true - returns relative path to the webroot
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
     * @param  bool   $relative If true - returns relative path to the webroot
     * @return string
     */
    public function getQuoteTargetDir($relative = false)
    {
        return $this->getTargetDir($relative) . DS . 'quote';
    }

    /**
     * Order items destination directory
     *
     * @param  bool   $relative If true - returns relative path to the webroot
     * @return string
     */
    public function getOrderTargetDir($relative = false)
    {
        return $this->getTargetDir($relative) . DS . 'order';
    }

    /**
     * Set url to custom option download controller
     *
     * @param  string $url
     * @return $this
     */
    public function setCustomOptionDownloadUrl($url)
    {
        $this->_customOptionDownloadUrl = $url;
        return $this;
    }

    /**
     * Directory structure initializing
     * @throws Mage_Core_Exception
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
     * @param  string              $path Absolute directory path
     * @throws Mage_Core_Exception
     */
    protected function _createWriteableDir($path)
    {
        $io = new Varien_Io_File();
        if (!$io->isWriteable($path) && !$io->mkdir($path, 0777, true)) {
            Mage::throwException(Mage::helper('catalog')->__("Cannot create writeable directory '%s'.", $path));
        }
    }

    /**
     * Return URL for option file download
     *
     * @param  string                          $route
     * @param  array                           $params
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getOptionDownloadUrl($route, $params)
    {
        if (empty($params['_store']) && Mage::app()->getStore()->isAdmin()) {
            $order = Mage::registry('current_order');
            if (is_object($order)) {
                $params['_store'] = Mage::app()->getStore($order->getStoreId())->getCode();
            } else {
                $params['_store'] = Mage::app()->getDefaultStoreView()->getCode();
            }
        }

        return Mage::getUrl($route, $params);
    }

    /**
     * Parse file extensions string with various separators
     *
     * @param  string     $extensions String to parse
     * @return null|array
     */
    protected function _parseExtensionsString($extensions)
    {
        preg_match_all('/[a-z0-9]+/si', strtolower($extensions), $matches);
        if ($matches[0] !== []) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Simple check if file is image
     *
     * @param  array|string $fileInfo - either file data from Zend_File_Transfer or file path
     * @return bool
     */
    protected function _isImage($fileInfo)
    {
        // Maybe array with file info came in
        if (is_array($fileInfo)) {
            return strstr($fileInfo['type'], 'image/');
        }

        // File path came in - check the physical file
        if (!is_readable($fileInfo)) {
            return false;
        }

        $imageInfo = getimagesize($fileInfo);
        if (!$imageInfo) {
            return false;
        }

        return true;
    }

    /**
     * Max upload filesize in bytes
     *
     * @return int
     */
    protected function _getUploadMaxFilesize()
    {
        return min($this->_getBytesIniValue('upload_max_filesize'), $this->_getBytesIniValue('post_max_size'));
    }

    /**
     * Return php.ini setting value in bytes
     *
     * @param  string $option php.ini Var name
     * @return int    Setting value
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    protected function _getBytesIniValue($option)
    {
        $_bytes = @ini_get($option);

        return ini_parse_quantity($_bytes);
    }

    /**
     * Simple convert bytes to Megabytes
     *
     * @param  int   $bytes
     * @return float
     */
    protected function _bytesToMbytes($bytes)
    {
        return round($bytes / (1024 * 1024));
    }
}
