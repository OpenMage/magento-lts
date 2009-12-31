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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract resource model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Resource_Abstract
{
    public function __construct()
    {
        $this->_construct();
    }

    /**
     * Resource initialization
     */
    abstract protected function _construct();

    /**
     * Retrieve connection for read data
     */
    abstract protected function _getReadAdapter();

    /**
     * Retrieve connection for write data
     */
    abstract protected function _getWriteAdapter();

    /**
     * Start resource transaction
     *
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function beginTransaction()
    {
        $this->_getWriteAdapter()->beginTransaction();
        return $this;
    }

    /**
     * Commit resource transaction
     *
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function commit()
    {
        $this->_getWriteAdapter()->commit();
        return $this;
    }

    /**
     * Roll back resource transaction
     *
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function rollBack()
    {
        $this->_getWriteAdapter()->rollBack();
        return $this;
    }

    /**
     * Format date to internal format
     *
     * @param   string | Zend_Date $date
     * @param   bool $includeTime
     * @return  string
     */
    public function formatDate($date, $includeTime=true)
    {
        if ($date instanceof Zend_Date) {
            if ($includeTime) {
                return $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }
            else {
                return $date->toString(Varien_Date::DATE_INTERNAL_FORMAT);
            }
        }

    	if (empty($date)) {
    		return new Zend_Db_Expr('NULL');
    	}

        if (!is_numeric($date)) {
            $date = strtotime($date);
        }
        if ($includeTime) {
            return date('Y-m-d H:i:s', $date);
        }
        else {
            return date('Y-m-d', $date);
        }
    }

    public function mktime($str)
    {
        return  strtotime($str);
    }
}
