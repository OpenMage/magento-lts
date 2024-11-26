<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ImportExport MySQL resource helper model
 *
 * @category   Mage
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Resource_Helper_Mysql4 extends Mage_Core_Model_Resource_Helper_Mysql4
{
    /**
     * Constants to be used for DB
     */
    public const DB_MAX_PACKET_SIZE        = 1048576; // Maximal packet length by default in MySQL
    public const DB_MAX_PACKET_COEFFICIENT = 0.85; // The coefficient of useful data from maximum packet length

    /**
     * Semaphore to disable schema stats only once
     *
     * @var bool
     */
    private static $instantInformationSchemaStatsExpiry = false;

    /**
     * Returns maximum size of packet, that we can send to DB
     *
     * @return float
     */
    public function getMaxDataSize()
    {
        $maxPacketData = $this->_getReadAdapter()->fetchRow('SHOW VARIABLES LIKE "max_allowed_packet"');
        $maxPacket = empty($maxPacketData['Value']) ? self::DB_MAX_PACKET_SIZE : $maxPacketData['Value'];
        return floor($maxPacket * self::DB_MAX_PACKET_COEFFICIENT);
    }

    /**
     * Returns next autoincrement value for a table
     *
     * @param string $tableName
     * @return int
     * @throws Mage_Core_Exception
     */
    public function getNextAutoincrement($tableName)
    {
        $adapter = $this->_getReadAdapter();
        $this->setInformationSchemaStatsExpiry();
        $entityStatus = $adapter->showTableStatus($tableName);
        if (empty($entityStatus['Auto_increment'])) {
            Mage::throwException(Mage::helper('importexport')->__('Cannot get autoincrement value'));
        }
        return $entityStatus['Auto_increment'];
    }

    /**
     * Set information_schema_stats_expiry to 0 if not already set.
     */
    public function setInformationSchemaStatsExpiry(): void
    {
        if (!self::$instantInformationSchemaStatsExpiry) {
            try {
                $this->_getReadAdapter()->query('SET information_schema_stats_expiry = 0;');
            } catch (Exception $e) {
            }
            self::$instantInformationSchemaStatsExpiry = true;
        }
    }
}
