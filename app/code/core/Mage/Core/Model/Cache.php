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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * System cache model
 * support id and tags preffix support,
 */

class Mage_Core_Model_Cache
{
    const DEFAULT_LIFETIME  = 7200;
    const OPTIONS_CACHE_ID  = 'core_cache_options';

    /**
     * @var string
     */
    protected $_idPrefix    = '';

    /**
     * Cache frontend API
     *
     * @var Zend_Cache_Core
     */
    protected $_frontend    = null;

    /**
     * Shared memory backend models list (required TwoLevels backend model)
     *
     * @var array
     */
    protected $_shmBackends = array(
        'apc', 'memcached', 'xcache',
        'zendserver_shmem', 'zendserver_disk', 'varien_eaccelerator',
    );

    /**
     * Fefault cache backend type
     *
     * @var string
     */
    protected $_defaultBackend = 'File';

    /**
     * Default iotions for default backend
     *
     * @var array
     */
    protected $_defaultBackendOptions = array(
        'hashed_directory_level'    => 0,
        'hashed_directory_umask'    => 0777,
        'file_name_prefix'          => 'mage',
    );

    /**
     * List of allowed cache options
     *
     * @var array
     */
    protected $_allowedCacheOptions = null;

    /**
     * Class constructor. Initialize cache instance based on options
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->_defaultBackendOptions['cache_dir'] = Mage::getBaseDir('cache');
        /**
         * Initialize id prefix
         */
        $this->_idPrefix = isset($options['id_prefix']) ? $options['id_prefix'] : '';
        if (!$this->_idPrefix && isset($options['prefix'])) {
            $this->_idPrefix = $options['prefix'];
        }

        $backend    = $this->_getBackendOptions($options);
        $frontend   = $this->_getFrontendOptions($options);

        if (empty($this->_idPrefix)) {
            $this->_idPrefix = substr(md5(Mage::getConfig()->getOptions()->getEtcDir()), 0, 5).'-';
        }

