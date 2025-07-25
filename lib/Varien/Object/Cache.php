<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Object
 */

/**
 * Object Cache
 *
 * Stores objects for reuse, cleanup and to avoid circular references
 *
 */
class Varien_Object_Cache
{
    /**
     * Singleton instance
     *
     * @var Varien_Object_Cache|null
     */
    protected static $_instance;

    /**
     * Running object index for anonymous objects
     *
     * @var int
     */
    protected $_idx = 0;

    /**
     * Array of objects
     *
     * @var array of objects
     */
    protected $_objects = [];

    /**
     * SPL object hashes
     *
     * @var array
     */
    protected $_hashes = [];

    /**
     * SPL hashes by object
     *
     * @var array
     */
    protected $_objectHashes = [];

    /**
     * Objects by tags for cleanup
     *
     * @var array 2D
     */
    protected $_tags = [];

    /**
     * Tags by objects
     *
     * @var array 2D
     */
    protected $_objectTags = [];

    /**
     * References to objects
     *
     * @var array
     */
    protected $_references = [];

    /**
     * References by object
     *
     * @var array 2D
     */
    protected $_objectReferences = [];

    /**
     * @var array
     */
    protected $_referencesByObject = [];

    /**
     * Debug data such as backtrace per class
     *
     * @var array
     */
    protected $_debug = [];

    /**
     * Singleton factory
     *
     * @return Varien_Object_Cache
     */
    public static function singleton()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Load an object from registry
     *
     * @param string|object $idx
     * @param object $default
     * @return object
     */
    public function load($idx, $default = null)
    {
        if (isset($this->_references[$idx])) {
            $idx = $this->_references[$idx];
        }
        return $this->_objects[$idx] ?? $default;
    }

    /**
     * Save an object entry
     *
     * @param object $object
     * @param string $idx
     * @param array|string $tags
     * @return string|false
     */
    public function save($object, $idx = null, $tags = null)
    {
        //Varien_Profiler::start('OBJECT_SAVE');
        if (!is_object($object)) {
            return false;
        }

        $hash = spl_object_hash($object);
        if (!is_null($idx) && strpos($idx, '{')) {
            $idx = str_replace('{hash}', $hash, $idx);
        }
        if (isset($this->_hashes[$hash])) {
            //throw new Exception('test');
            if (!is_null($idx)) {
                $this->_references[$idx] = $this->_hashes[$hash];
            }
            return $this->_hashes[$hash];
        }

        if (is_null($idx)) {
            $idx = '#' . (++$this->_idx);
        }

        if (isset($this->_objects[$idx])) {
            throw new Varien_Exception('Object already exists in registry (' . $idx . '). Old object class: ' . $this->_objects[$idx]::class . ', new object class: ' . $object::class);
        }

        $this->_objects[$idx] = $object;

        $this->_hashes[$hash] = $idx;
        $this->_objectHashes[$idx] = $hash;

        if (is_string($tags)) {
            $this->_tags[$tags][$idx] = true;
            $this->_objectTags[$idx][$tags] = true;
        } elseif (is_array($tags)) {
            foreach ($tags as $t) {
                $this->_tags[$t][$idx] = true;
                $this->_objectTags[$idx][$t] = true;
            }
        }
        //Varien_Profiler::stop('OBJECT_SAVE');

        return $idx;
    }

    /**
     * Add a reference to an object
     *
     * @param string|array $refName
     * @param string $idx
     * @return bool
     */
    public function reference($refName, $idx)
    {
        if (is_array($refName)) {
            foreach ($refName as $ref) {
                $this->reference($ref, $idx);
            }
            return false;
        }

        if (isset($this->_references[$refName])) {
            throw new Varien_Exception('The reference already exists: ' . $refName . '. New index: ' . $idx . ', old index: ' . $this->_references[$refName]);
        }
        $this->_references[$refName] = $idx;
        $this->_objectReferences[$idx][$refName] = true;

        return true;
    }

