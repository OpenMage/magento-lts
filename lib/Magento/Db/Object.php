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
 * @category    Magento
 * @package     Magento_Db
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Magento_Db_Object
 *
 * @category    Magento
 * @package     Magento_Db
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Db_Object
{
    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $_adapter     = null;

    /**
     * @var string
     */
    protected $_objectName  = null;

    /**
     * @var string
     */
    protected $_dbType  = null;

    /**
     * @var string
     */
    protected $_schemaName  = null;

    /**
     * Constructor
     * @param $objectName
     * @param Varien_Db_Adapter_Interface $adapter
     * @param $schemaName
     */
    public function __construct(Varien_Db_Adapter_Interface $adapter, $objectName, $schemaName = null)
    {
        $this->_objectName  = $objectName;
        $this->_adapter = $adapter;
        $this->_schemaName = $schemaName;
    }

    /**
     * Returns object type
     *
     * @return string
     */
    public function getDbType()
    {
        return $this->_dbType;
    }


    /**
     * Returns current schema name
     *
     * @return string
     */
    protected function _getCurrentSchema()
    {
        return $this->_adapter->fetchOne('SELECT SCHEMA()');
    }

    /**
     * Returns schema name
     *
     * @return string
     */
    public function getSchemaName()
    {
        if (!$this->_schemaName) {
            $this->_schemaName = $this->_getCurrentSchema();
        }

        return $this->_schemaName;
    }

    /**
     * Drop database object
     *
     * @return Magento_Db_Object
     */
    public function drop()
    {
        $query  = 'DROP ' . $this->getDbType() . ' IF EXISTS '
            . $this->_adapter->quoteIdentifier($this->_objectName);
        $this->_adapter->query($query);

        return $this;
    }

    /**
     * Returns object name
     *
     * @return string
     */
    public function getFullName()
    {
        return ($this->getSchemaName() ? $this->getSchemaName() . '.' : '') . $this->_objectName;
    }

    /**
     * Returns object name
     *
     * @return string
     */
    public function getObjectName()
    {
        return $this->_objectName;
    }
}
