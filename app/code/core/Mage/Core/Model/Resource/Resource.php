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
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Core Resource Resource Model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Resource extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Database versions
     *
     * @var array
     */
    protected static $_versions        = null;

    /**
     * Resource data versions cache array
     *
     * @var array
     */
    protected static $_dataVersions    = null;

    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('core/resource', 'store_id');
    }

    /**
     * Fill static versions arrays.
     * This routine fetches Db and Data versions of at once to optimize sql requests. However, when upgrading, it's
     * possible that 'data' column will be created only after all Db installs are passed. So $neededType contains
     * information on main purpose of calling this routine, and even when 'data' column is absent - it won't require
     * reissuing new sql just to get 'db' version of module.
     *
     * @param string $needType Can be 'db' or 'data'
     * @return Mage_Core_Model_Resource_Resource
     */
    protected function _loadVersionData($needType)
    {
        if ((($needType == 'db') && is_null(self::$_versions))
            || (($needType == 'data') && is_null(self::$_dataVersions))) {
            self::$_versions     = array(); // Db version column always exists
            self::$_dataVersions = null; // Data version array will be filled only if Data column exist

            if ($this->_getReadAdapter()->isTableExists($this->getMainTable())) {
                $select = $this->_getReadAdapter()->select()
                    ->from($this->getMainTable(), '*');
                $rowset = $this->_getReadAdapter()->fetchAll($select);
                foreach ($rowset as $row) {
                    self::$_versions[$row['code']] = $row['version'];
                    if (array_key_exists('data_version', $row)) {
                        if (is_null(self::$_dataVersions)) {
                            self::$_dataVersions = array();
                        }
                        self::$_dataVersions[$row['code']] = $row['data_version'];
                    }
                }
            }
        }

        return $this;
    }


    /**
     * Get Module version from DB
     *
     * @param string $resName
     * @return bool|string
     */
    public function getDbVersion($resName)
    {
        if (!$this->_getReadAdapter()) {
            return false;
        }
        $this->_loadVersionData('db');
        return isset(self::$_versions[$resName]) ? self::$_versions[$resName] : false;
    }

    /**
     * Set module version into DB
     *
     * @param string $resName
     * @param string $version
     * @return int
     */
    public function setDbVersion($resName, $version)
    {
        $dbModuleInfo = array(
            'code'    => $resName,
            'version' => $version,
        );

        if ($this->getDbVersion($resName)) {
            self::$_versions[$resName] = $version;
            return $this->_getWriteAdapter()->update($this->getMainTable(),
                    $dbModuleInfo,
                    array('code = ?' => $resName));
        } else {
            self::$_versions[$resName] = $version;
            return $this->_getWriteAdapter()->insert($this->getMainTable(), $dbModuleInfo);
        }
    }

    /**
     * Get resource data version
     *
     * @param string $resName
     * @return string|false
     */
    public function getDataVersion($resName)
    {
        if (!$this->_getReadAdapter()) {
            return false;
        }

        $this->_loadVersionData('data');

        return isset(self::$_dataVersions[$resName]) ? self::$_dataVersions[$resName] : false;
    }

    /**
     * Specify resource data version
     *
     * @param string $resName
     * @param string $version
     * @return Mage_Core_Model_Resource_Resource
     */
    public function setDataVersion($resName, $version)
    {
        $data = array(
            'code'          => $resName,
            'data_version'  => $version
        );

        if ($this->getDbVersion($resName) || $this->getDataVersion($resName)) {
            self::$_dataVersions[$resName] = $version;
            $this->_getWriteAdapter()->update($this->getMainTable(), $data, array('code = ?' => $resName));
        } else {
            self::$_dataVersions[$resName] = $version;
            $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        }
        return $this;
    }
}
