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
 * @category    Mage
 * @package     Mage_Log
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer log resource
 *
 * @category   Mage
 * @package    Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Log_Model_Mysql4_Customer
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
     * Database read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * Database write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');

        $this->_visitorTable    = $resource->getTableName('log/visitor');
        $this->_visitorInfoTable= $resource->getTableName('log/visitor_info');
        $this->_urlTable        = $resource->getTableName('log/url_table');
        $this->_urlInfoTable    = $resource->getTableName('log/url_info_table');
        $this->_customerTable   = $resource->getTableName('log/customer');
        $this->_quoteTable      = $resource->getTableName('log/quote_table');

        $this->_read = $resource->getConnection('log_read');
        $this->_write = $resource->getConnection('log_write');
    }
    
    public function load($object, $customerId)
    {
        $select = $this->_read->select();
        $select->from($this->_customerTable, array('login_at', 'logout_at'))
            ->joinInner($this->_visitorTable, $this->_visitorTable.'.visitor_id='.$this->_customerTable.'.visitor_id', array('last_visit_at'))
            ->joinInner($this->_visitorInfoTable, $this->_visitorTable.'.visitor_id='.$this->_visitorInfoTable.'.visitor_id', array('http_referer', 'remote_addr'))
            ->joinInner($this->_urlInfoTable, $this->_urlInfoTable.'.url_id='.$this->_visitorTable.'.last_url_id', array('url'))
            ->where($this->_read->quoteInto($this->_customerTable.'.customer_id=?', $customerId))
            ->order($this->_customerTable.'.login_at desc')
            ->limit(1);
        $object->setData($this->_read->fetchRow($select));
        return $object;
    }
}
