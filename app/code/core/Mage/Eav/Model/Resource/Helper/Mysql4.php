<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eav Mysql resource helper model
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Resource_Helper_Mysql4 extends Mage_Core_Model_Resource_Helper_Mysql4
{
    /**
     * Mysql column - Table DDL type pairs
     *
     * @var array
     */
    protected $_ddlColumnTypes      = [
        Varien_Db_Ddl_Table::TYPE_BOOLEAN       => 'bool',
        Varien_Db_Ddl_Table::TYPE_SMALLINT      => 'smallint',
        Varien_Db_Ddl_Table::TYPE_INTEGER       => 'int',
        Varien_Db_Ddl_Table::TYPE_BIGINT        => 'bigint',
        Varien_Db_Ddl_Table::TYPE_FLOAT         => 'float',
        Varien_Db_Ddl_Table::TYPE_DECIMAL       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_NUMERIC       => 'decimal',
        Varien_Db_Ddl_Table::TYPE_DATE          => 'date',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP     => 'timestamp',
        Varien_Db_Ddl_Table::TYPE_DATETIME      => 'datetime',
        Varien_Db_Ddl_Table::TYPE_TEXT          => 'text',
        Varien_Db_Ddl_Table::TYPE_BLOB          => 'blob',
        Varien_Db_Ddl_Table::TYPE_VARBINARY     => 'blob'
    ];

    /**
     * Returns columns for select
     *
     * @param string $tableAlias
     * @param string $eavType
     * @return string
     */
    public function attributeSelectFields($tableAlias, $eavType)
    {
        return '*';
    }

    /**
     * Returns DDL type by column type in database
     *
     * @param string $columnType
     * @return string
     */
    public function getDdlTypeByColumnType($columnType)
    {
        switch ($columnType) {
            case 'char':
            case 'varchar':
                $columnType = 'text';
                break;
            case 'tinyint':
                $columnType = 'smallint';
                break;
            case 'int unsigned':
                $columnType = 'int';
                break;
        }

        return array_search($columnType, $this->_ddlColumnTypes);
    }

    /**
     * Prepares value fields for unions depend on type
     *
     * @param string $value
     * @param string $eavType
     * @return string
     */
    public function prepareEavAttributeValue($value, $eavType)
    {
        return $value;
    }

    /**
     * Groups selects to separate unions depend on type
     *
     * @param array $selects
     * @return array
     */
    public function getLoadAttributesSelectGroups($selects)
    {
        $mainGroup  = [];
        foreach ($selects as $eavType => $selectGroup) {
            $mainGroup = array_merge($mainGroup, $selectGroup);
        }
        return $mainGroup;
    }

    /**
     * Retrieve 'cast to int' expression
     *
     * @param string|Zend_Db_Expr $expression
     * @return Zend_Db_Expr
     */
    public function getCastToIntExpression($expression)
    {
        return new Zend_Db_Expr("CAST($expression AS SIGNED)");
    }
}
