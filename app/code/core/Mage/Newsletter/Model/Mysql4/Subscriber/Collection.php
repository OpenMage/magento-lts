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
 * @package     Mage_Newsletter
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter Subscribers Collection
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 * @todo       Refactoring this collection to Mage_Core_Model_Mysql4_Collection_Abstract.
 */

class Mage_Newsletter_Model_Mysql4_Subscriber_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Subscribers table name
     *
     * @var string
     */
    protected $_subscriberTable;

    /**
     * Queue link table name
     *
     * @var string
     */
    protected $_queueLinkTable;

    /**
     * Store table name
     *
     * @var string
     */
    protected $_storeTable;

    /**
     * Queue joined flag
     *
     * @var boolean
     */
    protected $_queueJoinedFlag = false;

    /**
     * Flag that indicates apply of customers info on load
     *
     * @var boolean
     */
    protected $_showCustomersInfo = false;

    /**
     * Filter for count
     *
     * @var unknown_type
     */
    protected $_countFilterPart = array();

    /**
     * Constructor
     *
     * Configures collection
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_subscriberTable = Mage::getSingleton('core/resource')->getTableName('newsletter/subscriber');
        $this->_queueLinkTable = Mage::getSingleton('core/resource')->getTableName('newsletter/queue_link');
        $this->_storeTable = Mage::getSingleton('core/resource')->getTableName('core/store');
        $this->_init('newsletter/subscriber');

        // defining mapping for fields represented in several tables
        $this->_map['fields']['customer_lastname'] = 'customer_lastname_table.value';
        $this->_map['fields']['customer_firstname'] = 'customer_firstname_table.value';
        $this->_map['fields']['type'] = 'IF(main_table.customer_id = 0, 1, 2)';
        $this->_map['fields']['website_id'] = 'store.website_id';
        $this->_map['fields']['group_id'] = 'store.group_id';
        $this->_map['fields']['store_id'] = 'main_table.store_id';
    }

    /**
     * Set loading mode subscribers by queue
     *
     * @param Mage_Newsletter_Model_Queue $queue
     */
    public function useQueue(Mage_Newsletter_Model_Queue $queue)
    {
        $this->getSelect()->join(array('link'=>$this->_queueLinkTable), "link.subscriber_id = main_table.subscriber_id", array())
            ->where("link.queue_id = ? ", $queue->getId());
        $this->_queueJoinedFlag = true;
        return $this;
    }

    /**
     * Set using of links to only unsendet letter subscribers.
     */
    public function useOnlyUnsent( )
    {
        if($this->_queueJoinedFlag) {
            $this->getSelect()->where("link.letter_sent_at IS NULL");
        }

        return $this;
    }

    /**
     * Adds customer info to select
     *
     * @return  Mage_Newsletter_Model_Mysql4_Subscriber_Collection
     */
    public function showCustomerInfo()
    {
        $customer = Mage::getModel('customer/customer');
        /* @var $customer Mage_Customer_Model_Customer */
        $firstname  = $customer->getAttribute('firstname');
        $lastname   = $customer->getAttribute('lastname');

//        $customersCollection = Mage::getModel('customer/customer')->getCollection();
//        /* @var $customersCollection Mage_Customer_Model_Entity_Customer_Collection */
//        $firstname = $customersCollection->getAttribute('firstname');
//        $lastname  = $customersCollection->getAttribute('lastname');

        $this->getSelect()
            ->joinLeft(
                array('customer_lastname_table'=>$lastname->getBackend()->getTable()),
                'customer_lastname_table.entity_id=main_table.customer_id
                 AND customer_lastname_table.attribute_id = '.(int) $lastname->getAttributeId() . '
                 ',
                array('customer_lastname'=>'value')
             )
             ->joinLeft(
                array('customer_firstname_table'=>$firstname->getBackend()->getTable()),
                'customer_firstname_table.entity_id=main_table.customer_id
                 AND customer_firstname_table.attribute_id = '.(int) $firstname->getAttributeId() . '
                 ',
                array('customer_firstname'=>'value')
             );

        return $this;
    }

    /**
     * Add type field expression to select
     *
     * @return Mage_Newsletter_Model_Mysql4_Subscriber_Collection
     */
    public function addSubscriberTypeField()
    {
        $this->getSelect()
            ->columns(array('type'=>new Zend_Db_Expr($this->_getMappedField('type'))));
        return $this;
    }

     /**
     * Sets flag for customer info loading on load
     *
     * @param   boolean $show
     * @return  Mage_Newsletter_Model_Mysql4_Subscriber_Collection
     */
    public function showStoreInfo()
    {
        $this->getSelect()->join(
            array('store' => $this->_storeTable),
            'store.store_id = main_table.store_id',
            array('group_id', 'website_id')
        );

        return $this;
    }

    /**
     * @deprecated after 1.4.0.0-rc1
     *
     * @param string $field
     * @return string
     */
    public function _getFieldTableAlias($field)
    {
        if (strpos($field,'customer') === 0) {
           return $field .'_table.value';
        }

        if($field=='type') {
            return new Zend_Db_Expr('IF(main_table.customer_id = 0, 1, 2)');
        }

        if(in_array($field, array('website_id','group_id'))) {
            return 'store.' . $field;
        }

        return 'main_table.' . $field;
    }

     public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();

        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        foreach ($this->_countFilterPart as $where) {
            $countSelect->where($where);
        }


        // TODO: $ql->from('table',new Zend_Db_Expr('COUNT(*)'));
        $sql = $countSelect->__toString();
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(*) from ', $sql);

        return $sql;
    }


    /**
     * Load only subscribed customers
     */
    public function useOnlyCustomers()
    {
        $this->getSelect()->where("main_table.customer_id > 0");

        return $this;
    }

    /**
     * Show only with subscribed status
     */
    public function useOnlySubscribed()
    {
        $this->getSelect()->where("main_table.subscriber_status = ?", Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);

        return $this;
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return Mage_Newsletter_Model_Mysql4_Subscriber_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }
}
