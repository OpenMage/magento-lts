<?php

/**
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Active record implementation
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Iterator extends Varien_Object
{
    /**
     * Walk over records fetched from query one by one using callback function
     *
     * @param Zend_Db_Statement_Interface|Zend_Db_Select|string $query
     * @param array|string $callbacks
     * @param Varien_Db_Adapter_Interface $adapter
     * @return $this
     */
    public function walk($query, array $callbacks, array $args = [], $adapter = null)
    {
        $stmt = $this->_getStatement($query, $adapter);
        $args['idx'] = 0;
        while ($row = $stmt->fetch()) {
            $args['row'] = $row;
            foreach ($callbacks as $callback) {
                $result = call_user_func($callback, $args);
                if (!empty($result)) {
                    $args = array_merge($args, $result);
                }
            }
            $args['idx']++;
        }

        return $this;
    }

    /**
     * Fetch Zend statement instance
     *
     * @param Zend_Db_Statement_Interface|Zend_Db_Select|string $query
     * @param Zend_Db_Adapter_Abstract $conn
     * @return Zend_Db_Statement_Interface
     * @throws Mage_Core_Exception
     */
    protected function _getStatement($query, $conn = null)
    {
        if ($query instanceof Zend_Db_Statement_Interface) {
            return $query;
        }

        if ($query instanceof Zend_Db_Select) {
            return $query->query();
        }

        if (is_string($query)) {
            if (!$conn instanceof Zend_Db_Adapter_Abstract) {
                Mage::throwException(Mage::helper('core')->__('Invalid connection'));
            }
            return $conn->query($query);
        }

        Mage::throwException(Mage::helper('core')->__('Invalid query'));
    }
}
