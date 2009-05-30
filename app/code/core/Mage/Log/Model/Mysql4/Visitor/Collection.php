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
 * @package    Mage_Log
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Log_Model_Mysql4_Customers_Collection
 *
 * @category   Mage
 * @package    Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Log_Model_Mysql4_Visitor_Collection extends Varien_Data_Collection_Db
{
    /**
     * Visitor data table name
     *
     * @var string
     */
    protected $_visitorTable;

    /**
     * Visitor data info table name
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
     * Log URL data table name.
     *
     * @var string
     */
    protected $_urlTable;

    /**
     * Log URL expanded data table name.
     *
     * @var string
     */
    protected $_urlInfoTable;

    /**
     * Aggregator data table.
     *
     * @var string
     */
    protected $_summaryTable;

    /**
     * Aggregator type data table.
     *
     * @var string
     */
    protected $_summaryTypeTable;

    /**
     * Quote data table.
     *
     * @var string
     */
    protected $_quoteTable;

    protected $_isOnlineFilterUsed = false;

    protected $_fieldMap = array(
        'customer_firstname' => 'customer_firstname_table.value',
        'customer_lastname'  => 'customer_lastname_table.value',
        'customer_email'     => 'customer_email_table.email',
        'customer_id'        =>  'customer_table.customer_id',
        'url'                =>  'url_info_table.url'
    );

    /**
     * Construct
     *
     */
    function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('log_read'));

        $this->_visitorTable = $resource->getTableName('log/visitor');
        $this->_visitorInfoTable = $resource->getTableName('log/visitor_info');
        $this->_urlTable = $resource->getTableName('log/url_table');
        $this->_urlInfoTable = $resource->getTableName('log/url_info_table');
        $this->_customerTable = $resource->getTableName('log/customer');
        $this->_summaryTable = $resource->getTableName('log/summary_table');
        $this->_summaryTypeTable = $resource->getTableName('log/summary_type_table');
        $this->_quoteTable = $resource->getTableName('log/quote_table');

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('log/visitor'));
    }

    /**
     * Enables online only select
     *
     * @param int $minutes
     * @return object
     */
    public function useOnlineFilter($minutes=null)
    {
        if (is_null($minutes)) {
            $minutes = Mage_Log_Model_Visitor::getOnlineMinutesInterval();
        }
        $this->_select->from(array('visitor_table'=>$this->_visitorTable))
            //->joinLeft(array('url_table'=>$this->_urlTable), 'visitor_table.last_url_id=url_table.url_id')
            ->joinLeft(array('info_table'=>$this->_visitorInfoTable), 'info_table.visitor_id=visitor_table.visitor_id')
            ->joinLeft(array('customer_table'=>$this->_customerTable),
                'customer_table.visitor_id = visitor_table.visitor_id AND customer_table.logout_at IS NULL',
                array('log_id', 'customer_id', 'login_at', 'logout_at'))
            ->joinLeft(array('url_info_table'=>$this->_urlInfoTable),
                'url_info_table.url_id = visitor_table.last_url_id')
            //->joinLeft(array('quote_table'=>$this->_quoteTable), 'quote_table.visitor_id=visitor_table.visitor_id')
            ->where( 'visitor_table.last_visit_at >= ( ? - INTERVAL '.$minutes.' MINUTE)', now() );


        $customersCollection = Mage::getModel('customer/customer')->getCollection();
        /* @var $customersCollection Mage_Customer_Model_Entity_Customer_Collection */
        $firstname = $customersCollection->getAttribute('firstname');
        $lastname  = $customersCollection->getAttribute('lastname');
        $email  = $customersCollection->getAttribute('email');

        $this->_select
            ->from('', array('type' => 'IF(customer_id, \''.Mage_Log_Model_Visitor::VISITOR_TYPE_CUSTOMER.'\', \''.Mage_Log_Model_Visitor::VISITOR_TYPE_VISITOR.'\')'))
            ->joinLeft(
                array('customer_lastname_table'=>$lastname->getBackend()->getTable()),
                'customer_lastname_table.entity_id=customer_table.customer_id
                 AND customer_lastname_table.attribute_id = '.(int) $lastname->getAttributeId() . '
                 ',
                array('customer_lastname'=>'value')
             )
             ->joinLeft(
                array('customer_firstname_table'=>$firstname->getBackend()->getTable()),
                'customer_firstname_table.entity_id=customer_table.customer_id
                 AND customer_firstname_table.attribute_id = '.(int) $firstname->getAttributeId() . '
                 ',
                array('customer_firstname'=>'value')
             )
             ->joinLeft(
                array('customer_email_table'=>$email->getBackend()->getTable()),
                'customer_email_table.entity_id=customer_table.customer_id',
                array('customer_email'=>'email')
             );
        $this->_isOnlineFilterUsed = true;
        return $this;
    }

    public function showCustomersOnly()
    {
        $this->_select->where('customer_table.customer_id > 0')
            ->group('customer_table.customer_id');
        return $this;
    }

    public function getAggregatedData($period=720, $type_code=null, $customFrom=null, $customTo=null)
    {
        /**
         * @todo : need remove agregation logic
         */
        $timeZoneOffset = Mage::getModel('core/date')->getGmtOffset();//Mage::app()->getLocale()->date()->getGmtOffset();
        $this->_itemObjectClass = 'Varien_Object';
        $this->_setIdFieldName('summary_id');

/*
        $this->_select->from(array('summary'=>$this->_summaryTable), array('summary_id','customer_count','visitor_count','add_date'=>"DATE_SUB(summary.add_date, INTERVAL $timeZoneOffset SECOND)"))
           ->join(array('type'=>$this->_summaryTypeTable), 'type.type_id=summary.type_id', array());

        if (is_null($customFrom) && is_null($customTo)) {
           $this->_select->where( "DATE_SUB(summary.add_date, INTERVAL $timeZoneOffset SECOND) >= ( DATE_SUB(?, INTERVAL $timeZoneOffset SECOND) - INTERVAL {$period} {$this->_getRangeByType($type_code)} )", now() );
        } else {
            if($customFrom) {
                $this->_select->where( "DATE_SUB(summary.add_date, INTERVAL $timeZoneOffset SECOND) >= ", $this->_read->convertDate($customFrom));
            }
            if($customTo) {
                $this->_select->where( "DATE_SUB(summary.add_date, INTERVAL $timeZoneOffset SECOND) <= ", $this->_read->convertDate($customTo));
            }
        }


        if( is_null($type_code) ) {
            $this->_select->where("summary.type_id IS NULL");
        } else {
            $this->_select->where("type.type_code = ? ", $type_code);
        }
*/

        $this->_select->from(array('summary'=>$this->_summaryTable),
            array('summary_id',
                'customer_count'=>'SUM(customer_count)',
                'visitor_count'=>'SUM(visitor_count)',
                'add_date'=>"DATE_ADD(summary.add_date, INTERVAL $timeZoneOffset SECOND)"
        ));

        $this->_select->where("DATE_SUB(summary.add_date, INTERVAL $timeZoneOffset SECOND) >= ( DATE_SUB(?, INTERVAL $timeZoneOffset SECOND) - INTERVAL {$period} {$this->_getRangeByType($type_code)} )", now() );
        $this->_select->group('DATE_FORMAT(add_date, \''.$this->_getGroupByDateFormat($type_code).'\')');
        $this->_select->order('add_date ASC');

        return $this;
    }

    protected function _getGroupByDateFormat($type)
    {
        switch ($type) {
            case 'day':
                $format = '%Y-%m-%d';
                break;
            default:
            case 'hour':
                $format = '%Y-%m-%d %H';
                break;
        }
        return $format;
    }

    protected function _getRangeByType($type_code)
    {
        switch ($type_code)
        {
            case 'day':
                $range = 'DAY';
                break;
            case 'hour':
                $range = 'HOUR';
                break;
            case 'minute':
            default:
                $range = 'MINUTE';
                break;

        }

        return $range;
    }

    /**
     * Filter by customer ID, as 'type' field does not exist
     *
     * @param string $fieldName
     * @param array $condition
     * @return Mage_Log_Model_Mysql4_Visitor_Collection
     */
    public function addFieldToFilter($fieldName, $condition=null)
    {
        if ($fieldName == 'type' && is_array($condition) && isset($condition['eq'])) {
            $fieldName = 'customer_id';
            if ($condition['eq'] === Mage_Log_Model_Visitor::VISITOR_TYPE_VISITOR) {
                $condition = array('null' => 1);
            } else {
                $condition = array('moreq' => 1);
            }
        }
        return parent::addFieldToFilter($this->_getFieldMap($fieldName), $condition);
    }

    protected function _getFieldMap($fieldName)
    {
        if(isset($this->_fieldMap[$fieldName])) {
            return $this->_fieldMap[$fieldName];
        } else {
            return 'visitor_table.' . $fieldName;
        }
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        Mage::dispatchEvent('log_visitor_collection_load_before', array('collection' => $this));
        return parent::load($printQuery, $logQuery);
    }

    public function getIsOnlineFilterUsed()
    {
        return $this->_isOnlineFilterUsed;
    }

    /**
     * Filter visitors by specified store ids
     *
     * @param array|int $storeIds
     */
    public function addVisitorStoreFilter($storeIds)
    {
        $this->_select->where('visitor_table.store_id IN (?)', $storeIds);
    }
}