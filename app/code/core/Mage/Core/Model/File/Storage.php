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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * File storage model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_File_Storage extends Mage_Core_Model_Abstract
{
    /**
     * Storage systems ids
     */
    const STORAGE_MEDIA_FILE_SYSTEM         = 0;
    const STORAGE_MEDIA_DATABASE            = 1;

    /**
     * Config pathes for storing storage configuration
     */
    const XML_PATH_STORAGE_MEDIA            = 'default/system/media_storage_configuration/media_storage';
    const XML_PATH_STORAGE_MEDIA_DATABASE   = 'default/system/media_storage_configuration/media_database';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_file_storage';

    /**
     * Retrieve storage model
     * If storage not defined - retrieve current storage
     *
     * params = array(
     *  connection  => string,  - define connection for model if needed
     *  init        => bool     - force initialization process for storage
     * )
     *
     * @param  int|null $storage
     * @param  array $params
     * @return Mage_Core_Model_Abstract|bool
     */
    public function getStorageModel($storage = null, $params = array())
    {
        if (is_null($storage)) {
            $storage = Mage::helper('core/file_storage')->getCurrentStorage();
        }

        switch ($storage) {
            case self::STORAGE_MEDIA_FILE_SYSTEM:
                $model = Mage::getModel('core/file_storage_file');
                break;
            case self::STORAGE_MEDIA_DATABASE:
                $connection = (isset($params['connection'])) ? $params['connection'] : null;
                $model = Mage::getModel('core/file_storage_database', array('connection' => $connection));
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
     * @param  Mage_Core_Model_File_Storage_Flag|null $flag
     * @return Mage_Core_Model_File_Storage
     */
    public function synchronize($storage, Mage_Core_Model_File_Storage_Flag $flag = null)
    {
        if (isset($storage['type'])) {
            $storageDest    = (int) $storage['type'];
            $connection     = (isset($storage['connection'])) ? $storage['connection'] : null;
            $helper         = Mage::helper('core/file_storage');

            // if unable to sync to internal storage from itself
            if ($storageDest == $helper->getCurrentStorage() && $helper->isInternalStorage()) {
                return $this;
            }

            $sourceModel        = $this->getStorageModel();
            $destinationModel   = $this->getStorageModel(
                $storageDest,
                array(
                    'connection'    => $connection,
                    'init'          => true
                )
            );

            if (!$sourceModel || !$destinationModel) {
                return $this;
            }

            if ($flag) {
                $flag->setFlagData(array(
                    'source'        => $sourceModel->getStorageName(),
                    'destination'   => $destinationModel->getStorageName()
                ));
            }

            $destinationModel->clear();

            $offset = 0;
            while (($dirs = $sourceModel->exportDirectories($offset)) !== false) {
                if ($flag) {
                    $flag->save();
                }

                $destinationModel->importDirectories($dirs);
                $offset += count($dirs);
            }

            $offset = 0;
            while (($files = $sourceModel->exportFiles($offset, 1)) !== false) {
                if ($flag) {
                    $flag->save();
                }

                $destinationModel->importFiles($files);
                $offset += count($files);
            }
        }

        return $this;
    }
}
