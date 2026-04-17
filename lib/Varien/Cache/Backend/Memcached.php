<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Cache
 */

/**
 * Class Varien_Cache_Backend_Memcached
 *
 * @deprecated after 1.7.0.2
 */
class Varien_Cache_Backend_Memcached extends Zend_Cache_Backend_Memcached implements Zend_Cache_Backend_ExtendedInterface
{
    /**
     * Maximum chunk of data that could be saved in one memcache cell (1 MiB)
     */
    public const DEFAULT_SLAB_SIZE = 1048576;

    /**
     * Used to tell chunked data from ordinary
     */
    public const CODE_WORD = '{splitted}';

    /**
     * Constructor
     *
     * @throws Varien_Exception
     * @throws Zend_Cache_Exception
     * @see Zend_Cache_Backend_Memcached::__construct()
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (!isset($options['slab_size']) || !is_numeric($options['slab_size'])) {
            if (isset($options['slab_size'])) {
                throw new Varien_Exception('Invalid value for the node <slab_size>. Expected to be positive integer.');
            }

            $this->_options['slab_size'] = self::DEFAULT_SLAB_SIZE;
        } else {
            $this->_options['slab_size'] = $options['slab_size'];
        }
    }

    /**
     * Returns ID of a specific chunk on the basis of data's ID
     *
     * @param  string $id    Main data's ID
     * @param  int    $index Particular chunk number to return ID for
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
        for ($index = 0; $index < $chunks; $index++) {
            $this->remove($this->_getChunkId($id, $index));
        }

        $this->remove($id);
    }

    /**
     * Save data to memcached, split it into chunks if data size is bigger than memcached slab size.
     *
     * @param  string    $data
     * @param  string    $id
     * @param  array     $tags
     * @param  false|int $specificLifetime
     * @return bool
     * @see Zend_Cache_Backend_Memcached::save()
     */
    public function save($data, $id, $tags = [], $specificLifetime = false)
    {
        if (is_string($data) && (strlen($data) > $this->_options['slab_size'])) {
            $dataChunks = str_split($data, $this->_options['slab_size']);

            for ($index = 0, $cnt = count($dataChunks); $index < $cnt; $index++) {
                $chunkId = $this->_getChunkId($id, $index);

                if (!parent::save($dataChunks[$index], $chunkId, $tags, $specificLifetime)) {
                    $this->_cleanTheMess($id, $index + 1);
                    return false;
                }
            }

            $data = self::CODE_WORD . '|' . $index;
        }

        return parent::save($data, $id, $tags, $specificLifetime);
    }

    /**
     * Load data from memcached, glue from several chunks if it was split upon save.
     *
     * @param  string            $id
     * @param  bool              $doNotTestCacheValidity
     * @return bool|false|string
     * @see Zend_Cache_Backend_Memcached::load()
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        $data = parent::load($id, $doNotTestCacheValidity);

        if (is_string($data) && (str_starts_with($data, self::CODE_WORD))) {
            // Seems we've got chunked data

            $arr = explode('|', $data);
            $chunks = $arr[1] ?? false;
            $chunkData = [];

            if ($chunks && is_numeric($chunks)) {
                for ($index = 0; $index < $chunks; $index++) {
                    $chunk = parent::load($this->_getChunkId($id, $index), $doNotTestCacheValidity);

                    if (false === $chunk) {
                        // Some chunk in chain was not found, we can not glue-up the data:
                        // clean the mess and return nothing

                        $this->_cleanTheMess($id, $chunks);
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
}
