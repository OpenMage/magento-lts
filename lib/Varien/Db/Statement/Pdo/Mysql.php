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
 * @category    Varien
 * @package     Varien_Db
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Mysql DB Statement
 *
 * @category    Varien
 * @package     Varien_Db
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Db_Statement_Pdo_Mysql extends Zend_Db_Statement_Pdo
{
    /**
     * Executes statement with binding values to it.
     * Allows transferring specific options to DB driver.
     *
     * @param array $params Array of values to bind to parameter placeholders.
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function _executeWithBinding(array $params)
    {
        // Check whether we deal with named bind
        $isPositionalBind = true;
        foreach ($params as $k => $v) {
            if (!is_int($k)) {
                $isPositionalBind = false;
                break;
            }
        }

        /* @var $statement PDOStatement */
        $statement = $this->_stmt;
        $bindValues = array(); // Separate array with values, as they are bound by reference
        foreach ($params as $name => $param) {
            $dataType = PDO::PARAM_STR;
            $length = null;
            $driverOptions = null;

            if ($param instanceof Varien_Db_Statement_Parameter) {
                if ($param->getIsBlob()) {
                    // Nothing to do there - default options are fine for MySQL driver
                } else {
                    $dataType = $param->getDataType();
                    $length = $param->getLength();
                    $driverOptions = $param->getDriverOptions();
                }
                $bindValues[$name] = $param->getValue();
            } else {
                $bindValues[$name] = $param;
            }

            $paramName = $isPositionalBind ? ($name + 1) : $name;
            $statement->bindParam($paramName, $bindValues[$name], $dataType, $length, $driverOptions);
        }

        try {
            return $statement->execute();
        } catch (PDOException $e) {
            throw new Zend_Db_Statement_Exception($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Executes a prepared statement.
     *
     * @param array $params OPTIONAL Values to bind to parameter placeholders.
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function _execute(array $params = null)
    {
        $specialExecute = false;
        if ($params) {
            foreach ($params as $param) {
                if ($param instanceof Varien_Db_Statement_Parameter) {
                    $specialExecute = true;
                    break;
                }
            }
        }

        if ($specialExecute) {
            return $this->_executeWithBinding($params);
        } else {
            return parent::_execute($params);
        }
    }
}
