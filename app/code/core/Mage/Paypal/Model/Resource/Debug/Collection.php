<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Model_Resource_Debug_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initializes the resource collection.
     */
    protected function _construct(): void
    {
        $this->_init('paypal/debug');
    }

    /**
     * Adds a filter to the collection for a specific quote ID.
     *
     * @param int $quoteId The quote ID to filter by.
     * @return $this
     */
    public function addQuoteIdFilter(int $quoteId): self
    {
        $this->addFieldToFilter('quote_id', $quoteId);
        return $this;
    }

    /**
     * Adds a filter to the collection for a specific transaction ID.
     *
     * @param string $transactionId The transaction ID to filter by.
     * @return $this
     */
    public function addTransactionIdFilter(string $transactionId): self
    {
        $this->addFieldToFilter('transaction_id', $transactionId);
        return $this;
    }

    /**
     * Adds a filter to the collection for a specific action.
     *
     * @param string $action The action string to filter by.
     * @return $this
     */
    public function addActionFilter(string $action): self
    {
        $this->addFieldToFilter('action', $action);
        return $this;
    }

    /**
     * Adds a date range filter to the collection based on the 'created_at' field.
     *
     * @param string|null $from The start date of the range.
     * @param string|null $to The end date of the range.
     * @return $this
     */
    public function addDateRangeFilter(?string $from, ?string $to): self
    {
        if ($from) {
            $this->addFieldToFilter('created_at', ['gteq' => $from]);
        }
        if ($to) {
            $this->addFieldToFilter('created_at', ['lteq' => $to]);
        }
        return $this;
    }

    /**
     * Initializes the select object for the collection, setting the default order.
     *
     * @return $this
     */
    protected function _initSelect(): self
    {
        parent::_initSelect();
        $this->getSelect()->order('created_at DESC');
        return $this;
    }
}
