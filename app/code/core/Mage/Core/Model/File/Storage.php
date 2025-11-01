<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * File storage model class
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_File_Storage extends Mage_Core_Model_Abstract
{
    /**
     * Storage systems ids
     */
    public const STORAGE_MEDIA_FILE_SYSTEM         = 0;

    public const STORAGE_MEDIA_DATABASE            = 1;

    /**
     * Config paths for storing storage configuration
     */
    public const XML_PATH_STORAGE_MEDIA            = 'default/system/media_storage_configuration/media_storage';

    public const XML_PATH_STORAGE_MEDIA_DATABASE   = 'default/system/media_storage_configuration/media_database';

    public const XML_PATH_MEDIA_RESOURCE_WHITELIST = 'default/system/media_storage_configuration/allowed_resources';

    public const XML_PATH_MEDIA_RESOURCE_IGNORED   = 'default/system/media_storage_configuration/ignored_resources';

    public const XML_PATH_MEDIA_LOADED_MODULES     = 'default/system/media_storage_configuration/loaded_modules';

    public const XML_PATH_MEDIA_UPDATE_TIME        = 'system/media_storage_configuration/configuration_update_time';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_file_storage';

    /**
     * Show if there were errors while synchronize process
     *
     * @return bool
     */
    protected function _synchronizeHasErrors(
        Mage_Core_Model_Abstract $sourceModel,
        Mage_Core_Model_Abstract $destinationModel
    ) {
        return $sourceModel->hasErrors() || $destinationModel->hasErrors();
    }

    /**
     * Return synchronize process status flag
     *
     * @return Mage_Core_Model_File_Storage_Flag
     */
    public function getSyncFlag()
    {
        return Mage::getSingleton('core/file_storage_flag')->loadSelf();
    }

    /**
     * Retrieve storage model
     * If storage not defined - retrieve current storage
     *
     * params = array(
     *  connection  => string,  - define connection for model if needed
     *  init        => bool     - force initialization process for storage model
     * )
     *
     * @param  null|int $storage
     * @param  array $params
     * @return false|Mage_Core_Model_File_Storage_Database|Mage_Core_Model_File_Storage_File
     */
    public function getStorageModel($storage = null, $params = [])
    {
        if (is_null($storage)) {
            $storage = Mage::helper('core/file_storage')->getCurrentStorageCode();
        }

        switch ($storage) {
            case self::STORAGE_MEDIA_FILE_SYSTEM:
                $model = Mage::getModel('core/file_storage_file');
                break;
            case self::STORAGE_MEDIA_DATABASE:
                $connection = $params['connection'] ?? null;
                $model = Mage::getModel('core/file_storage_database', ['connection' => $connection]);
                break;
            default:
                return false;
        }

        if (isset($params['init']) && $params['init']) {
            $model->init();
        }

        return $model;
    }

    /**
     * Synchronize current media storage with defined
     * $storage = array(
     *  type        => int
     *  connection  => string
     * )
     *
     * @param  array $storage
     * @return $this
     */
    public function synchronize($storage)
    {
        if (is_array($storage) && isset($storage['type'])) {
            $storageDest    = (int) $storage['type'];
            $connection     = $storage['connection'] ?? null;
            $helper         = Mage::helper('core/file_storage');

            // if unable to sync to internal storage from itself
            if ($storageDest == $helper->getCurrentStorageCode() && $helper->isInternalStorage()) {
                return $this;
            }

            $sourceModel        = $this->getStorageModel();
            $destinationModel   = $this->getStorageModel(
                $storageDest,
                [
                    'connection'    => $connection,
                    'init'          => true,
                ],
            );

            if (!$sourceModel || !$destinationModel) {
                return $this;
            }

            $hasErrors = false;
            $flag = $this->getSyncFlag();
            $flagData = [
                'source'                        => $sourceModel->getStorageName(),
                'destination'                   => $destinationModel->getStorageName(),
                'destination_storage_type'      => $storageDest,
                'destination_connection_name'   => (string) $destinationModel->getConfigConnectionName(),
                'has_errors'                    => false,
                'timeout_reached'               => false,
            ];
            $flag->setFlagData($flagData);

            $destinationModel->clear();

            $offset = 0;
            while (($dirs = $sourceModel->exportDirectories($offset)) !== false) {
                $flagData['timeout_reached'] = false;
                if (!$hasErrors) {
                    $hasErrors = $this->_synchronizeHasErrors($sourceModel, $destinationModel);
                    if ($hasErrors) {
                        $flagData['has_errors'] = true;
                    }
                }

                $flag->setFlagData($flagData)
                    ->save();

                $destinationModel->importDirectories($dirs);
                $offset += count($dirs);
            }

            unset($dirs);

            $offset = 0;
            while (($files = $sourceModel->exportFiles($offset, 1)) !== false) {
                $flagData['timeout_reached'] = false;
                if (!$hasErrors) {
                    $hasErrors = $this->_synchronizeHasErrors($sourceModel, $destinationModel);
                    if ($hasErrors) {
                        $flagData['has_errors'] = true;
                    }
                }

                $flag->setFlagData($flagData)
                    ->save();

                $destinationModel->importFiles($files);
                $offset += count($files);
            }

            unset($files);
        }

        return $this;
    }

    /**
     * Return current media directory, allowed resources for get.php script, etc.
     *
     * @return array
     */
    public static function getScriptConfig()
    {
        $config = [];
        $config['media_directory'] = Mage::getBaseDir('media');

        $loadedModules = (array) Mage::app()->getConfig()->getNode(self::XML_PATH_MEDIA_LOADED_MODULES);
        foreach ($loadedModules as $loadedModule) {
            $config['loaded_modules'][] = $loadedModule->getName();
        }

        $allowedResources = (array) Mage::app()->getConfig()->getNode(self::XML_PATH_MEDIA_RESOURCE_WHITELIST);
        foreach ($allowedResources as $allowedResource) {
            $config['allowed_resources'][] = $allowedResource;
        }

        $config['update_time'] = Mage::getStoreConfig(self::XML_PATH_MEDIA_UPDATE_TIME);

        return $config;
    }
}