        $this->_frontend = Zend_Cache::factory('Core', $backend['type'], $frontend, $backend['options'],
            false, true, true
        );
    }

    /**
     * Get cache backend options. Result array contain backend type ('type' key) and backend options ('options')
     *
     * @param   array $cacheOptions
     * @return  array
     */
    protected function _getBackendOptions(array $cacheOptions)
    {
        $enable2levels = false;
        $type   = isset($cacheOptions['backend']) ? $cacheOptions['backend'] : $this->_defaultBackend;
        if (isset($cacheOptions['backend_options']) && is_array($cacheOptions['backend_options'])) {
            $options = $cacheOptions['backend_options'];
        } else {
            $options = array();
        }

        $type = strtolower($type);
        $backendType = false;
        switch ($type) {
            case 'sqlite':
                if (extension_loaded('sqlite') && isset($options['cache_db_complete_path'])) {
                    $backendType = 'Sqlite';
                }
                break;
            case 'memcached':
                if (extension_loaded('memcache')) {
                    if (isset($cacheOptions['memcached'])) {
                        $options = $cacheOptions['memcached'];
                    }
                    $enable2levels = true;
                    $backendType = 'Memcached';
                }
                break;
            case 'apc':
                if (extension_loaded('apc') && ini_get('apc.enabled')) {
                    $enable2levels = true;
                    $backendType = 'Apc';
                }
                break;
            case 'xcache':
                if (extension_loaded('xcache')) {
                    $enable2levels = true;
                    $backendType = 'Xcache';
                }
                break;
            case 'eaccelerator':
            case 'varien_cache_backend_eaccelerator':
                if (extension_loaded('eaccelerator') && ini_get('eaccelerator.enable')) {
                    $enable2levels = true;
                    $backendType = 'Varien_Cache_Backend_Eaccelerator';
                }
                break;
            case 'database':
                $backendType = 'Varien_Cache_Backend_Database';
                $options = $this->getDbAdapterOptions();
                break;
        }

        if (!$backendType) {
            $backendType = $this->_defaultBackend;
            foreach ($this->_defaultBackendOptions as $option => $value) {
                if (!array_key_exists($option, $options)) {
                    $options[$option] = $value;
                }
            }
        }

        $backendOptions = array('type' => $backendType, 'options' => $options);
        if ($enable2levels) {
            $backendOptions = $this->_getTwoLevelsBackendOptions($backendOptions, $cacheOptions);
        }
        return $backendOptions;
    }

    /**
     * Get options for database backend type
     *
     * @return array
     */
    protected function getDbAdapterOptions()
    {
        $options['adapter_callback'] = array($this, 'getDbAdapter');
        $options['data_table']  = Mage::getSingleton('core/resource')->getTableName('core/cache');
        $options['tags_table']  = Mage::getSingleton('core/resource')->getTableName('core/cache_tag');
        return $options;
    }

    /**
     * Initialize two levels backend model options
     *
     * @param array $fastOptions fast level backend type and options
     * @param array $cacheOptions all cache options
     * @return array
     */
    protected function _getTwoLevelsBackendOptions($fastOptions, $cacheOptions)
    {
        $options = array();
        $options['fast_backend']                = $fastOptions['type'];
        $options['fast_backend_options']        = $fastOptions['options'];
        $options['fast_backend_custom_naming']  = true;
        $options['fast_backend_autoload']       = true;
        $options['slow_backend_custom_naming']  = true;
        $options['slow_backend_autoload']       = true;

        if (isset($cacheOptions['slow_backend'])) {
            $options['slow_backend'] = $cacheOptions['slow_backend'];
        } else {
            $options['slow_backend'] = $this->_defaultBackend;
        }
        if (isset($cacheOptions['slow_backend_options'])) {
            $options['slow_backend_options'] = $cacheOptions['slow_backend_options'];
        } else {
            $options['slow_backend_options'] = $this->_defaultBackendOptions;
        }
        if ($options['slow_backend'] == 'database') {
            $options['slow_backend'] = 'Varien_Cache_Backend_Database';
            $options['slow_backend_options'] = $this->getDbAdapterOptions();
        }

        $backend = array(
            'type'      => 'TwoLevels',
            'options'   => $options
        );
        return $backend;
    }

    /**
     * Get options of cache frontend (options of Zend_Cache_Core)
     *
     * @param   array $cacheOptions
     * @return  array
     */
    protected function _getFrontendOptions(array $cacheOptions)
    {
        $options = isset($cacheOptions['frontend_options']) ? $cacheOptions['frontend_options'] : array();
        if (!array_key_exists('caching', $options)) {
            $options['caching'] = true;
        }
        if (!array_key_exists('lifetime', $options)) {
            $options['lifetime'] = isset($cacheOptions['lifetime']) ? $cacheOptions['lifetime'] : self::DEFAULT_LIFETIME;
        }
        if (!array_key_exists('automatic_cleaning_factor', $options)) {
            $options['automatic_cleaning_factor'] = 0;
        }
        return $options;
    }

    /**
     * Prepare unified valid identifier with preffix
     *
     * @param   string $id
     * @return  string
     */
    protected function _id($id)
    {
        if ($id) {
            $id = strtoupper($this->_idPrefix.$id);
            $id = preg_replace('/([^a-zA-Z0-9_]{1,1})/', '_', $id);
        }
        return $id;
    }

    /**
     * Prepare cache tags.
     *
     * @param   array $tags
     * @return  array
     */
    protected function _tags($tags = array())
    {
        foreach ($tags as $key => $value) {
            $tags[$key] = $this->_id($value);
        }
        return $tags;
    }

    /**
     * Get cache frontend API object
     *
     * @return Zend_Cache_Core
     */
    public function getFrontend()
    {
        return $this->_frontend;
    }

    /**
     * Load data from cache by id
     *
     * @param   string $id
     * @return  string
     */
    public function load($id)
    {
        return $this->_frontend->load($this->_id($id));
    }

    /**
     * Save data
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param int $lifeTime
     * @return bool
     */
    public function save($data, $id, $tags=array(), $lifeTime=null)
    {
        /**
         * Add global magento cache tag to all cached data exclude config cache
         */
        if (!in_array(Mage_Core_Model_Config::CACHE_TAG, $tags)) {
            $tags[] = Mage_Core_Model_App::CACHE_TAG;
        }
        return $this->_frontend->save((string)$data, $this->_id($id), $this->_tags($tags), $lifeTime);
    }

    /**
     * Remove cached data by identifier
     *
     * @param   string $id
     * @return  bool
     */
    public function remove($id)
    {
        return $this->_frontend->remove($this->_id($id));
    }

    /**
     * Clean cached data by specific tag
     *
     * @param   array $tags
     * @return  bool
     */
    public function clean($tags=array())
    {
        $mode = Zend_Cache::CLEANING_MODE_MATCHING_TAG;
        if (!empty($tags)) {
            if (!is_array($tags)) {
                $tags = array($tags);
            }
            $res = $this->_frontend->clean($mode, $this->_tags($tags));
        } else {
            $res = $this->_frontend->clean($mode, array(Mage_Core_Model_App::CACHE_TAG));
            $res = $res && $this->_frontend->clean($mode, array(Mage_Core_Model_Config::CACHE_TAG));
        }
        return $res;
    }

    /**
     * Get adapter for database cache backend model
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    /**
     * Get cache resource model
     *
     * @return Mage_Core_Model_Mysql4_Cache
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('core/cache');
    }

    /**
     * Initialize cache options
     *
     * @return Mage_Core_Model_Cache
     */
    protected function _initOptions()
    {
        $options = $this->load(self::OPTIONS_CACHE_ID);
        if ($options === false) {
            $options = $this->_getResource()->getAllOptions();
            if (is_array($options)) {
                $this->_allowedCacheOptions = $options;
                $this->save(serialize($this->_allowedCacheOptions), self::OPTIONS_CACHE_ID);
            } else {
                $this->_allowedCacheOptions = array();
            }
        } else {
            $this->_allowedCacheOptions = unserialize($options);
        }
        return $this;
    }

    /**
     * Check if cache can be used for specific data group
     *
     * @param string $cacheType
     * @return bool
     */
    public function canUse($groupCode)
    {
        if (is_null($this->_allowedCacheOptions)) {
            $this->_initOptions();
        }

        if (empty($groupCode)) {
            return $this->_allowedCacheOptions;
        } else {
            if (isset($this->_allowedCacheOptions[$groupCode])) {
                return (bool)$this->_allowedCacheOptions[$groupCode];
            } else {
                return false;
            }
        }
    }

    /**
     * Save cache usage options
     *
     * @param array $options
     * @return Mage_Core_Model_Cache
     */
    public function saveOptions($options)
    {
        $this->remove(self::OPTIONS_CACHE_ID);
        $options = $this->_getResource()->saveAllOptions($options);
        return $this;
    }
}