    /**
     * Delete an object from registry
     *
     * @param string|object $idx
     * @return bool
     */
    public function delete($idx)
    {
        //Varien_Profiler::start("OBJECT_DELETE");
        if (is_object($idx)) {
            $idx = $this->find($idx);
            if (false === $idx) {
                //Varien_Profiler::stop("OBJECT_DELETE");
                return false;
            }
            unset($this->_objects[$idx]);
            //Varien_Profiler::stop("OBJECT_DELETE");
            return false;
        } elseif (!isset($this->_objects[$idx])) {
            //Varien_Profiler::stop("OBJECT_DELETE");
            return false;
        }

        unset($this->_objects[$idx]);

        unset($this->_hashes[$this->_objectHashes[$idx]], $this->_objectHashes[$idx]);

        if (isset($this->_objectTags[$idx])) {
            foreach ($this->_objectTags[$idx] as $t => $dummy) {
                unset($this->_tags[$t][$idx]);
            }
            unset($this->_objectTags[$idx]);
        }

        if (isset($this->_objectReferences[$idx])) {
            foreach (array_keys($this->_references) as $r) {
                unset($this->_references[$r]);
            }
            unset($this->_objectReferences[$idx]);
        }
        //Varien_Profiler::stop("OBJECT_DELETE");

        return true;
    }

    /**
     * Cleanup by class name for objects of subclasses too
     *
     * @param string $class
     */
    public function deleteByClass($class)
    {
        foreach ($this->_objects as $idx => $object) {
            if ($object instanceof $class) {
                $this->delete($idx);
            }
        }
    }

    /**
     * Cleanup objects by tags
     *
     * @param array|string $tags
     */
    public function deleteByTags($tags)
    {
        if (is_string($tags)) {
            $tags = [$tags];
        }
        foreach ($tags as $t) {
            foreach ($this->_tags[$t] as $idx => $dummy) {
                $this->delete($idx);
            }
        }
        return true;
    }

    /**
     * Check whether object id exists in registry
     *
     * @param string $idx
     * @return bool
     */
    public function has($idx)
    {
        return isset($this->_objects[$idx]) || isset($this->_references[$idx]);
    }

    /**
     * Find an object id
     *
     * @param object $object
     * @return string|bool
     */
    public function find($object)
    {
        foreach ($this->_objects as $idx => $obj) {
            if ($object === $obj) {
                return $idx;
            }
        }
        return false;
    }

    public function findByIds($ids)
    {
        $objects = [];
        foreach ($this->_objects as $idx => $obj) {
            if (in_array($idx, $ids)) {
                $objects[$idx] = $obj;
            }
        }
        return $objects;
    }

    public function findByHash($hash)
    {
        return isset($this->_hashes[$hash]) ? $this->_objects[$this->_hashes[$hash]] : null;
    }

    /**
     * Find objects by tags
     *
     * @param array|string $tags
     * @return array
     */
    public function findByTags($tags)
    {
        if (is_string($tags)) {
            $tags = [$tags];
        }
        $objects = [];
        foreach ($tags as $t) {
            foreach ($this->_tags[$t] as $idx => $dummy) {
                if (isset($objects[$idx])) {
                    continue;
                }
                $objects[$idx] = $this->load($idx);
            }
        }
        return $objects;
    }

    /**
     * Find by class name for objects of subclasses too
     *
     * @param string $class
     */
    public function findByClass($class)
    {
        $objects = [];
        foreach ($this->_objects as $idx => $object) {
            if ($object instanceof $class) {
                $objects[$idx] = $object;
            }
        }
        return $objects;
    }

    public function debug($idx, $object = null)
    {
        $bt = debug_backtrace();
        $debug = [];
        foreach ($bt as $i => $step) {
            $debug[$i] = [
                'file'     => $step['file'] ?? null,
                'line'     => $step['line'] ?? null,
                'function' => $step['function'],
            ];
        }
        $this->_debug[$idx] = $debug;
    }

    /**
     * Return debug information by ids
     *
     * @param array|integer $ids
     * @return array
     */
    public function debugByIds($ids)
    {
        if (is_string($ids)) {
            $ids = [$ids];
        }
        $debug = [];
        foreach ($ids as $idx) {
            $debug[$idx] = $this->_debug[$idx];
        }
        return $debug;
    }

    /**
     * Get all objects
     *
     * @return array
     */
    public function getAllObjects()
    {
        return $this->_objects;
    }

    /**
     * Get all tags
     *
     * @return array
     */
    public function getAllTags()
    {
        return $this->_tags;
    }

    /**
     * Get all tags by object
     *
     * @return array
     */
    public function getAllTagsByObject()
    {
        return $this->_objectTags;
    }

    /**
     * Get all references
     *
     * @return array
     */
    public function getAllReferences()
    {
        return $this->_references;
    }

    /**
     * Get all references by object
     *
     * @return array
     */
    public function getAllReferencesByObject()
    {
        return $this->_referencesByObject;
    }
}
