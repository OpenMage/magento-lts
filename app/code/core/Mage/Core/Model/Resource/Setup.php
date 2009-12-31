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
 * Resource setup model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Setup
{
    const DEFAULT_SETUP_CONNECTION = 'core_setup';
    const VERSION_COMPARE_EQUAL   = 0;
    const VERSION_COMPARE_LOWER   = -1;
    const VERSION_COMPARE_GREATER = 1;

    protected $_resourceName;
    protected $_resourceConfig;
    protected $_connectionConfig;
    protected $_moduleConfig;

    /**
     * Setup Connection
     *
     * @var Varien_Db_Adapter_Pdo_Mysql
     */
    protected $_conn;
    protected $_tables = array();
    protected $_setupCache = array();

    protected static $_hadUpdates;

    public function __construct($resourceName)
    {
        $config = Mage::getConfig();
        $this->_resourceName = $resourceName;
        $this->_resourceConfig = $config->getResourceConfig($resourceName);
        $connection = $config->getResourceConnectionConfig($resourceName);
        if ($connection) {
            $this->_connectionConfig = $connection;
        } else {
            $this->_connectionConfig = $config->getResourceConnectionConfig(self::DEFAULT_SETUP_CONNECTION);
        }

        $modName = (string)$this->_resourceConfig->setup->module;
        $this->_moduleConfig = $config->getModuleConfig($modName);
        $this->_conn = Mage::getSingleton('core/resource')->getConnection($this->_resourceName);
    }

    /**
     * get Connection
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function getConnection()
    {
        return $this->_conn;
    }

    public function setTable($tableName, $realTableName)
    {
        $this->_tables[$tableName] = $realTableName;
        return $this;
    }

    /**
     * Get table name
     *
     * @param string $tableName
     * @return string
     */
    public function getTable($tableName) {
        if (!isset($this->_tables[$tableName])) {
            $this->_tables[$tableName] = Mage::getSingleton('core/resource')->getTableName($tableName);
        }
        return $this->_tables[$tableName];
    }

    /**
     * Apply database updates whenever needed
     *
     * @return  boolean
     */
    static public function applyAllUpdates()
    {
        Mage::app()->setUpdateMode(true);
        $res = Mage::getSingleton('core/resource');
        /*
        if ($res->getAutoUpdate() == Mage_Core_Model_Resource::AUTO_UPDATE_NEVER) {
            return true;
        }
        */

        self::$_hadUpdates = false;

        $resources = Mage::getConfig()->getNode('global/resources')->children();
        foreach ($resources as $resName=>$resource) {
            if (!$resource->setup) {
                continue;
            }
            $className = __CLASS__;
            if (isset($resource->setup->class)) {
                $className = $resource->setup->getClassName();
            }
            $setupClass = new $className($resName);
            $setupClass->applyUpdates();
        }
/*
        if (self::$_hadUpdates) {
            if ($res->getAutoUpdate() == Mage_Core_Model_Resource::AUTO_UPDATE_ONCE) {
                $res->setAutoUpdate(Mage_Core_Model_Resource::AUTO_UPDATE_NEVER);
            }
        }
*/
        Mage::app()->setUpdateMode(false);
        return true;
    }

    public function applyUpdates()
    {
        $dbVer = Mage::getResourceModel('core/resource')->getDbVersion($this->_resourceName);
        $configVer = (string)$this->_moduleConfig->version;
        // Module is installed
        if ($dbVer!==false) {
             $status = version_compare($configVer, $dbVer);
             switch ($status) {
                case self::VERSION_COMPARE_LOWER:
                    $this->_rollbackResourceDb($configVer, $dbVer);
                    break;
                case self::VERSION_COMPARE_GREATER:
                    $this->_upgradeResourceDb($dbVer, $configVer);
                    break;
                default:
                    return true;
                    break;
             }
        }
        // Module not installed
        elseif ($configVer) {
            $this->_installResourceDb($configVer);
        }
    }

    /**
     * Install resource
     *
     * @param     string $version
     * @return    boolean
     */
    protected function _installResourceDb($newVersion)
    {
        $oldVersion = $this->_modifyResourceDb('install', '', $newVersion);
        $this->_modifyResourceDb('upgrade', $oldVersion, $newVersion);
    }

    /**
     * Upgrade DB for new resource version
     *
     * @param string $oldVersion
     * @param string $newVersion
     */
    protected function _upgradeResourceDb($oldVersion, $newVersion)
    {
        $this->_modifyResourceDb('upgrade', $oldVersion, $newVersion);
    }

    /**
     * Roll back resource
     *
     * @param     string $newVersion
     * @return    bool
     */

    protected function _rollbackResourceDb($newVersion, $oldVersion)
    {
        $this->_modifyResourceDb('rollback', $newVersion, $oldVersion);
    }

    /**
     * Uninstall resource
     *
     * @param     $version existing resource version
     * @return    bool
     */

    protected function _uninstallResourceDb($version)
    {
        $this->_modifyResourceDb('uninstall', $version, '');
    }

    /**
     * Run module modification sql
     *
     * @param     string $actionType install|upgrade|uninstall
     * @param     string $fromVersion
     * @param     string $toVersion
     * @return    bool
     */

    protected function _modifyResourceDb($actionType, $fromVersion, $toVersion)
    {
        $resModel = (string)$this->_connectionConfig->model;
        $modName = (string)$this->_moduleConfig[0]->getName();

        $sqlFilesDir = Mage::getModuleDir('sql', $modName).DS.$this->_resourceName;
        if (!is_dir($sqlFilesDir) || !is_readable($sqlFilesDir)) {
            Mage::getResourceModel('core/resource')->setDbVersion($this->_resourceName, $toVersion);
            return $toVersion;
        }
        // Read resource files
        $arrAvailableFiles = array();
        $sqlDir = dir($sqlFilesDir);
        while (false !== ($sqlFile = $sqlDir->read())) {
            $matches = array();
            if (preg_match('#^'.$resModel.'-'.$actionType.'-(.*)\.(sql|php)$#i', $sqlFile, $matches)) {
                $arrAvailableFiles[$matches[1]] = $sqlFile;
            }
        }
        $sqlDir->close();
        if (empty($arrAvailableFiles)) {
            Mage::getResourceModel('core/resource')->setDbVersion($this->_resourceName, $toVersion);
            return $toVersion;
        }

        // Get SQL files name
        $arrModifyFiles = $this->_getModifySqlFiles($actionType, $fromVersion, $toVersion, $arrAvailableFiles);
        if (empty($arrModifyFiles)) {
            Mage::getResourceModel('core/resource')->setDbVersion($this->_resourceName, $toVersion);
            return $toVersion;
        }

        $modifyVersion = null;
        foreach ($arrModifyFiles as $resourceFile) {
            $sqlFile = $sqlFilesDir.DS.$resourceFile['fileName'];
            $fileType = pathinfo($resourceFile['fileName'], PATHINFO_EXTENSION);

            // Execute SQL
            if ($this->_conn) {
                try {
                    switch ($fileType) {
                        case 'sql':
                            $sql = file_get_contents($sqlFile);
                            if ($sql!='') {
                                $result = $this->run($sql);
                            } else {
                                $result = true;
                            }
                            break;

                        case 'php':
                            $conn = $this->_conn;
                            /**
                             * useful variables:
                             * - $conn: setup db connection
                             * - $sqlFilesDir: root dir for sql update files
                             */
                            try {
                                #$conn->beginTransaction();
                                $result = include($sqlFile);
                                #$conn->commit();
                            } catch (Exception $e) {
                                #$conn->rollback();
                                throw ($e);
                            }
                            break;

                        default:
                            $result = false;
                    }
                    if ($result) {
                        /*$this->run("replace into ".$this->getTable('core/resource')." (code, version) values ('".$this->_resourceName."', '".$resourceFile['toVersion']."')");*/
                        Mage::getResourceModel('core/resource')->setDbVersion($this->_resourceName, $resourceFile['toVersion']);
                    }
                }
                catch (Exception $e){
                    echo "<pre>".print_r($e,1)."</pre>";
                    throw Mage::exception('Mage_Core', Mage::helper('core')->__('Error in file: "%s" - %s', $sqlFile, $e->getMessage()));
                }
            }

            $modifyVersion = $resourceFile['toVersion'];
        }

        if ($actionType == 'upgrade' && $modifyVersion != $toVersion) {
            Mage::getResourceModel('core/resource')->setDbVersion($this->_resourceName, $toVersion);
        }
        else {
            $toVersion = $modifyVersion;
        }

        self::$_hadUpdates = true;
        return $toVersion;
    }

    /**
     * Get sql files for modifications
     *
     * @param     $actionType
     * @return    array
     */

    protected function _getModifySqlFiles($actionType, $fromVersion, $toVersion, $arrFiles)
    {
        $arrRes = array();

        switch ($actionType) {
            case 'install':
                uksort($arrFiles, 'version_compare');
                foreach ($arrFiles as $version => $file) {
                    if (version_compare($version, $toVersion)!==self::VERSION_COMPARE_GREATER) {
                        $arrRes[0] = array('toVersion'=>$version, 'fileName'=>$file);
                    }
                }
                break;

            case 'upgrade':
                uksort($arrFiles, 'version_compare');
                foreach ($arrFiles as $version => $file) {
                    $version_info = explode('-', $version);

                    // In array must be 2 elements: 0 => version from, 1 => version to
                    if (count($version_info)!=2) {
                        break;
                    }
                    $infoFrom = $version_info[0];
                    $infoTo   = $version_info[1];
                    if (version_compare($infoFrom, $fromVersion)!==self::VERSION_COMPARE_LOWER
                        && version_compare($infoTo, $toVersion)!==self::VERSION_COMPARE_GREATER) {
                        $arrRes[] = array('toVersion'=>$infoTo, 'fileName'=>$file);
                    }
                }
                break;

            case 'rollback':
                break;

            case 'uninstall':
                break;
        }
        return $arrRes;
    }


/******************* UTILITY METHODS *****************/

    /**
     * Retrieve row or field from table by id or string and parent id
     *
     * @param string $table
     * @param string $idField
     * @param string|integer $id
     * @param string $field
     * @param string $parentField
     * @param string|integer $parentId
     * @return mixed|boolean
     */
    public function getTableRow($table, $idField, $id, $field=null, $parentField=null, $parentId=0)
    {
        if (strpos($table, '/')!==false) {
            $table = $this->getTable($table);
        }

        if (empty($this->_setupCache[$table][$parentId][$id])) {
            $sql = "select * from $table where $idField=?";
            if (!is_null($parentField)) {
                $sql .= $this->_conn->quoteInto(" and $parentField=?", $parentId);
            }
            $this->_setupCache[$table][$parentId][$id] = $this->_conn->fetchRow($sql, $id);
        }
        if (is_null($field)) {
            return $this->_setupCache[$table][$parentId][$id];
        }
        return isset($this->_setupCache[$table][$parentId][$id][$field]) ? $this->_setupCache[$table][$parentId][$id][$field] : false;
    }

    /**
     * Delete table row
     *
     * @param   string $table
     * @param   string $idField
     * @param   string|int $id
     * @param   null|string $parentField
     * @param   int|string $parentId
     * @return  Mage_Core_Model_Resource_Setup
     */
    public function deleteTableRow($table, $idField, $id, $parentField=null, $parentId=0)
    {
        if (strpos($table, '/')!==false) {
            $table = $this->getTable($table);
        }

        $condition = $this->_conn->quoteInto("$idField=?", $id);
        if ($parentField !== null) {
            $condition.= $this->_conn->quoteInto(" AND $parentField=?", $parentId);
        }
        $this->_conn->delete($table, $condition);

        if (isset($this->_setupCache[$table][$parentId][$id])) {
            unset($this->_setupCache[$table][$parentId][$id]);
        }
        return $this;
    }

    /**
     * Update one or more fields of table row
     *
     * @param string $table
     * @param string $idField
     * @param string|integer $id
     * @param string|array $field
     * @param mixed|null $value
     * @param string $parentField
     * @param string|integer $parentId
     * @return Mage_Eav_Model_Entity_Setup
     */
    public function updateTableRow($table, $idField, $id, $field, $value=null, $parentField=null, $parentId=0)
    {
        if (is_array($field)) {
            $updateArr = array();
            foreach ($field as $f=>$v) {
                $updateArr[] = $this->_conn->quoteInto("$f=?", $v);
            }
            $updateStr = join(', ', $updateArr);
        } else {
            $updateStr = $this->_conn->quoteInto("$field=?", $value);
        }
        if (strpos($table, '/')!==false) {
            $table = $this->getTable($table);
        }
        $sql = "update $table set $updateStr where ".$this->_conn->quoteInto("$idField=?", $id);
        if (!is_null($parentField)) {
            $sql .= $this->_conn->quoteInto(" and $parentField=?", $parentId);
        }
        $this->_conn->query($sql);

        return $this;
    }

    public function updateTable($table, $conditionExpr, $valueExpr)
    {
        if (strpos($table, '/')!==false) {
            $table = $this->getTable($table);
        }
        $sql = 'update ' . $table . ' set ' . $valueExpr . ' where ' . $conditionExpr;
        $this->_conn->query($sql);
        return $this;
    }

    public function tableExists($table)
    {
        $select = $this->getConnection()->quoteInto('SHOW TABLES LIKE ?', $table);
        $result = $this->getConnection()->fetchOne($select);
        return !empty($result);
    }

/******************* CONFIG *****************/

    public function addConfigField($path, $label, array $data=array(), $default=null)
    {
        $data['level'] = sizeof(explode('/', $path));
        $data['path'] = $path;
        $data['frontend_label'] = $label;
        if ($id = $this->getTableRow('core/config_field', 'path', $path, 'field_id')) {
            $this->updateTableRow('core/config_field', 'field_id', $id, $data);
        } else {
            if (empty($data['sort_order'])) {
                $sql = "select max(sort_order) cnt from ".$this->getTable('core/config_field')." where level=".($data['level']+1);
                if ($data['level']>1) {
                    $sql.= $this->_conn->quoteInto(" and path like ?", dirname($path).'/%');
                }

                $result = $this->_conn->raw_fetchRow($sql);
                $this->_conn->fetchAll($sql);
#print_r($result); die;
                $data['sort_order'] = $result['cnt']+1;
/*
// Triggers "Command out of sync" mysql error for next statement!?!?
                $data['sort_order'] = $this->_conn->fetchOne("select max(sort_order)
                    from ".$this->getTable('core/config_field')."
                    where level=?".$parentWhere, $data['level'])+1;
*/
            }

            #$this->_conn->raw_query("insert into ".$this->getTable('core/config_field')." (".join(',', array_keys($data)).") values ('".join("','", array_values($data))."')");
            $this->_conn->insert($this->getTable('core/config_field'), $data);
        }

        if (!is_null($default)) {
            $this->setConfigData($path, $default);
        }
        return $this;
    }

    public function setConfigData($path, $value, $scope='default', $scopeId=0, $inherit=0)
    {
        $this->_conn->raw_query("replace into ".$this->getTable('core/config_data')." (scope, scope_id, path, value) values ('$scope', $scopeId, '$path', '$value')");
        return $this;
    }

    /**
     * Delete config field values
     *
     * @param   string $path
     * @param   string $scope (default|stores|websites|config)
     * @return  Mage_Core_Model_Resource_Setup
     */
    public function deleteConfigData($path, $scope=null)
    {
        $sql = "delete from ".$this->getTable('core/config_data')." where path='".$path."'";
        if ($scope) {
            $sql.= " and scope='".$scope."'";
        }
        $this->_conn->raw_query($sql);
        return $this;
    }

    public function run($sql)
    {
        $this->_conn->multi_query($sql);
        return $this;
    }

    public function startSetup()
    {
        $this->_conn->multi_query("SET SQL_MODE='';
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
");

        return $this;
    }

    public function endSetup()
    {
        $this->_conn->multi_query("
SET SQL_MODE=IFNULL(@OLD_SQL_MODE,'');
SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS,0);
");
        return $this;
    }
}
