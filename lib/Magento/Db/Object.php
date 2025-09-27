<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Magento_Db
 */

/**
 * Magento_Db_Object
 *
 * @package    Magento_Db
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
