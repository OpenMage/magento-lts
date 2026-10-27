<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Model_Resource_Webhook_Event extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    #[Override]
    protected function _construct(): void
    {
        $this->_init('paypal/webhook_event', 'entity_id');
    }

    /**
     * Begin a write transaction and take a pessimistic row lock on the given
     * event, so concurrent workers cannot process the same webhook event twice.
     *
     * The caller owns the returned connection and must commit or roll it back.
     */
    public function lockEvent(int $eventId): Varien_Db_Adapter_Interface
    {
        $connection = $this->_getWriteAdapter();
        $connection->beginTransaction();
        $connection->query(
            sprintf(
                'SELECT entity_id FROM %s WHERE entity_id = ? FOR UPDATE',
                $connection->quoteIdentifier($this->getMainTable()),
            ),
            [$eventId],
        );

        return $connection;
    }
}
