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
 * @package     Mage_Mview
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Mview_Model_Command_Factory
 *
 * @category    Mage
 * @package     Mage_Mview
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Mview_Model_Command_Factory
{
    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection  = null;

    /**
     * @var Mage_Mview_Model_Factory
     */
    protected $_factory = null;
    /**
     * Constructor
     */
    public function __construct($arguments)
    {
        if (!empty($arguments['factory'])) {
            $this->_factory = $arguments['factory'];
        } else {
            $this->_factory = Mage::getModel('mview/factory');
        }
        $this->_connection  = $this->_factory->getModel('core/resource')->getConnection('write');
    }

    /**
     * Returns create materialized view command
     *
     * @param Zend_Db_Select $select
     * @param $tableName
     * @param $viewName
     * @return false|Mage_Core_Model_Abstract
     */
    public function getCommandCreate(Zend_Db_Select $select, $tableName, $viewName)
    {
        return $this->_factory->getModel('mview/command_create', array(
            'connection'    => $this->_connection,
            'view'          => new Magento_Db_Object_View($this->_connection, $viewName),
            'table'         => new Magento_Db_Object_Table($this->_connection, $tableName),
            'select'        => $select
        ));
    }

    /**
     * Returns drop materialized view command
     *
     * @param $tableName
     * @param $viewName
     * @return false|Mage_Core_Model_Abstract
     */
    public function getCommandDrop($tableName, $viewName)
    {
        return $this->_factory->getModel('mview/command_drop', array(
            'connection'    => $this->_connection,
            'view'          => new Magento_Db_Object_View($this->_connection, $viewName),
            'table'         => new Magento_Db_Object_Table($this->_connection, $tableName)
        ));
    }

    /**
     * Returns refresh materialized view command
     *
     * @param $tableName
     * @param $viewName
     * @return false|Mage_Core_Model_Abstract
     */
    public function getCommandRefresh($tableName, $viewName)
    {
        return $this->_factory->getModel('mview/command_refresh', array(
            'connection'    => $this->_connection,
            'view_name'     => $viewName,
            'table_name'    => $tableName
        ));
    }

    /**
     * Returns refresh row materialized view command
     *
     * @param $tableName
     * @param $viewName
     * @param $ruleColumn
     * @param $value
     * @return false|Mage_Core_Model_Abstract
     */
    public function getCommandRefreshRow($tableName, $viewName, $ruleColumn, $value)
    {
        return $this->_factory->getModel('mview/command_refresh_row', array(
            'connection'    => $this->_connection,
            'view_name'     => $viewName,
            'table_name'    => $tableName,
            'rule_column'   => $ruleColumn,
            'value'         => $value
        ));
    }

    /**
     * Create changelog table
     * @param $mviewName
     * @param $changelogName
     * @param $ruleColumn
     * @return false|Mage_Core_Model_Abstract
     */
    public function getCommandChangelogCreate($mviewName, $changelogName, $ruleColumn)
    {
        return $this->_factory->getModel('mview/command_changelog_create', array(
            'connection'    => $this->_connection,
            'mview'         => new Magento_Db_Object_Table($this->_connection, $mviewName),
            'changelog'     => new Magento_Db_Object_Table($this->_connection, $changelogName),
            'rule_column'   => $ruleColumn
        ));
    }
}
