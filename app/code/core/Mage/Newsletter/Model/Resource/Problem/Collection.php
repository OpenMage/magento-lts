<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter problems collection
 *
 * @category   Mage
 * @package    Mage_Newsletter
 *
 * @method Mage_Newsletter_Model_Problem[] getItems()
 * @method Mage_Newsletter_Model_Problem[] getItemsByColumnValue(string $column, string $value)
 * @method $this setCustomerFirstName(string $value)
 * @method $this setCustomerLastName(string $value)
 */
class Mage_Newsletter_Model_Resource_Problem_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * True when subscribers info joined
     *
     * @var bool
     */
    protected $_subscribersInfoJoinedFlag  = false;

    /**
     * True when grouped
     *
     * @var bool
     */
    protected $_problemGrouped             = false;

    /**
     * Define resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('newsletter/problem');
    }

    /**
     * Adds subscribers info
     *
     * @return $this
     */
    public function addSubscriberInfo()
    {
        $this->getSelect()->joinLeft(
            ['subscriber' => $this->getTable('newsletter/subscriber')],
            'main_table.subscriber_id = subscriber.subscriber_id',
            ['subscriber_email','customer_id','subscriber_status'],
        );
        $this->addFilterToMap('subscriber_id', 'main_table.subscriber_id');
        $this->_subscribersInfoJoinedFlag = true;

        return $this;
    }

    /**
     * Adds queue info
     *
     * @return $this
     */
    public function addQueueInfo()
    {
        $this->getSelect()->joinLeft(
            ['queue' => $this->getTable('newsletter/queue')],
            'main_table.queue_id = queue.queue_id',
            ['queue_start_at', 'queue_finish_at'],
        )
        ->joinLeft(
            ['template' => $this->getTable('newsletter/template')],
            'queue.template_id = template.template_id',
            ['template_subject','template_code','template_sender_name','template_sender_email'],
        );
        return $this;
    }

    /**
     * Loads customers info to collection
     *
     */
    protected function _addCustomersData()
    {
        $customersIds = [];

        foreach ($this->getItems() as $item) {
            if ($item->getCustomerId()) {
                $customersIds[] = $item->getCustomerId();
            }
        }

        if (count($customersIds) == 0) {
            return;
        }

        $customers = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToFilter('entity_id', ['in' => $customersIds]);

        $customers->load();

        foreach ($customers->getItems() as $customer) {
            $problems = $this->getItemsByColumnValue('customer_id', $customer->getId());
            foreach ($problems as $problem) {
                $problem->setCustomerName($customer->getName())
                    ->setCustomerFirstName($customer->getFirstName())
                    ->setCustomerLastName($customer->getLastName());
            }
        }
    }

    /**
     * Loads collection and adds customers info
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        if ($this->_subscribersInfoJoinedFlag && !$this->isLoaded()) {
            $this->_addCustomersData();
        }
        return $this;
    }
}
