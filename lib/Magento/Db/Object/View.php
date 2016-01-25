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
 * Magento_Db_Object_View
 *
 * @category    Magento
 * @package     Magento_Db
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Db_Object_View extends Magento_Db_Object implements Magento_Db_Object_Interface
{
    const ALGORITHM_MERGE       = 'MERGE';
    const ALGORITHM_TEMPTABLE   = 'TEMPTABLE';

    /**
     * @var string
     */
    protected $_dbType  = 'VIEW';

    /**
     * Create view from source
     *
     * @param $source
     * @param string $algorithm
     * @return Magento_Db_Object_View
     */
    public function createFromSource(Zend_Db_Select $source, $algorithm = self::ALGORITHM_MERGE)
    {
        $this->_adapter->query(
            'CREATE ALGORITHM = ' . $algorithm . ' ' . $this->getDbType() . ' '
                . $this->getObjectName() . ' AS ' . $source
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
