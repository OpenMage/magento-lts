<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Uploader
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Uploader Config Instance Abstract Model
 *
 * @category   Mage
 * @package    Mage_Uploader
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method $this setTarget(string $url)
 *      The target URL for the multipart POST request.
 * @method $this setSingleFile(bool $isSingleFile)
 *      Enable single file upload.
 *      Once one file is uploaded, second file will overtake existing one, first one will be canceled.
 * @method $this setChunkSize(int $chunkSize) The size in bytes of each uploaded chunk of data.
 * @method $this setForceChunkSize(bool $forceChunkSize)
 *      Force all chunks to be less or equal than chunkSize.
 * @method $this setSimultaneousUploads(int $amountOfSimultaneousUploads)
 * @method $this setFileParameterName(string $fileUploadParam)
 * @method $this setQuery(array $additionalQuery)
 * @method $this setHeaders(array $headers)
 *      Extra headers to include in the multipart POST with data.
 * @method $this setWithCredentials(bool $isCORS)
 *      Standard CORS requests do not send or set any cookies by default.
 *      In order to include cookies as part of the request, you need to set the withCredentials property to true.
 * @method $this setMethod(string $sendMethod)
 *       Method to use when POSTing chunks to the server. Defaults to "multipart"
 * @method $this setTestMethod(string $testMethod) Defaults to "GET"
 * @method $this setUploadMethod(string $uploadMethod) Defaults to "POST"
 * @method $this setAllowDuplicateUploads(bool $allowDuplicateUploads)
 *      Once a file is uploaded, allow reupload of the same file. By default, if a file is already uploaded,
 *      it will be skipped unless the file is removed from the existing Flow object.
 * @method $this setPrioritizeFirstAndLastChunk(bool $prioritizeFirstAndLastChunk)
 *      This can be handy if you can determine if a file is valid for your service from only the first or last chunk.
 * @method $this setTestChunks(bool $prioritizeFirstAndLastChunk)
 *      Make a GET request to the server for each chunks to see if it already exists.
 * @method $this setPreprocess(bool $prioritizeFirstAndLastChunk)
 *      Optional function to process each chunk before testing & sending.
 * @method $this setInitFileFn(string $function)
 *      Optional function to initialize the fileObject (js).
 * @method $this setReadFileFn(string $function)
 *      Optional function wrapping reading operation from the original file.
 * @method $this setGenerateUniqueIdentifier(string $function)
 *      Override the function that generates unique identifiers for each file. Defaults to "null"
 * @method $this setMaxChunkRetries(int $maxChunkRetries) Defaults to 0
 * @method $this setChunkRetryInterval(int $chunkRetryInterval) Defaults to "undefined"
 * @method $this setProgressCallbacksInterval(int $progressCallbacksInterval)
 * @method $this setSpeedSmoothingFactor(int $speedSmoothingFactor)
 *      Used for calculating average upload speed. Number from 1 to 0.
 *      Set to 1 and average upload speed wil be equal to current upload speed.
 *      For longer file uploads it is better set this number to 0.02,
 *      because time remaining estimation will be more accurate.
 * @method $this setSuccessStatuses(array $successStatuses)
 *      Response is success if response status is in this list
 * @method $this setPermanentErrors(array $permanentErrors)
 *      Response fails if response status is in this list
 */
class Mage_Uploader_Model_Config_Uploader extends Mage_Uploader_Model_Config_Abstract
{
    /**
     * Type of upload
     */
    public const UPLOAD_TYPE = 'multipart';

    /**
     * Test chunks on resumable uploads
     */
    public const TEST_CHUNKS = false;

    /**
     * Used for calculating average upload speed.
     */
    public const SMOOTH_UPLOAD_FACTOR = 0.02;

    /**
     * Progress check interval
     */
    public const PROGRESS_CALLBACK_INTERVAL = 0;

    /**
     * Set default values for uploader
     */
    protected function _construct()
    {
        // Fix error where setting post_max_size or upload_max_filesize to 0
        // causes the flow.js to make infinite chunks and crash the browser
        $maxSize = $this->_getHelper()->getDataMaxSizeInBytes();

        if ($maxSize === 0) {
            $maxSize = PHP_INT_MAX;
        }

        $this
            ->setChunkSize($maxSize)
            ->setWithCredentials(false)
            ->setForceChunkSize(false)
            ->setQuery([
                'form_key' => Mage::getSingleton('core/session')->getFormKey()
            ])
            ->setMethod(self::UPLOAD_TYPE)
            ->setSimultaneousUploads(1)
            ->setAllowDuplicateUploads(true)
            ->setPrioritizeFirstAndLastChunk(false)
            ->setTestChunks(self::TEST_CHUNKS)
            ->setSpeedSmoothingFactor(self::SMOOTH_UPLOAD_FACTOR)
            ->setProgressCallbacksInterval(self::PROGRESS_CALLBACK_INTERVAL)
            ->setSuccessStatuses([200, 201, 202])
            ->setPermanentErrors([404, 415, 500, 501]);
    }
}
