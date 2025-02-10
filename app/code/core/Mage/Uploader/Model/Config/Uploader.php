<?php

/**
 * Uploader Config Instance Abstract Model
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Uploader
 * @method $this setTarget(string $url)
 * @method $this setSingleFile(bool $isSingleFile)
 * @method $this setChunkSize(int $chunkSize) The size in bytes of each uploaded chunk of data.
 * @method $this setForceChunkSize(bool $forceChunkSize)
 * @method $this setSimultaneousUploads(int $amountOfSimultaneousUploads)
 * @method $this setFileParameterName(string $fileUploadParam)
 * @method $this setQuery(array $additionalQuery)
 * @method $this setHeaders(array $headers)
 * @method $this setWithCredentials(bool $isCORS)
 * @method $this setMethod(string $sendMethod)
 * @method $this setTestMethod(string $testMethod) Defaults to "GET"
 * @method $this setUploadMethod(string $uploadMethod) Defaults to "POST"
 * @method $this setAllowDuplicateUploads(bool $allowDuplicateUploads)
 * @method $this setPrioritizeFirstAndLastChunk(bool $prioritizeFirstAndLastChunk)
 * @method $this setTestChunks(bool $prioritizeFirstAndLastChunk)
 * @method $this setPreprocess(bool $prioritizeFirstAndLastChunk)
 * @method $this setInitFileFn(string $function)
 * @method $this setReadFileFn(string $function)
 * @method $this setGenerateUniqueIdentifier(string $function)
 * @method $this setMaxChunkRetries(int $maxChunkRetries) Defaults to 0
 * @method $this setChunkRetryInterval(int $chunkRetryInterval) Defaults to "undefined"
 * @method $this setProgressCallbacksInterval(int $progressCallbacksInterval)
 * @method $this setSpeedSmoothingFactor(int $speedSmoothingFactor)
 * @method $this setSuccessStatuses(array $successStatuses)
 * @method $this setPermanentErrors(array $permanentErrors)
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
                'form_key' => Mage::getSingleton('core/session')->getFormKey(),
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
