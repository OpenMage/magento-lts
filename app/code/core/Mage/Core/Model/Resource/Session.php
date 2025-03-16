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
 * @copyright  Copyright (c) 2016-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mysql4 session save handler
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Session implements SessionHandlerInterface
{
    /**
     * Session maximum cookie lifetime
     */
    public const SEESION_MAX_COOKIE_LIFETIME = 3155692600;

    /**
     * Session lifetime
     *
     * @var string|int|null
     */
    protected $_lifeTime;

    /**
     * Session data table name
     *
     * @var string
     */
    protected $_sessionTable;

    /**
     * Database read connection
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_read;

    /**
     * Database write connection
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_write;

    /**
     * Automatic cleaning factor of expired sessions
     * value zero means no automatic cleaning, one means automatic cleaning each time a session is closed, and x>1 means
     * cleaning once in x calls
     *
     * @var int
     */
    protected $_automaticCleaningFactor    = 50;

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->_sessionTable = $resource->getTableName('core/session');
        $this->_read         = $resource->getConnection('core_read');
        $this->_write        = $resource->getConnection('core_write');
    }

    /**
     * Destructor
     *
     */
    public function __destruct()
    {
        session_write_close();
    }

    /**
     * Retrieve session life time
     *
     * @return int
     */
    public function getLifeTime()
    {
        if (is_null($this->_lifeTime)) {
            $configNode = Mage::app()->getStore()->isAdmin() ?
                    'admin/security/session_cookie_lifetime' : 'web/cookie/cookie_lifetime';
            $this->_lifeTime = Mage::getStoreConfigAsInt($configNode);

            if ($this->_lifeTime < 60) {
                $this->_lifeTime = ini_get('session.gc_maxlifetime');
            }

            if ($this->_lifeTime < 60) {
                $this->_lifeTime = 3600; //one hour
            }

            if ($this->_lifeTime > self::SEESION_MAX_COOKIE_LIFETIME) {
                $this->_lifeTime = self::SEESION_MAX_COOKIE_LIFETIME; // 100 years
            }
        }
        return $this->_lifeTime;
    }

    /**
     * Check DB connection
     *
     * @return bool
     */
    public function hasConnection()
    {
        if (!$this->_read) {
            return false;
        }
        if (!$this->_read->isTableExists($this->_sessionTable)) {
            return false;
        }

        return true;
    }

    /**
     * Setup save handler
     *
     * @return $this
     */
    public function setSaveHandler()
    {
        if ($this->hasConnection()) {
            session_set_save_handler(
                [$this, 'open'],
                [$this, 'close'],
                [$this, 'read'],
                [$this, 'write'],
                [$this, 'destroy'],
                [$this, 'gc'],
            );
        } else {
            session_save_path(Mage::getBaseDir('session'));
        }
        return $this;
    }

    /**
     * Adds session handler via static call
     */
    public static function setStaticSaveHandler()
    {
        $handler = new self();
        $handler->setSaveHandler();
    }

    /**
     * Open session
     *
     * @param string $savePath ignored
     * @param string $sessName ignored
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function open($savePath, $sessName)
    {
        return true;
    }

    /**
     * Close session
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function close()
    {
        $this->gc($this->getLifeTime());

        return true;
    }

    /**
     * Fetch session data
     *
     * @param string $sessId
     * @return string
     */
    #[\ReturnTypeWillChange]
    public function read($sessId)
    {
        $select = $this->_read->select()
                ->from($this->_sessionTable, ['session_data'])
                ->where('session_id = :session_id')
                ->where('session_expires > :session_expires');
        $bind = [
            'session_id'      => $sessId,
            'session_expires' => Varien_Date::toTimestamp(true),
        ];

        $data = $this->_read->fetchOne($select, $bind);

        return (string) $data;
    }

    /**
     * Update session
     *
     * @param string $sessId
     * @param string $sessData
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function write($sessId, $sessData)
    {
        $bindValues = [
            'session_id'      => $sessId,
        ];
        $select = $this->_write->select()
                ->from($this->_sessionTable)
                ->where('session_id = :session_id');
        $exists = $this->_read->fetchOne($select, $bindValues);

        $bind = [
            'session_expires' => Varien_Date::toTimestamp(true) + $this->getLifeTime(),
            'session_data' => $sessData,
        ];
        if ($exists) {
            $where = [
                'session_id=?' => $sessId,
            ];
            $this->_write->update($this->_sessionTable, $bind, $where);
        } else {
            $bind['session_id'] = $sessId;
            $this->_write->insert($this->_sessionTable, $bind);
        }

        return true;
    }

    /**
     * Destroy session
     *
     * @param string $sessId
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function destroy($sessId)
    {
        $where = ['session_id = ?' => $sessId];
        $this->_write->delete($this->_sessionTable, $where);
        return true;
    }

    /**
     * Garbage collection
     *
     * @param int $sessMaxLifeTime ignored
     * @return bool
     * @SuppressWarnings("PHPMD.ShortMethodName")
     */
    #[\ReturnTypeWillChange]
    public function gc($sessMaxLifeTime)
    {
        if ($this->_automaticCleaningFactor > 0) {
            if ($this->_automaticCleaningFactor == 1 ||
                random_int(1, $this->_automaticCleaningFactor) == 1
            ) {
                $where = ['session_expires < ?' => Varien_Date::toTimestamp(true)];
                $this->_write->delete($this->_sessionTable, $where);
            }
        }
        return true;
    }
}
