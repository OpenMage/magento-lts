<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Magento
 * @package    Magento_Db
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Magento_Db_Object_Trigger
 *
 * @category   Magento
 * @package    Magento_Db
 */
class Magento_Db_Object_Trigger extends Magento_Db_Object implements Magento_Db_Object_Interface
{
    /**
     * @var string
     */
    protected $_dbType  = 'TRIGGER';

    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @return bool
     */
    public function isExists()
    {
        if (!isset($this->_data['triggers'][$this->_schemaName])) {
            $this->describe();
        }

        if (isset($this->_data['triggers'][$this->_schemaName][$this->_objectName])) {
            return true;
        }

        return false;
    }

    public function describe()
    {
        $columns = [
            'TRIGGER_NAME',
            'EVENT_MANIPULATION',
            'EVENT_OBJECT_CATALOG',
            'EVENT_OBJECT_SCHEMA',
            'EVENT_OBJECT_TABLE',
            'ACTION_ORDER',
            'ACTION_CONDITION',
            'ACTION_STATEMENT',
            'ACTION_ORIENTATION',
            'ACTION_TIMING',
            'ACTION_REFERENCE_OLD_TABLE',
            'ACTION_REFERENCE_NEW_TABLE',
            'ACTION_REFERENCE_OLD_ROW',
            'ACTION_REFERENCE_NEW_ROW',
            'CREATED',
        ];
        $sql = 'SELECT ' . implode(', ', $columns)
            . ' FROM ' . $this->_adapter->quoteIdentifier(['INFORMATION_SCHEMA','TRIGGERS'])
            . ' WHERE ';

        $schema = $this->getSchemaName();
        if ($schema) {
            $sql .= $this->_adapter->quoteIdentifier('EVENT_OBJECT_SCHEMA')
                . ' = ' . $this->_adapter->quote($schema);
        } else {
            $sql .= $this->_adapter->quoteIdentifier('EVENT_OBJECT_SCHEMA')
                . ' != ' . $this->_adapter->quote('INFORMATION_SCHEMA');
        }

        $results = $this->_adapter->query($sql);

        $data = [];
        foreach ($results as $row) {
            $row = array_change_key_case($row, CASE_LOWER);
            if (null !== $row['created']) {
                $row['created'] = new DateTime($row['created']);
            }
            $data[$row['trigger_name']] = $row;
        }
        $this->_data['triggers'][$schema] = $data;

        return $data;
    }
}
