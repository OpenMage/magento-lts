<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Magento
 * @package    Magento_Db
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Magento_Db_Object_Table
 *
 * @category   Magento
 * @package    Magento_Db
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Db_Object_Table extends Magento_Db_Object implements Magento_Db_Object_Interface
{
    /**
     * @var string
     */
    protected $_dbType  = 'TABLE';

    /**
     * Check is object exists
     *
     * @return bool
     */
    public function isExists()
    {
        return $this->_adapter->isTableExists($this->_objectName, $this->_schemaName);
    }

    /**
     * Create a new table from source
     *
     * @param $source Zend_Db_Select
     * @return Magento_Db_Object_Table
     */
    public function createFromSource(Zend_Db_Select $source)
    {
        $this->_adapter->query(
            'CREATE ' . $this->getDbType() . ' ' . $this->_objectName . ' AS ' . $source
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
}
