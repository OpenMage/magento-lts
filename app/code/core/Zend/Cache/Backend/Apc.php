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
require_once 'Zend/Cache/Backend/Interface.php';

/**
 * Zend_Cache_Backend
 */
require_once 'Zend/Cache/Backend.php';


/**
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Backend_Apc extends Zend_Cache_Backend implements Zend_Cache_Backend_Interface
{
    const TAGS_PREFIX    = "internal_APCtag:";
    const TAGS_MASTER_ID = "internal_APCmastertags";

    /**
     * Available options
     *
     * @var array available options
     */
    protected $_options = array(
        'cache_prefix'  => '',
        'tag_prefix'    => 'APC_TAG_',
        'tag_storage'   => 'APC_ALL_TAGS'
    );

    /**
     * Constructor
     *
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {
        if (!extension_loaded('apc')) {
            Zend_Cache::throwException('The apc extension must be loaded for using this backend !');
        }
        parent::__construct($options);
    }

    /**
     * Get tag storage cache identifier
     *
     * @return string
     */
    protected function _getTagStorageId()
    {
        return $this->_prepareId($this->_options['tag_storage']);
    }

    /**
     * Get all cache ids identifier
     *
     * @return string
     */
    protected function _getCachePrefix()
    {
        return $this->_options['cache_prefix'];
    }

    /**
     * Prepare cache identifier
     *
     * @param   string $origId
     * @return  string
     */
    protected function _prepareId($origId)
    {
        return $this->_getCachePrefix() . $origId;
    }

    /**
     * Parepare tag identifier
     *
     * @param   string $tagId
     * @return  string
     */
    protected function _prepareTagId($tagId)
    {
        return $this->_prepareId($this->_options['tag_prefix'] . $tagId);
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * WARNING $doNotTestCacheValidity=true is unsupported by the Apc backend
     *
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string cached datas (or false)
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        $id = $this->_prepareId($id);

        if ($doNotTestCacheValidity) {
            $this->_log("Zend_Cache_Backend_Apc::load() : \$doNotTestCacheValidity=true is unsupported by the Apc backend");
        }

        $tmp = apc_fetch($id);
        if (is_array($tmp) && isset($tmp[0])) {
            return $tmp[0];
        } else {
            return $tmp;
        }
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
        $id = $this->_prepareId($id);
        $tmp = apc_fetch($id);
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
        $id       = $this->_prepareId($id);
        $lifetime = $this->getLifetime($specificLifetime);
        $result1  = apc_store($id, array($data, time()), $lifetime);
        $result2  = true;

        if (count($tags) > 0) {
            foreach ($tags as $tag) {
                $this->recordTagUsage($tag);

                $tagid = $this->_prepareTagId($tag);
                $old_tags = apc_fetch($tagid);
                if ($old_tags === false) {
                    $old_tags = array();
                }
                $old_tags[$id] = $id;
                $this->remove($tagid);
                $result2 = apc_store($tagid, $old_tags);
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
    private function recordTagUsage($tagId)
    {
        $old_tmaster_wrapper = apc_fetch($this->_getTagStorageId());

        if (!is_array($old_tmaster_wrapper)) {
            $old_tmaster = array();
        } else {
            $old_tmaster = $old_tmaster_wrapper[0];
        }

        if (in_array($tagId, $old_tmaster)) {
            return true;
        } else {
            //master tag list has tag ID as both key and value for speed
            $old_tmaster[$tagId] = $tagId;
            return apc_store($this->_getTagStorageId(), array($old_tmaster, time()));
        }
    }

	/**
	 * Remove this tagId from a global array of all tags with self::TAGS_MASTER_ID as the id.
	 *
	 * @param $tagId string the id of the tag being removed
	 * @return bool true if the tag was removed, false otherwise
	 */
    private function removeTagUsage($tagId)
    {
        $tmaster_wrapper = apc_fetch($this->_getTagStorageId());
        if (!is_array($tmaster_wrapper)) {
            $old_tmaster = array();
            return false;
        } else {
            $old_tmaster = $tmaster_wrapper[0];
        }

        if (in_array($tagId, $old_tmaster)) {
            //master tag list has tag ID as both key and value for speed
            unset($old_tmaster[$tagId]);
            return apc_store($this->_getTagStorageId(), array($old_tmaster, time()));
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
        return apc_delete($id);
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
            return apc_clear_cache('user');
        }

        if ($mode==Zend_Cache::CLEANING_MODE_OLD) {
            $this->_log("Zend_Cache_Backend_Apc::clean() : CLEANING_MODE_OLD is unsupported by the Apc backend");
        }

        if ($mode==Zend_Cache::CLEANING_MODE_MATCHING_TAG) {
            $idList = array();
            foreach ($tags as $tag) {
                $tagId = $this->_prepareTagId($tag);
                $current_idList = apc_fetch($tagId);
                if (is_array($current_idList)) {
                    $idList = array_merge($idList, $current_idList);
                }
            }

            //clean up all tags completely
            //remove tagIds from the master tag list
            foreach ($tags as $tag) {
                $tagId = $this->_prepareTagId($tag);
                apc_delete($tagId);
                $this->removeTagUsage($tag);
            }

            //leave if there were no found IDs
            if( count($idList) < 1) {
                return true;
            }

            //remove the deleted IDs from any other tag references
            $masterTagWrapper = apc_fetch($this->_getTagStorageId());
            $masterTagList = null;
            if (is_array($masterTagWrapper)) {
                $masterTagList = $masterTagWrapper[0];
            }

            foreach ($masterTagList as $tag) {
                $needsUpdate = false;
                $tagId = $this->_prepareTagId($tag);
                $other_tagList = apc_fetch($tagId);

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
                            apc_delete($tagId);
                        } else {
                            apc_store($tagId, $other_tagList);
                        }
                    }
                }
            }

            foreach ($idList as $id) {
                apc_delete($id);
            }
            return true;
        }

        if ($mode==Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG) {
            $this->_log("Zend_Cache_Backend_Apc::clean() : tags are unsupported by the Apc backend");
        }
        return $this;
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
