<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Cache_Backend_Interface
 */
#require_once 'Zend/Cache/Backend/Interface.php';

/**
 * Zend_Cache_Backend
 */
#require_once 'Zend/Cache/Backend.php';


/**
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Backend_Memcached extends Zend_Cache_Backend implements Zend_Cache_Backend_Interface
{

    // -----------------
    // --- Constants ---
    // -----------------
    const DEFAULT_HOST       = '127.0.0.1';
    const DEFAULT_PORT       = 11211;
    const DEFAULT_PERSISTENT = true;

    const TAGS_PREFIX    = "internal_MCtag:";
    const TAGS_MASTER_ID = "internal_MCmastertags";

    // ------------------
    // --- Properties ---
    // ------------------

    /**
     * Available options
     *
     * =====> (array) servers :
     * an array of memcached server ; each memcached server is described by an associative array :
     * 'host' => (string) : the name of the memcached server
     * 'port' => (int) : the port of the memcached server
     * 'persistent' => (bool) : use or not persistent connections to this memcached server
     *
     * =====> (boolean) compression :
     * true if you want to use on-the-fly compression
     *
     * @var array available options
     */
    protected $_options = array(
        'servers' => array(array(
            'host' => Zend_Cache_Backend_Memcached::DEFAULT_HOST,
            'port' => Zend_Cache_Backend_Memcached::DEFAULT_PORT,
            'persistent' => Zend_Cache_Backend_Memcached::DEFAULT_PERSISTENT
        )),
        'compression' => false,
        'cache_dir' => null,
        'hashed_directory_level' => null,
        'hashed_directory_umask' => null,
        'file_name_prefix' => null,
    );

    /**
     * Memcache object
     *
     * @var mixed memcache object
     */
    private $_memcache = null;


    // ----------------------
    // --- Public methods ---
    // ----------------------

    /**
     * Constructor
     *
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {
        if (!extension_loaded('memcache')) {
            Zend_Cache::throwException('The memcache extension must be loaded for using this backend !');
        }
        parent::__construct($options);
        if (isset($this->_options['servers'])) {
            $value= $this->_options['servers'];
            if (isset($value['host'])) {
                // in this case, $value seems to be a simple associative array (one server only)
                $value = array(0 => $value); // let's transform it into a classical array of associative arrays
            }
            $this->setOption('servers', $value);
        }
        $this->_memcache = new Memcache;
        foreach ($this->_options['servers'] as $server) {
            if (!array_key_exists('persistent', $server)) {
                $server['persistent'] = Zend_Cache_Backend_Memcached::DEFAULT_PERSISTENT;
            }
            if (!array_key_exists('port', $server)) {
                $server['port'] = Zend_Cache_Backend_Memcached::DEFAULT_PORT;
            }
            $this->_memcache->addServer($server['host'], $server['port'], $server['persistent']);
        }
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string cached datas (or false)
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        // WARNING : $doNotTestCacheValidity is not supported !!!
        if ($doNotTestCacheValidity) {
            $this->_log("Zend_Cache_Backend_Memcached::load() : \$doNotTestCacheValidity=true is unsupported by the Memcached backend");
        }
        $tmp = $this->_memcache->get($id);
        if (is_array($tmp) && isset($tmp[0])) {
            return $tmp[0];
        } else { return $tmp; }
        return false;
    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        $tmp = $this->_memcache->get($id);
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
        if ($this->_options['compression']) {
            $flag = MEMCACHE_COMPRESSED;
        } else {
            $flag = 0;
        }
    //DEBUG ADDED TO WATCH ALL THE KEYS
    /*
$old_master_wrapper = $this->_memcache->get('master_ids');
if (! is_array($old_master_wrapper) ) {
    $old_master = array();
} else {
    $old_master = $old_master_wrapper[0];
}
$old_master[] = $id;
$this->_memcache->set('master_ids', array($old_master, time()));
    //DEBUG ADDED TO WATCH ALL THE KEYS
//*/

        if (count($tags) > 0) {
    }

        $result1 = $this->_memcache->set($id, array($data, time()), $flag, $lifetime);
    $result2  = (count($tags) == 0);

        if (count($tags) > 0) {
        foreach ($tags as $tag) {
            $this->recordTagUsage($tag);
            $tagid = self::TAGS_PREFIX.$tag;
                $old_tags = $this->_memcache->get($tagid);
            if ($old_tags === false) {
            $old_tags = array();
            }
            $old_tags[$id] = $id;
            $this->remove($tagid);
            $result2 = $this->_memcache->set($tagid, $old_tags);
        }
    }

        return $result1 && $result2;
    }

    /**
     * Save this tagId into a global array of all tags with self::TAGS_MASTER_ID as the id.
     *
     * @param $tagId string the id of the tag being used
     * @return bool true if the tag was set or was already present
     */
    private function recordTagUsage($tagId) {
    $old_tmaster_wrapper = $this->_memcache->get(self::TAGS_MASTER_ID);
    if (! is_array($old_tmaster_wrapper) ) {
        $old_tmaster = array();
    } else {
        $old_tmaster = $old_tmaster_wrapper[0];
    }
    if (in_array( $tagId, $old_tmaster) ) {
        return true;
    } else {
        //master tag list has tag ID as both key and value for speed
        $old_tmaster[$tagId] = $tagId;
        return $this->_memcache->set(self::TAGS_MASTER_ID, array($old_tmaster, time()));
    }
    }

    /**
     * Remove this tagId from a global array of all tags with self::TAGS_MASTER_ID as the id.
     *
     * @param $tagId string the id of the tag being removed
     * @return bool true if the tag was removed, false otherwise
     */
    private function removeTagUsage($tagId) {
    $tmaster_wrapper = $this->_memcache->get(self::TAGS_MASTER_ID);
    if (! is_array($tmaster_wrapper) ) {
        $old_tmaster = array();
        return false;
    } else {
        $old_tmaster = $tmaster_wrapper[0];
    }
    if (in_array( $tagId, $old_tmaster) ) {
        //master tag list has tag ID as both key and value for speed
        unset($old_tmaster[$tagId]);
        return $this->_memcache->set(self::TAGS_MASTER_ID, array($old_tmaster, time()));
    }
    return false;
    }

    /**
     * Remove a cache record
     *
     * @param string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id)
    {
        return $this->_memcache->delete($id);
    }

    /**
     * Clean some cache records
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => remove too old cache entries ($tags is not used)
     * 'matchingTag'    => remove cache entries matching all given tags
     *                     ($tags can be an array of strings or a single string)
     * 'notMatchingTag' => remove cache entries not matching one of the given tags
     *                     ($tags can be an array of strings or a single string)
     *
     * @param string $mode clean mode
     * @param array $tags array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        if ($mode==Zend_Cache::CLEANING_MODE_ALL) {
            return $this->_memcache->flush();
        }
        if ($mode==Zend_Cache::CLEANING_MODE_OLD) {
            $this->_log("Zend_Cache_Backend_Memcached::clean() : CLEANING_MODE_OLD is unsupported by the Memcached backend");
        }
        if ($mode==Zend_Cache::CLEANING_MODE_MATCHING_TAG) {
            $idList = array();
            foreach ($tags as $tag) {
                $current_idList = $this->_memcache->get(self::TAGS_PREFIX.$tag);
                if (is_array($current_idList)) {
                    $idList = array_merge($idList, $current_idList);
                }
            }
            //clean up all tags completely
            //remove tagIds from the master tag list
            foreach ($tags as $tag) {
                    $this->_memcache->delete(self::TAGS_PREFIX.$tag);
                    $this->removeTagUsage($tag);
            }

            //leave if there were no found IDs
            if( count($idList) < 1) {
                return true;
            }

            //remove the deleted IDs from any other tag references
            $masterTagWrapper = $this->_memcache->get(self::TAGS_MASTER_ID);
            $masterTagList = null;
            if (is_array($masterTagWrapper)) {
                $masterTagList = $masterTagWrapper[0];
            }
            foreach ($masterTagList as $tag) {
                $needsUpdate = false;
                $other_tagList = $this->_memcache->get(self::TAGS_PREFIX.$tag);
                if (is_array($other_tagList) ) {
                    foreach ($other_tagList as $_tagIdx => $otherRefId) {
                        if ( in_array($otherRefId, $idList)) {
                            unset($other_tagList[$_tagIdx]);
                            $needsUpdate = true;
                        }
                    }
                    if ($needsUpdate) {
                        //completely remove tags if there are no more items in their array.
                        if ( count($other_tagList) < 1) {
                            $this->_memcache->delete(self::TAGS_PREFIX.$tag);
                        } else {
                            $this->_memcache->set(self::TAGS_PREFIX.$tag, $other_tagList);
                        }
                    }
                }
            }

            foreach ($idList as $id) {
                $this->_memcache->delete($id);
            }
            return true;
        }
        if ($mode==Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG) {
            $this->_log("Zend_Cache_Backend_Memcached::clean() : tags are unsupported by the Memcached backend");
        }
    }

    /**
     * Return true if the automatic cleaning is available for the backend
     *
     * @return boolean
     */
    public function isAutomaticCleaningAvailable()
    {
        return false;
    }

}
