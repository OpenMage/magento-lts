<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

use Carbon\Carbon;

class Mage_Paypal_Model_Resource_Webhook_Event_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    #[Override]
    protected function _construct(): void
    {
        $this->_init('paypal/webhook_event');
    }

    public function addWebhookEventIdFilter(string $eventId): self
    {
        $this->addFieldToFilter('webhook_event_id', $eventId);
        return $this;
    }

    public function addProcessableFilter(int $retryLimit): self
    {
        $connection = $this->getConnection();
        $processableStatuses = [
            Mage_Paypal_Model_Webhook_Event::STATUS_RECEIVED,
            Mage_Paypal_Model_Webhook_Event::STATUS_VERIFIED,
            Mage_Paypal_Model_Webhook_Event::STATUS_DEFERRED,
        ];

        $this->getSelect()->where(
            sprintf(
                '(main_table.status IN (%s) OR (main_table.status = %s AND main_table.processing_attempts < %d))',
                $connection->quote($processableStatuses),
                $connection->quote(Mage_Paypal_Model_Webhook_Event::STATUS_FAILED),
                max(0, $retryLimit),
            ),
        );

        $this->setOrder('created_at', self::SORT_ORDER_ASC);
        return $this;
    }

    public function addRetentionFilter(int $retentionDays): self
    {
        $connection = $this->getConnection();
        $cutoff = gmdate(
            Varien_Date::DATETIME_PHP_FORMAT,
            Carbon::now()->subDays(max(1, $retentionDays))->getTimestamp(),
        );

        $this->addFieldToFilter('status', [
            'in' => [
                Mage_Paypal_Model_Webhook_Event::STATUS_PROCESSED,
                Mage_Paypal_Model_Webhook_Event::STATUS_IGNORED,
            ],
        ]);
        $this->getSelect()->where('main_table.created_at < ' . $connection->formatDate($cutoff));

        return $this;
    }
}
