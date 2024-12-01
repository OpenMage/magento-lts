<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eav Mysql resource helper model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Helper_Mysql4 extends Mage_Eav_Model_Resource_Helper_Mysql4
{
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
     * Compare Flat style with Describe style columns
     * If column a different - return false
     *
     * @param array $column
     * @param array $describe
     * @return bool
     */
    public function compareIndexColumnProperties($column, $describe)
    {
        $type = $column['type'];
        if (isset($column['length'])) {
            $type = sprintf('%s(%s)', $type[0], $column['length']);
        } else {
            $type = $type[0];
        }
        $length     = null;
        $precision  = null;
        $scale      = null;

        $matches = [];
        if (preg_match('/^((?:var)?char)\((\d+)\)/', $type, $matches)) {
            $type       = $matches[1];
            $length     = $matches[2];
        } elseif (preg_match('/^decimal\((\d+),(\d+)\)/', $type, $matches)) {
            $type       = 'decimal';
            $precision  = $matches[1];
            $scale      = $matches[2];
        } elseif (preg_match('/^float\((\d+),(\d+)\)/', $type, $matches)) {
            $type       = 'float';
            $precision  = $matches[1];
            $scale      = $matches[2];
        } elseif (preg_match('/^((?:big|medium|small|tiny)?int)\((\d+)\)?/', $type, $matches)) {
            $type       = $matches[1];
        }

        return ($describe['DATA_TYPE'] == $type)
            && ($describe['DEFAULT'] == $column['default'])
            && ((bool)$describe['NULLABLE'] == (bool)$column['nullable'])
            && ((bool)$describe['UNSIGNED'] == (bool)$column['unsigned'])
            && ($describe['LENGTH'] == $length)
            && ($describe['SCALE'] == $scale)
            && ($describe['PRECISION'] == $precision);
    }

    /**
     * Getting condition isNull(f1,f2) IS NOT Null
     *
     * @param string $field1
     * @param string $field2
     * @return string
     */
    public function getIsNullNotNullCondition($field1, $field2)
    {
        return sprintf('%s IS NOT NULL', $this->_getReadAdapter()->getIfNullSql($field1, $field2));
    }
}
