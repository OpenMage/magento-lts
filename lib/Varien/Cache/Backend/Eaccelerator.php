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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Varien_Cache_Backend_Eaccelerator extends Zend_Cache_Backend implements Zend_Cache_Backend_ExtendedInterface
{
    /**
     * Log message
     */
    const TAGS_UNSUPPORTED_BY_CLEAN_OF_EACCELERATOR_BACKEND = 'Varien_Cache_Backend_Eaccelerator::clean() : tags are unsupported by the Eaccelerator backend';
    const TAGS_UNSUPPORTED_BY_SAVE_OF_EACCELERATOR_BACKEND =  'Varien_Cache_Backend_Eaccelerator::save() : tags are unsupported by the Eaccelerator backend';

    /**
     * Constructor
     *
     * @param  array $options associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct(array $options = array())
    {
        if (!extension_loaded('eaccelerator')) {
            Zend_Cache::throwException('The eaccelerator extension must be loaded for using this backend !');
        }
        parent::__construct($options);
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * WARNING $doNotTestCacheValidity=true is unsupported by the Eaccelerator backend
     *
     * @param  string  $id                     cache id
     * @param  boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string cached datas (or false)
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        $tmp = eaccelerator_get($id);
        if (is_array($tmp)) {
            return $tmp[0];
        }
        return false;
    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param  string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        $tmp = eaccelerator_get($id);
        if (is_array($tmp)) {
            return $tmp[1];
        }
        return false;
    }

    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always "string" (serialization is done by the
     * core not by the backend)
     *
     * @param string $data datas to cache
     * @param string $id cache id
     * @param array $tags array of strings, the cache record will be tagged by each string entry
     * @param int $specificLifetime if != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @return boolean true if no problem
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $lifetime = $this->getLifetime($specificLifetime);
        $result = eaccelerator_put($id, array($data, time(), $lifetime), $lifetime);
        if (count($tags) > 0) {
            $this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_EACCELERATOR_BACKEND);
        }
        return $result;
    }

    /**
     * Remove a cache record
     *
     * @param  string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id)
    {
        return eaccelerator_rm($id);
    }

    /**
     * Clean some cache records
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => unsupported
     * 'matchingTag'    => unsupported
     * 'notMatchingTag' => unsupported
     * 'matchingAnyTag' => unsupported
     *
     * @param  string $mode clean mode
     * @param  array  $tags array of tags
     * @throws Zend_Cache_Exception
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        switch ($mode) {
            case Zend_Cache::CLEANING_MODE_ALL:
                return eaccelerator_clean();
                break;
            case Zend_Cache::CLEANING_MODE_OLD:
                $this->_log("Varien_Cache_Backend_Eaccelerator::clean() : CLEANING_MODE_OLD is unsupported by the Eaccelerator backend");
                break;
            case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
            case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
            case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                $this->_log(self::TAGS_UNSUPPORTED_BY_CLEAN_OF_EACCELERATOR_BACKEND);
                break;
            default:
                Zend_Cache::throwException('Invalid mode for clean() method');
                break;
        }
    }

    /**
     * Return true if the automatic cleaning is available for the backend
     *
     * DEPRECATED : use getCapabilities() instead
     *
     * @deprecated
     * @return boolean
     */
    public function isAutomaticCleaningAvailable()
    {
        return false;
    }

    /**
     * Return the filling percentage of the backend storage
     *
     * @throws Zend_Cache_Exception
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        $mem = eaccelerator_info();
        $memSize    = $mem['memorySize'];
        $memAvailable= $mem['memoryAvailable'];
        $memUsed = $memSize - $memAvailable;
        if ($memSize == 0) {
            Zend_Cache::throwException('can\'t get eaccelerator memory size');
        }
        if ($memUsed > $memSize) {
            return 100;
        }
        return ((int) (100. * ($memUsed / $memSize)));
    }

    /**
     * Return an array of stored tags
     *
     * @return array array of stored tags (string)
     */
    public function getTags()
    {
        $this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_EACCELERATOR_BACKEND);
        return array();
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
        $this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_EACCELERATOR_BACKEND);
        return array();
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
        $this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_EACCELERATOR_BACKEND);
        return array();
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
        $this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_EACCELERATOR_BACKEND);
        return array();
    }

    /**
     * Return an array of stored cache ids
     *
     * @return array array of stored cache ids (string)
     */
    public function getIds()
    {
        $res = array();
        $array = eaccelerator_list_keys();
        foreach ($array as $key => $info) {
            $res[] = $key;
        }
        return $res;
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
        $tmp = eaccelerator_get($id);
        if (is_array($tmp)) {
            $data = $tmp[0];
            $mtime = $tmp[1];
            if (!isset($tmp[2])) {
                // because this record is only with 1.7 release
                // if old cache records are still there...
                return false;
            }
            $lifetime = $tmp[2];
            return array(
                'expire' => $mtime + $lifetime,
                'tags' => array(),
                'mtime' => $mtime
            );
        }
        return false;
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
        $tmp = eaccelerator_get($id);
        if (is_array($tmp)) {
            $data = $tmp[0];
            $mtime = $tmp[1];
            if (!isset($tmp[2])) {
                // because this record is only with 1.7 release
                // if old cache records are still there...
                return false;
            }
            $lifetime = $tmp[2];
            $newLifetime = $lifetime - (time() - $mtime) + $extraLifetime;
            if ($newLifetime <=0) {
                return false;
            }
            eaccelerator_put($id, array($data, time(), $newLifetime), $newLifetime);
            return true;
        }
        return false;
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
            'automatic_cleaning' => false,
            'tags' => false,
            'expired_read' => false,
            'priority' => false,
            'infinite_lifetime' => false,
            'get_list' => true
        );
    }

}
