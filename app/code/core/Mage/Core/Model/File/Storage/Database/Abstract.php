<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract database storage model class
 *
 * @category   Mage
 * @package    Mage_Core
 *
 * @method string getConnectionName()
 */
abstract class Mage_Core_Model_File_Storage_Database_Abstract extends Mage_Core_Model_File_Storage_Abstract
{
    /**
     * Class construct
     *
     * @param array $params
     */
    public function __construct($params = [])
    {
        $connectionName = $params['connection'] ?? null;
        if (empty($connectionName)) {
            $connectionName = $this->getConfigConnectionName();
        }

        $this->setConnectionName($connectionName);
    }

    /**
     * Retrieve connection name saved at config
     *
     * @return string
     */
    public function getConfigConnectionName()
    {
        $connectionName = (string) Mage::app()->getConfig()
            ->getNode(Mage_Core_Model_File_Storage::XML_PATH_STORAGE_MEDIA_DATABASE);
        if (empty($connectionName)) {
            $connectionName = 'default_setup';
        }

        return $connectionName;
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Resource_File_Storage_Database
     */
    protected function _getResource()
    {
        /** @var Mage_Core_Model_Resource_File_Storage_Database $resource */
        $resource = parent::_getResource();
        $resource->setConnectionName($this->getConnectionName());

        return $resource;
    }

    /**
     * Prepare data storage
     *
     * @return $this
     */
    public function prepareStorage()
    {
        $this->_getResource()->createDatabaseScheme();

        return $this;
    }

    /**
     * Specify connection name
     *
     * @param  string $connectionName
     * @return $this
     */
    public function setConnectionName($connectionName)
    {
        if (!empty($connectionName)) {
            $this->setData('connection_name', $connectionName);
            $this->_getResource()->setConnectionName($connectionName);
        }

        return $this;
    }
}
