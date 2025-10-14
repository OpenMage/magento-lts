<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Newsletter queue collection.
 *
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Model_Resource_Queue_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * True when subscribers info joined
     *
     * @var bool
     */
    protected $_addSubscribersFlag   = false;

    /**
     * True when filtered by store
     *
     * @var bool
     */
    protected $_isStoreFilter        = false;

    /**
     * Initializes collection
     *
     */
    protected function _construct()
    {
        $this->_map['fields']['queue_id'] = 'main_table.queue_id';
        $this->_init('newsletter/queue');
    }

    /**
     * Joines templates information
     *
     * @deprecated since 1.4.0.1
     *
     * @return $this
     */
    public function addTemplateInfo()
    {
        $this->getSelect()->joinLeft(
            ['template' => $this->getTable('template')],
            'template.template_id=main_table.template_id',
            ['template_subject','template_sender_name','template_sender_email'],
        );
        $this->_joinedTables['template'] = true;
        return $this;
    }

    /**
     * Adds subscribers info to selelect
     *
     * @return $this
     */
    protected function _addSubscriberInfoToSelect()
    {
        /** @var Varien_Db_Select $select */
        $select = $this->getConnection()->select()
            ->from(['qlt' => $this->getTable('newsletter/queue_link')], 'COUNT(qlt.queue_link_id)')
            ->where('qlt.queue_id = main_table.queue_id');
        $totalExpr = new Zend_Db_Expr(sprintf('(%s)', $select->assemble()));
        $select = $this->getConnection()->select()
            ->from(['qls' => $this->getTable('newsletter/queue_link')], 'COUNT(qls.queue_link_id)')
            ->where('qls.queue_id = main_table.queue_id')
            ->where('qls.letter_sent_at IS NOT NULL');
        $sentExpr  = new Zend_Db_Expr(sprintf('(%s)', $select->assemble()));

        $this->getSelect()->columns([
            'subscribers_sent'  => $sentExpr,
            'subscribers_total' => $totalExpr,
        ]);
        return $this;
    }

    /**
     * Adds subscribers info to select and loads collection
     *
     * @inheritDoc
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->_addSubscribersFlag && !$this->isLoaded()) {
            $this->_addSubscriberInfoToSelect();
        }

        return parent::load($printQuery, $logQuery);
    }

    /**
     * Joines subscribers information
     *
     * @return $this
     */
    public function addSubscribersInfo()
    {
        $this->_addSubscribersFlag = true;
        return $this;
    }

    /**
     * Checks if field is 'subscribers_total', 'subscribers_sent'
     * to add specific filter or adds reguler filter
     *
     * @inheritDoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, ['subscribers_total', 'subscribers_sent'])) {
            $this->addFieldToFilter('main_table.queue_id', ['in' => $this->_getIdsFromLink($field, $condition)]);
            return $this;
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }

    /**
     * Returns ids from queue_link table
     *
     * @param string $field
     * @param mixed $condition
     * @return array
     */
    protected function _getIdsFromLink($field, $condition)
    {
        $select = $this->getConnection()->select()
            ->from(
                $this->getTable('newsletter/queue_link'),
                ['queue_id', 'total' => new Zend_Db_Expr('COUNT(queue_link_id)')],
            )
            ->group('queue_id')
            ->having($this->_getConditionSql('total', $condition));

        if ($field == 'subscribers_sent') {
            $select->where('letter_sent_at IS NOT NULL');
        }

        $idList = $this->getConnection()->fetchCol($select);

        if (count($idList)) {
            return $idList;
        }

        return [0];
    }

    /**
     * Set filter for queue by subscriber.
     *
     * @param int $subscriberId
     * @return $this
     */
    public function addSubscriberFilter($subscriberId)
    {
        $this->getSelect()->join(
            ['link' => $this->getTable('newsletter/queue_link')],
            'main_table.queue_id=link.queue_id',
            ['letter_sent_at'],
        )
        ->where('link.subscriber_id = ?', $subscriberId);

        return $this;
    }

    /**
     * Add filter by only ready for sending item
     *
     * @return $this
     */
    public function addOnlyForSendingFilter()
    {
        $this->getSelect()
            ->where('main_table.queue_status in (?)', [Mage_Newsletter_Model_Queue::STATUS_SENDING,
                Mage_Newsletter_Model_Queue::STATUS_NEVER])
            ->where('main_table.queue_start_at < ?', Mage::getSingleton('core/date')->gmtDate())
            ->where('main_table.queue_start_at IS NOT NULL');

        return $this;
    }

    /**
     * Add filter by only not sent items
     *
     * @return $this
     */
    public function addOnlyUnsentFilter()
    {
        $this->addFieldToFilter('main_table.queue_status', Mage_Newsletter_Model_Queue::STATUS_NEVER);

        return $this;
    }

    /**
     * Returns options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('queue_id', 'template_subject');
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        if (!$this->_isStoreFilter) {
            $this->getSelect()->joinInner(
                ['store_link' => $this->getTable('newsletter/queue_store_link')],
                'main_table.queue_id = store_link.queue_id',
                [],
            )
            ->where('store_link.store_id IN (?)', $storeIds)
            ->group('main_table.queue_id');
            $this->_isStoreFilter = true;
        }

        return $this;
    }
}
