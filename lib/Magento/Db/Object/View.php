<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Magento_Db
 */

/**
 * Magento_Db_Object_View
 *
 * @package    Magento_Db
 */
class Magento_Db_Object_View extends Magento_Db_Object implements Magento_Db_Object_Interface
{
    public const ALGORITHM_MERGE       = 'MERGE';

    public const ALGORITHM_TEMPTABLE   = 'TEMPTABLE';

    /**
     * @var string
     */
    protected $_dbType  = 'VIEW';

    /**
     * Create view from source
     *
     * @param                         $source
     * @param  string                 $algorithm
     * @return Magento_Db_Object_View
     */
    public function createFromSource(Zend_Db_Select $source, $algorithm = self::ALGORITHM_MERGE)
    {
        $this->_adapter->query(
            'CREATE ALGORITHM = ' . $algorithm . ' ' . $this->getDbType() . ' '
                . $this->getObjectName() . ' AS ' . $source,
        );
        return $this;
    }

    /**
     * Describe Table
     *
     * @return array
     */
    public function describe()
    {
        return $this->_adapter->describeTable($this->_objectName, $this->_schemaName);
    }

    /**
     * Check is object exists
     *
     * @return bool
     */
    public function isExists()
    {
        return $this->_adapter->isTableExists($this->_objectName, $this->_schemaName);
    }
}
