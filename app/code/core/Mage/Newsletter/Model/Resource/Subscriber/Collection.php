<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Newsletter subscribers collection
 *
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Model_Resource_Subscriber_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
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
     * @var bool
     */
    protected $_queueJoinedFlag    = false;

    /**
     * Flag that indicates apply of customers info on load
     *
     * @var bool
     */
    protected $_showCustomersInfo  = false;

    /**
     * Filter for count
     *
     * @var array
     */
    protected $_countFilterPart    = [];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('newsletter/subscriber');
        $this->_queueLinkTable = $this->getTable('newsletter/queue_link');
        $this->_storeTable     = $this->getTable('core/store');

        // defining mapping for fields represented in several tables
        $this->_map['fields']['customer_lastname']   = 'customer_lastname_table.value';
        $this->_map['fields']['customer_middlename'] = 'customer_middlename_table.value';
        $this->_map['fields']['customer_firstname']  = 'customer_firstname_table.value';
        $this->_map['fields']['type']                = $this->getResource()->getReadConnection()
            ->getCheckSql('main_table.customer_id = 0', '1', '2');
        $this->_map['fields']['website_id']          = 'store.website_id';
        $this->_map['fields']['group_id']            = 'store.group_id';
        $this->_map['fields']['store_id']            = 'main_table.store_id';
    }

    /**
     * Set loading mode subscribers by queue
     *
     * @return $this
     */
    public function useQueue(Mage_Newsletter_Model_Queue $queue)
    {
        $this->getSelect()
            ->join(['link' => $this->_queueLinkTable], 'link.subscriber_id = main_table.subscriber_id', [])
            ->where('link.queue_id = ? ', $queue->getId());
        $this->_queueJoinedFlag = true;
        return $this;
    }

    /**
     * Set using of links to only unsendet letter subscribers.
     *
     * @return $this
     */
    public function useOnlyUnsent()
    {
        if ($this->_queueJoinedFlag) {
            $this->addFieldToFilter('link.letter_sent_at', ['null' => 1]);
        }

        return $this;
    }

    /**
     * Adds customer info to select
     *
     * @return $this
     */
    public function showCustomerInfo()
    {
        $adapter    = $this->getConnection();
        $customer   = Mage::getModel('customer/customer');
        $firstname  = $customer->getAttribute('firstname');
        $lastname   = $customer->getAttribute('lastname');
        $middlename = $customer->getAttribute('middlename');

        $this->getSelect()
            ->joinLeft(
                ['customer_lastname_table' => $lastname->getBackend()->getTable()],
                $adapter->quoteInto('customer_lastname_table.entity_id=main_table.customer_id
                    AND customer_lastname_table.attribute_id = ?', (int) $lastname->getAttributeId()),
                ['customer_lastname' => 'value'],
            )
            ->joinLeft(
                ['customer_middlename_table' => $middlename->getBackend()->getTable()],
                $adapter->quoteInto('customer_middlename_table.entity_id=main_table.customer_id
                    AND customer_middlename_table.attribute_id = ?', (int) $middlename->getAttributeId()),
                ['customer_middlename' => 'value'],
            )
            ->joinLeft(
                ['customer_firstname_table' => $firstname->getBackend()->getTable()],
                $adapter->quoteInto('customer_firstname_table.entity_id=main_table.customer_id
                    AND customer_firstname_table.attribute_id = ?', (int) $firstname->getAttributeId()),
                ['customer_firstname' => 'value'],
            );

        return $this;
    }

    /**
     * Add type field expression to select
     *
     * @return $this
     */
    public function addSubscriberTypeField()
    {
        $this->getSelect()
            ->columns(['type' => new Zend_Db_Expr($this->_getMappedField('type'))]);
        return $this;
    }

    /**
     * Sets flag for customer info loading on load
     *
     * @return $this
     */
    public function showStoreInfo()
    {
        $this->getSelect()->join(
            ['store' => $this->_storeTable],
            'store.store_id = main_table.store_id',
            ['group_id', 'website_id'],
        );

        return $this;
    }

    /**
     * Returns field table alias
     *
     * @param  string              $field
     * @return string|Zend_Db_Expr
     * @deprecated after 1.4.0.0-rc1
     */
    public function _getFieldTableAlias($field)
    {
        if (str_starts_with($field, 'customer')) {
            return $field . '_table.value';
        }

        if ($field == 'type') {
            return $this->getConnection()->getCheckSql('main_table.customer_id = 0', '1', '2');
        }

        if (in_array($field, ['website_id', 'group_id'])) {
            return 'store.' . $field;
        }

        return 'main_table.' . $field;
    }

    /**
     * Returns select count sql
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::HAVING);
        return $select;
    }

    /**
     * Load only subscribed customers
     *
     * @return $this
     */
    public function useOnlyCustomers()
    {
        $this->addFieldToFilter('main_table.customer_id', ['gt' => 0]);

        return $this;
    }

    /**
     * Show only with subscribed status
     *
     * @return $this
     */
    public function useOnlySubscribed()
    {
        $this->addFieldToFilter('main_table.subscriber_status', Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);

        return $this;
    }

    /**
     * Filter collection by specified store ids
     *
     * @param  array|int $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->addFieldToFilter('main_table.store_id', ['in' => $storeIds]);
        return $this;
    }
}
