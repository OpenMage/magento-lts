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
 * @package     Mage_Uploader
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Uploader Config Instance Abstract Model
 *
 * @category    Mage
 * @package     Mage_Uploader
 */

/**
 * @method Mage_Uploader_Model_Config_Uploader setTarget(string $url)
 *      The target URL for the multipart POST request.
 * @method Mage_Uploader_Model_Config_Uploader setSingleFile(bool $isSingleFile)
 *      Enable single file upload.
 *      Once one file is uploaded, second file will overtake existing one, first one will be canceled.
 * @method Mage_Uploader_Model_Config_Uploader setChunkSize(int $chunkSize) The size in bytes of each uploaded chunk of data.
 * @method Mage_Uploader_Model_Config_Uploader setForceChunkSize(bool $forceChunkSize)
 *      Force all chunks to be less or equal than chunkSize.
 * @method Mage_Uploader_Model_Config_Uploader setSimultaneousUploads(int $amountOfSimultaneousUploads)
 * @method Mage_Uploader_Model_Config_Uploader setFileParameterName(string $fileUploadParam)
 * @method Mage_Uploader_Model_Config_Uploader setQuery(array $additionalQuery)
 * @method Mage_Uploader_Model_Config_Uploader setHeaders(array $headers)
 *      Extra headers to include in the multipart POST with data.
 * @method Mage_Uploader_Model_Config_Uploader setWithCredentials(bool $isCORS)
 *      Standard CORS requests do not send or set any cookies by default.
 *      In order to include cookies as part of the request, you need to set the withCredentials property to true.
 * @method Mage_Uploader_Model_Config_Uploader setMethod(string $sendMethod)
 *       Method to use when POSTing chunks to the server. Defaults to "multipart"
 * @method Mage_Uploader_Model_Config_Uploader setTestMethod(string $testMethod) Defaults to "GET"
 * @method Mage_Uploader_Model_Config_Uploader setUploadMethod(string $uploadMethod) Defaults to "POST"
 * @method Mage_Uploader_Model_Config_Uploader setAllowDuplicateUploads(bool $allowDuplicateUploads)
 *      Once a file is uploaded, allow reupload of the same file. By default, if a file is already uploaded,
 *      it will be skipped unless the file is removed from the existing Flow object.
 * @method Mage_Uploader_Model_Config_Uploader setPrioritizeFirstAndLastChunk(bool $prioritizeFirstAndLastChunk)
 *      This can be handy if you can determine if a file is valid for your service from only the first or last chunk.
 * @method Mage_Uploader_Model_Config_Uploader setTestChunks(bool $prioritizeFirstAndLastChunk)
 *      Make a GET request to the server for each chunks to see if it already exists.
 * @method Mage_Uploader_Model_Config_Uploader setPreprocess(bool $prioritizeFirstAndLastChunk)
 *      Optional function to process each chunk before testing & sending.
 * @method Mage_Uploader_Model_Config_Uploader setInitFileFn(string $function)
 *      Optional function to initialize the fileObject (js).
 * @method Mage_Uploader_Model_Config_Uploader setReadFileFn(string $function)
 *      Optional function wrapping reading operation from the original file.
 * @method Mage_Uploader_Model_Config_Uploader setGenerateUniqueIdentifier(string $function)
 *      Override the function that generates unique identifiers for each file. Defaults to "null"
 * @method Mage_Uploader_Model_Config_Uploader setMaxChunkRetries(int $maxChunkRetries) Defaults to 0
 * @method Mage_Uploader_Model_Config_Uploader setChunkRetryInterval(int $chunkRetryInterval) Defaults to "undefined"
 * @method Mage_Uploader_Model_Config_Uploader setProgressCallbacksInterval(int $progressCallbacksInterval)
 * @method Mage_Uploader_Model_Config_Uploader setSpeedSmoothingFactor(int $speedSmoothingFactor)
 *      Used for calculating average upload speed. Number from 1 to 0.
 *      Set to 1 and average upload speed wil be equal to current upload speed.
 *      For longer file uploads it is better set this number to 0.02,
 *      because time remaining estimation will be more accurate.
 * @method Mage_Uploader_Model_Config_Uploader setSuccessStatuses(array $successStatuses)
 *      Response is success if response status is in this list
 * @method Mage_Uploader_Model_Config_Uploader setPermanentErrors(array $permanentErrors)
 *      Response fails if response status is in this list
 *
 * Class Mage_Uploader_Model_Config_Uploader
 */
class Mage_Uploader_Model_Config_Uploader extends Mage_Uploader_Model_Config_Abstract
{
    /**
     * Type of upload
     */
    const UPLOAD_TYPE = 'multipart';

    /**
     * Test chunks on resumable uploads
     */
    const TEST_CHUNKS = false;

    /**
     * Used for calculating average upload speed.
     */
    const SMOOTH_UPLOAD_FACTOR = 0.02;

    /**
     * Progress check interval
     */
    const PROGRESS_CALLBACK_INTERVAL = 0;

    /**
     * Set default values for uploader
     */
    protected function _construct()
    {
        $this
            ->setChunkSize($this->_getHelper()->getDataMaxSizeInBytes())
            ->setWithCredentials(false)
            ->setForceChunkSize(false)
            ->setQuery(array(
                'form_key' => Mage::getSingleton('core/session')->getFormKey()
            ))
            ->setMethod(self::UPLOAD_TYPE)
            ->setAllowDuplicateUploads(true)
            ->setPrioritizeFirstAndLastChunk(false)
            ->setTestChunks(self::TEST_CHUNKS)
            ->setSpeedSmoothingFactor(self::SMOOTH_UPLOAD_FACTOR)
            ->setProgressCallbacksInterval(self::PROGRESS_CALLBACK_INTERVAL)
            ->setSuccessStatuses(array(200, 201, 202))
            ->setPermanentErrors(array(404, 415, 500, 501));
    }
}
