<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Cache
 */

class Varien_Cache_Core extends Zend_Cache_Core
{
    /**
     * Specific slab size = 1Mb minus overhead
     *
     * @var array $_specificOptions
     */
    protected $_specificOptions = ['slab_size' => 0];

    /**
     * Used to tell chunked data from ordinary
     */
    public const CODE_WORD = '{splitted}';

    /**
     * Constructor
     *
     * @throws Varien_Exception
     * @param array|Zend_Config $options Associative array of options or Zend_Config instance
     */
    public function __construct($options = [])
    {
        parent::__construct($options);
        if (!is_numeric($this->getOption('slab_size'))) {
            throw new Varien_Exception('Invalid value for the node <slab_size>. Expected to be integer.');
        }
    }

    /**
     * Returns ID of a specific chunk on the basis of data's ID
     *
     * @param string $id    Main data's ID
     * @param int    $index Particular chunk number to return ID for
     * @return string
     */
    protected function _getChunkId($id, $index)
    {
        return "{$id}[{$index}]";
    }

    /**
     * Remove saved chunks in case something gone wrong (e.g. some chunk from the chain can not be found)
     *
     * @param string $id     ID of data's info cell
     * @param int    $chunks Number of chunks to remove (basically, the number after '{splitted}|')
     */
    protected function _cleanTheMess($id, $chunks)
    {
        for ($i = 0; $i < $chunks; $i++) {
            $this->remove($this->_getChunkId($id, $i));
        }

        $this->remove($id);
    }

    /**
     * Make and return a cache id
     *
     * Checks 'cache_id_prefix' and returns new id with prefix or simply the id if null
     *
     * @param  string $id Cache id
     * @return string Cache id (with or without prefix)
     */
    protected function _id($id)
    {
        if ($id !== null) {
            $id = preg_replace('/([^a-zA-Z0-9_]{1,1})/', '_', $id);
            if (isset($this->_options['cache_id_prefix'])) {
                $id = $this->_options['cache_id_prefix'] . $id;
            }
        }
        return $id;
    }

    /**
     * Prepare tags
     *
     * @param array $tags
     * @return array
     */
    protected function _tags($tags)
    {
        foreach ($tags as $key => $tag) {
            $tags[$key] = $this->_id($tag);
        }
        return $tags;
    }

    /**
     * Save some data in a cache
     *
     * @param  mixed $data           Data to put in cache (can be another type than string if automatic_serialization is on)
     * @param  string $id             Cache id (if not set, the last cache id will be used)
     * @param  array $tags           Cache tags
     * @param bool|int $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @param  int $priority         integer between 0 (very low priority) and 10 (maximum priority) used by some particular backends
     * @return bool True if no problem
     */
    public function save($data, $id = null, $tags = [], $specificLifetime = false, $priority = 8)
    {
        $tags = $this->_tags($tags);

        if ($this->getOption('slab_size') && is_string($data) && (strlen($data) > $this->getOption('slab_size'))) {
            $dataChunks = str_split($data, $this->getOption('slab_size'));

            for ($i = 0, $cnt = count($dataChunks); $i < $cnt; $i++) {
                $chunkId = $this->_getChunkId($id, $i);

                if (!parent::save($dataChunks[$i], $chunkId, $tags, $specificLifetime, $priority)) {
                    $this->_cleanTheMess($id, $i + 1);
                    return false;
                }
            }

            $data = self::CODE_WORD . '|' . $i;
        }

        return parent::save($data, $id, $tags, $specificLifetime, $priority);
    }

    /**
     * Load data from cached, glue from several chunks if it was split upon save.
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @param  boolean $doNotUnserialize       Do not serialize (even if automatic_serialization is true) => for internal use
     * @return mixed|false Cached data
     */
    public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        $data = parent::load($id, $doNotTestCacheValidity, $doNotUnserialize);

        if (is_string($data) && (str_starts_with($data, self::CODE_WORD))) {
            // Seems we've got chunked data

            $arr = explode('|', $data);
            $chunks = $arr[1] ?? false;
            $chunkData = [];

            if ($chunks && is_numeric($chunks)) {
                for ($i = 0; $i < $chunks; $i++) {
                    $chunk = parent::load($this->_getChunkId($id, $i), $doNotTestCacheValidity, $doNotUnserialize);

                    if (false === $chunk) {
                        // Some chunk in chain was not found, we can not glue-up the data:
                        // clean the mess and return nothing

                        $this->_cleanTheMess($id, (int) $chunks);
                        return false;
                    }

                    $chunkData[] = $chunk;
                }

                return implode('', $chunkData);
            }
        }

        // Data has not been split to chunks on save
        return $data;
    }

    /**
     * Clean cache entries
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => remove too old cache entries ($tags is not used)
     * 'matchingTag'    => remove cache entries matching all given tags
     *                     ($tags can be an array of strings or a single string)
     * 'notMatchingTag' => remove cache entries not matching one of the given tags
     *                     ($tags can be an array of strings or a single string)
     * 'matchingAnyTag' => remove cache entries matching any given tags
     *                     ($tags can be an array of strings or a single string)
     *
     * @param  string       $mode
     * @param  array|string $tags
     * @throws Zend_Cache_Exception
     * @return bool True if ok
     */
    public function clean($mode = 'all', $tags = [])
    {
        $tags = $this->_tags($tags);
        return parent::clean($mode, $tags);
    }

    /**
     * Return an array of stored cache ids which match given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of matching cache ids (string)
     */
    public function getIdsMatchingTags($tags = [])
    {
        $tags = $this->_tags($tags);
        return parent::getIdsMatchingTags($tags);
    }

    /**
     * Return an array of stored cache ids which don't match given tags
     *
     * In case of multiple tags, a logical OR is made between tags
     *
     * @param array $tags array of tags
     * @return array array of not matching cache ids (string)
     */
    public function getIdsNotMatchingTags($tags = [])
    {
        $tags = $this->_tags($tags);
        return parent::getIdsNotMatchingTags($tags);
    }
}
