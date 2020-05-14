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
 * @category    Mage
 * @package     Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer log resource
 *
 * @category   Mage
 * @package    Mage_Log
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Log_Model_Resource_Customer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Visitor data table name
     *
     * @var string
     */
    protected $_visitorTable;

    /**
     * Visitor info data table
     *
     * @var string
     */
    protected $_visitorInfoTable;

    /**
     * Customer data table
     *
     * @var string
     */
    protected $_customerTable;

    /**
     * Url info data table
     *
     * @var string
     */
    protected $_urlInfoTable;

    /**
     * Log URL data table name.
     *
     * @var string
     */
    protected $_urlTable;

    /**
     * Log quote data table name.
     *
     * @var string
     */
    protected $_quoteTable;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('log/customer', 'log_id');

        $this->_visitorTable        = $this->getTable('log/visitor');
        $this->_visitorInfoTable    = $this->getTable('log/visitor_info');
        $this->_urlTable            = $this->getTable('log/url_table');
        $this->_urlInfoTable        = $this->getTable('log/url_info_table');
        $this->_customerTable       = $this->getTable('log/customer');
        $this->_quoteTable          = $this->getTable('log/quote_table');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Log_Model_Customer $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($field == 'customer_id') {
            // load additional data by last login
            $table  = $this->getMainTable();
            $select
                ->joinInner(
                    array('lvt' => $this->_visitorTable),
                    "lvt.visitor_id = {$table}.visitor_id",
                    array('last_visit_at')
                )
                ->joinInner(
                    array('lvit' => $this->_visitorInfoTable),
                    'lvt.visitor_id = lvit.visitor_id',
                    array('http_referer', 'remote_addr')
                )
                ->joinInner(
                    array('luit' => $this->_urlInfoTable),
                    'luit.url_id = lvt.last_url_id',
                    array('url')
                )
                ->order("{$table}.login_at DESC")
                ->limit(1);
        }
        return $select;
    }
}
