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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

#require_once 'Mage/Core/Model/Mysql4.php';

/**
 * Mysql Model for module
 */
class Mage_Core_Model_Mysql4_Resource
{
    protected $_read = null;
    protected $_write = null;
    protected $_resTable = null;
    protected static $_versions = null;

    public function __construct()
    {
        $this->_resTable = Mage::getSingleton('core/resource')->getTableName('core/resource');
        $this->_read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    /**
     * Get Module version from DB
     *
     * @param   string $moduleName
     * @return  string
     */
    function getDbVersion($resName)
    {
        if (!$this->_read) {
            return false;
        }

        if (is_null(self::$_versions)) {
            // if Core module not instaled
            try {
                $select = $this->_read->select()->from($this->_resTable, array('code', 'version'));
                self::$_versions = $this->_read->fetchPairs($select);
            }
            catch (Exception $e){
                self::$_versions = array();
            }
        }
        return isset(self::$_versions[$resName]) ? self::$_versions[$resName] : false;
    }

    /**
     * Set module wersion into DB
     *
     * @param   string $moduleName
     * @param   string $version
     * @return  int
     */
    function setDbVersion($resName, $version)
    {
        $dbModuleInfo = array(
            'code'    => $resName,
            'version' => $version,
        );

        if ($this -> getDbVersion($resName)) {
            self::$_versions[$resName] = $version;
        	$condition = $this->_write->quoteInto('code=?', $resName);
        	return $this->_write->update($this->_resTable, $dbModuleInfo, $condition);
        }
        else {
            self::$_versions[$resName] = $version;
        	return $this->_write->insert($this->_resTable, $dbModuleInfo);
        }
    }
}