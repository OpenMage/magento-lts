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
 * @category    Varien
 * @package     Varien_Cache
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
Tables declaration:

CREATE TABLE IF NOT EXISTS `core_cache` (
        `id` VARCHAR(255) NOT NULL,
        `data` mediumblob,
        `create_time` int(11),
        `update_time` int(11),
        `expire_time` int(11),
        PRIMARY KEY  (`id`),
        KEY `IDX_EXPIRE_TIME` (`expire_time`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `core_cache_tag` (
    `tag` VARCHAR(255) NOT NULL,
    `cache_id` VARCHAR(255) NOT NULL,
    KEY `IDX_TAG` (`tag`),
    KEY `IDX_CACHE_ID` (`cache_id`),
    CONSTRAINT `FK_CORE_CACHE_TAG` FOREIGN KEY (`cache_id`)
    REFERENCES `core_cache` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

/**
 * Database cache backend
 */
class Varien_Cache_Backend_Database
    extends Zend_Cache_Backend implements Zend_Cache_Backend_ExtendedInterface
{
    /**
     * Available options
     *
     * @var array available options
     */
    protected $_options = array(
        'adapter'           => '',
        'adapter_callback'  => '',
        'data_table'        => '',
        'tags_table'        => '',
        'store_data'        => true,
    );

    protected $_adapter = null;

    /**
     * Constructor
     *
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        if (empty($this->_options['adapter_callback'])) {
            if (!($this->_options['adapter'] instanceof Zend_Db_Adapter_Abstract)) {
                Zend_Cache::throwException('Option "adapter" should be declared and extend Zend_Db_Adapter_Abstract!');
            }
        }
        if (empty($this->_options['data_table']) || empty ($this->_options['tags_table'])) {
            Zend_Cache::throwException('Options "data_table" and "tags_table" should be declared!');
        }
    }

    /**
     * Get DB adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _getAdapter()
    {
        if (!$this->_adapter) {
            if (!empty($this->_options['adapter_callback'])) {
                $adapter = call_user_func($this->_options['adapter_callback']);
            } else {
                $adapter = $this->_options['adapter'];
            }
            if (!($adapter instanceof Zend_Db_Adapter_Abstract)) {
                Zend_Cache::throwException('DB Adapter should be declared and extend Zend_Db_Adapter_Abstract');
            } else {
                $this->_adapter = $adapter;
            }
        }
        return $this->_adapter;
    }

    /**
     * Get table name where data is stored
     *
     * @return string
     */
    protected function _getDataTable()
    {
        return $this->_options['data_table'];
    }

    /**
     * Get table name where tags are stored
     *
     * @return string
     */
    protected function _getTagsTable()
    {
        return $this->_options['tags_table'];
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * Note : return value is always "string" (unserialization is done by the core not by the backend)
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @return string|false cached datas
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        if ($this->_options['store_data']) {
            $select = $this->_getAdapter()->select()
                ->from($this->_getDataTable(), 'data')
                ->where('id=:cache_id');

            if (!$doNotTestCacheValidity) {
                $select->where('expire_time=0 OR expire_time>?', time());
            }
            return $this->_getAdapter()->fetchOne($select, array('cache_id'=>$id));
        } else {
            return false;
        }
    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param  string $id cache id
     * @return mixed|false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        if ($this->_options['store_data']) {
            $select = $this->_getAdapter()->select()
                ->from($this->_getDataTable(), 'update_time')
                ->where('id=:cache_id')
                ->where('expire_time=0 OR expire_time>?', time());
            return $this->_getAdapter()->fetchOne($select, array('cache_id'=>$id));
        } else {
            return false;
        }
    }

    /**
     * Save data into a cache storage
     *
     * Note : $data is always "string" (serialization is done by the
     * core not by the backend)
     *
     * @param  string $data Data to cache
     * @param  string $id Cache id
     * @param  array $tags Array of strings, the cache record will be tagged by each string entry
     * @param  int|bool|null $specificLifetime If != false, set a specific lifetime for this cache record
     *                                    (null => infinite lifetime)
     *
     * @return bool true if no problem
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        if ($this->_options['store_data']) {
            $adapter    = $this->_getAdapter();
            $dataTable  = $this->_getDataTable();

            $lifetime = $this->getLifetime($specificLifetime);
            $time     = time();
            $expire   = ($lifetime === 0 || $lifetime === null) ? 0 : $time+$lifetime;

            $dataCol    = $adapter->quoteIdentifier('data');
            $expireCol  = $adapter->quoteIdentifier('expire_time');
            $query = "INSERT INTO {$dataTable} (
                    {$adapter->quoteIdentifier('id')},
                    {$dataCol},
                    {$adapter->quoteIdentifier('create_time')},
                    {$adapter->quoteIdentifier('update_time')},
                    {$expireCol})
                VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
                    {$dataCol}=VALUES({$dataCol}),
                    {$expireCol}=VALUES({$expireCol})";

            $result = $adapter->query($query, array($id, $data, $time, $time, $expire))->rowCount();
            if (!$result) {
                return false;
            }
        }
        $tagRes = $this->_saveTags($id, $tags);
        return $tagRes;
    }

    /**
     * Remove a cache record
     *
     * @param  string $id Cache id
     * @return boolean True if no problem
     */
    public function remove($id)
    {
        $adapter = $this->_getAdapter();
        $result = true;
        if ($this->_options['store_data']) {
            $result = $adapter->delete($this->_getDataTable(), array('id = ?' => $id));
        }

        return $result && $adapter->delete($this->_getTagsTable(), array('cache_id = ?' => $id));
    }

    /**
     * Delete cache rows from Data table
     *
     * @param $cacheIdsToRemove
     * @return int
     */
    protected function _deleteCachesFromDataTable($cacheIdsToRemove)
    {
        return $this->_getAdapter()->delete($this->_getDataTable(), array('id IN (?)' => $cacheIdsToRemove));
    }

    /**
     * Delete cache rows from Tags table
     *
     * @param $cacheIdsToRemove
     * @return int
     */
    protected function _deleteCachesFromTagsTable($cacheIdsToRemove)
    {
        return $this->_getAdapter()->delete($this->_getTagsTable(), array('cache_id IN (?)' => $cacheIdsToRemove));
    }

    /**
     * Clean some cache records
     *
     * Available modes are :
     * Zend_Cache::CLEANING_MODE_ALL (default)    => remove all cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_OLD              => remove too old cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_MATCHING_TAG     => remove cache entries matching all given tags
     *                                               ($tags can be an array of strings or a single string)
     * Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG => remove cache entries not {matching one of the given tags}
     *                                               ($tags can be an array of strings or a single string)
     * Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG => remove cache entries matching any given tags
     *                                               ($tags can be an array of strings or a single string)
     *
     * @param  string $mode Clean mode
     * @param  array  $tags Array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        $adapter = $this->_getAdapter();
        $result = true;

        switch($mode) {
            case Zend_Cache::CLEANING_MODE_ALL:
                if ($this->_options['store_data']) {
                    $result = $adapter->query('TRUNCATE TABLE ' . $this->_getDataTable());
                }
                $result = $result && $adapter->query('TRUNCATE TABLE ' . $this->_getTagsTable());
                break;
            case Zend_Cache::CLEANING_MODE_OLD:
                if ($this->_options['store_data']) {
                    $result = $this->_cleanOldCache();
                }
                break;
            case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
            case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
            case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                $result = $this->_cleanByTags($mode, $tags);
                break;
            default:
                Zend_Cache::throwException('Invalid mode for clean() method');
                break;
        }

        return $result;
    }

    /**
     * Clean old cache data and related cache tag data
     *
     * @return bool
     */
    protected function _cleanOldCache()
    {
        $time    = time();
        $counter = 0;
        $result  = true;
        $adapter = $this->_getAdapter();
        $cacheIdsToRemove = array();

        $select = $adapter->select()
            ->from($this->_getDataTable(), 'id')
            ->where('expire_time > ?', 0)
            ->where('expire_time <= ?', $time)
        ;

        $statement = $adapter->query($select);
        while ($row = $statement->fetch()) {
            if (!$result) {
                break;
            }
            $cacheIdsToRemove[] = $row['id'];
            $counter++;
            if ($counter > 100) {
                $result = $result && $this->_deleteCachesFromDataTable($cacheIdsToRemove);
                $result = $result && $this->_deleteCachesFromTagsTable($cacheIdsToRemove);
                $cacheIdsToRemove = array();
                $counter = 0;
            }
        }
        if (!empty($cacheIdsToRemove)) {
            $result = $result && $this->_deleteCachesFromDataTable($cacheIdsToRemove);
            $result = $result && $this->_deleteCachesFromTagsTable($cacheIdsToRemove);
        }

        return $result;
    }

    /**
     * Return an array of stored cache ids
     *
     * @return array array of stored cache ids (string)
     */
    public function getIds()
    {
        if ($this->_options['store_data']) {
            $select = $this->_getAdapter()->select()
                ->from($this->_getDataTable(), 'id');
            return $this->_getAdapter()->fetchCol($select);
        } else {
            return array();
        }
    }

    /**
     * Return an array of stored tags
     *
     * @return array array of stored tags (string)
     */
    public function getTags()
    {
        $select = $this->_getAdapter()->select()
            ->from($this->_getTagsTable(), 'tag')
            ->distinct(true);
        return $this->_getAdapter()->fetchCol($select);
    }

    /**
     * Return an array of stored cache ids which match given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of matching cache ids (string)
     */
    public function getIdsMatchingTags($tags = array())
    {
        $select = $this->_getAdapter()->select()
            ->from($this->_getTagsTable(), 'cache_id')
            ->distinct(true)
            ->where('tag IN(?)', $tags)
            ->group('cache_id')
            ->having('COUNT(cache_id)='.count($tags));
        return $this->_getAdapter()->fetchCol($select);
    }

    /**
     * Return an array of stored cache ids which don't match given tags
     *
     * In case of multiple tags, a logical OR is made between tags
     *
     * @param array $tags array of tags
     * @return array array of not matching cache ids (string)
     */
    public function getIdsNotMatchingTags($tags = array())
    {
        return array_diff($this->getIds(), $this->getIdsMatchingAnyTags($tags));
    }

    /**
     * Return an array of stored cache ids which match any given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of any matching cache ids (string)
     */
    public function getIdsMatchingAnyTags($tags = array())
    {
        $select = $this->_getAdapter()->select()
            ->from($this->_getTagsTable(), 'cache_id')
            ->distinct(true)
            ->where('tag IN(?)', $tags);
        return $this->_getAdapter()->fetchCol($select);
    }

    /**
     * Return the filling percentage of the backend storage
     *
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        return 1;
    }

    /**
     * Return an array of metadatas for the given cache id
     *
     * The array must include these keys :
     * - expire : the expire timestamp
     * - tags : a string array of tags
     * - mtime : timestamp of last modification time
     *
     * @param string $id cache id
     * @return array array of metadatas (false if the cache id is not found)
     */
    public function getMetadatas($id)
    {
        $select = $this->_getAdapter()->select()
            ->from($this->_getTagsTable(), 'tag')
            ->where('cache_id=?', $id);
        $tags = $this->_getAdapter()->fetchCol($select);

        $select = $this->_getAdapter()->select()
            ->from($this->_getDataTable())
            ->where('id=?', $id);
        $data = $this->_getAdapter()->fetchRow($select);
        $res = false;
        if ($data) {
            $res = array (
                'expire'=> $data['expire_time'],
                'mtime' => $data['update_time'],
                'tags'  => $tags
            );
        }
        return $res;
    }

    /**
     * Give (if possible) an extra lifetime to the given cache id
     *
     * @param string $id cache id
     * @param int $extraLifetime
     * @return boolean true if ok
     */
    public function touch($id, $extraLifetime)
    {
        if ($this->_options['store_data']) {
            return $this->_getAdapter()->update(
                $this->_getDataTable(),
                array('expire_time'=>new Zend_Db_Expr('expire_time+'.$extraLifetime)),
                array('id=?' => $id, 'expire_time = 0 OR expire_time>?' => time())
            );
        } else {
            return true;
        }
    }

    /**
     * Return an associative array of capabilities (booleans) of the backend
     *
     * The array must include these keys :
     * - automatic_cleaning (is automating cleaning necessary)
     * - tags (are tags supported)
     * - expired_read (is it possible to read expired cache records
     *                 (for doNotTestCacheValidity option for example))
     * - priority does the backend deal with priority when saving
     * - infinite_lifetime (is infinite lifetime can work with this backend)
     * - get_list (is it possible to get the list of cache ids and the complete list of tags)
     *
     * @return array associative of with capabilities
     */
    public function getCapabilities()
    {
        return array(
            'automatic_cleaning' => true,
            'tags' => true,
            'expired_read' => true,
            'priority' => false,
            'infinite_lifetime' => true,
            'get_list' => true
        );
    }

    /**
     * Save tags related to specific id
     *
     * @param string $id
     * @param array $tags
     * @return bool
     */
    protected function _saveTags($id, $tags)
    {
        if (!is_array($tags)) {
            $tags = array($tags);
        }
        if (empty($tags)) {
            return true;
        }

        $adapter = $this->_getAdapter();
        $tagsTable = $this->_getTagsTable();
        $select = $adapter->select()
            ->from($tagsTable, 'tag')
            ->where('cache_id=?', $id)
            ->where('tag IN(?)', $tags);

        $result = true;
        $existingTags = $adapter->fetchCol($select);
        $insertTags = array_diff($tags, $existingTags);
        if (!empty($insertTags)) {
            $query = 'INSERT IGNORE INTO ' . $tagsTable . ' (tag, cache_id) VALUES ';
            $bind = array();
            $lines = array();
            foreach ($insertTags as $tag) {
                $lines[] = '(?, ?)';
                $bind[] = $tag;
                $bind[] = $id;
            }
            $query .= implode(',', $lines);
            $result = $adapter->query($query, $bind);
        }

        return $result;
    }

    /**
     * Remove cache data by tags with specified mode
     *
     * @param string $mode
     * @param array $tags
     * @return bool
     */
    protected function _cleanByTags($mode, $tags)
    {
        $adapter = $this->_getAdapter();
        $result = true;
        $select = $adapter->select()
            ->from($this->_getTagsTable(), 'cache_id');
        switch ($mode) {
            case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
                $select->where('tag IN (?)', $tags)
                    ->group('cache_id')
                    ->having('COUNT(cache_id) = ' . count($tags));
                break;
            case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
                $select->where('tag NOT IN (?)', $tags);
                break;
            case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                $select->where('tag IN (?)', $tags);
                break;
            default:
                Zend_Cache::throwException('Invalid mode for _cleanByTags() method');
                break;
        }

        $cacheIdsToRemove = array();
        $counter = 0;
        $statement = $adapter->query($select);
        while ($row = $statement->fetch()) {
            if (!$result) {
                break;
            }
            $cacheIdsToRemove[] = $row['cache_id'];
            $counter++;
            if ($counter > 100) {
                if ($this->_options['store_data']) {
                    $result = $result && $this->_deleteCachesFromDataTable($cacheIdsToRemove);
                }
                $result = $result && $this->_deleteCachesFromTagsTable($cacheIdsToRemove);
                $cacheIdsToRemove = array();
                $counter = 0;
            }
        }
        if (!empty($cacheIdsToRemove)) {
            if ($this->_options['store_data']) {
                $result = $result && $this->_deleteCachesFromDataTable($cacheIdsToRemove);
            }
            $result = $result && $this->_deleteCachesFromTagsTable($cacheIdsToRemove);
        }
        return $result;
    }
}
