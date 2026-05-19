<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Model_Webhook_Event_Resolver
{
    /**
     * Resolve the Magento order/payment related to a PayPal webhook event.
     */
    public function resolve(Mage_Paypal_Model_Webhook_Event $event): Varien_Object
    {
        $payload = $event->getPayload();
        $candidateIds = $this->extractCandidateIds($payload, $event);

        $result = $this->resolveByPaymentTransaction($candidateIds);
        if ((bool) $result->getData('is_authoritative')) {
            return $result;
        }

        $result = $this->resolveByPaymentCorrelationId($candidateIds);
        if ((bool) $result->getData('is_authoritative')) {
            return $result;
        }

        $result = $this->resolveByIncrementId($this->extractIncrementIds($payload));
        if ((bool) $result->getData('is_authoritative')) {
            return $result;
        }

        return $this->resolveDebugHint($candidateIds);
    }

    /**
     * @param  array<string, mixed> $payload
     * @return string[]
     */
    public function extractCandidateIds(array $payload, ?Mage_Paypal_Model_Webhook_Event $event = null): array
    {
        $resource = $this->getResourcePayload($payload);
        $relatedIds = $this->extractRelatedIds($resource);
        $ids = [
            $event?->getData('resource_id'),
            $event?->getData('paypal_order_id'),
            $event?->getData('paypal_capture_id'),
            $event?->getData('paypal_authorization_id'),
            $event?->getData('paypal_refund_id'),
            $resource['id'] ?? null,
            $resource['parent_payment'] ?? null,
            $resource['invoice_id'] ?? null,
        ];

        foreach ($relatedIds as $relatedId) {
            $ids[] = $relatedId;
        }

        return $this->filterUniqueStrings($ids);
    }

    /**
     * @param  array<string, mixed> $payload
     * @return string[]
     */
    public function extractIncrementIds(array $payload): array
    {
        $resource = $this->getResourcePayload($payload);
        $ids = [
            $resource['invoice_id'] ?? null,
            $resource['custom_id'] ?? null,
        ];

        $purchaseUnits = $resource['purchase_units'] ?? [];
        if (is_array($purchaseUnits)) {
            foreach ($purchaseUnits as $purchaseUnit) {
                if (!is_array($purchaseUnit)) {
                    continue;
                }

                $ids[] = $purchaseUnit['invoice_id'] ?? null;
                $ids[] = $purchaseUnit['reference_id'] ?? null;
                $ids[] = $purchaseUnit['custom_id'] ?? null;
            }
        }

        return $this->filterUniqueStrings($ids);
    }

    /**
     * @param string[] $ids
     */
    private function resolveByPaymentTransaction(array $ids): Varien_Object
    {
        if ($ids === []) {
            return new Varien_Object(['is_authoritative' => false]);
        }

        $transaction = Mage::getModel('sales/order_payment_transaction')->getCollection()
            ->addFieldToFilter('txn_id', ['in' => $ids])
            ->setPageSize(1)
            ->getFirstItem();

        if (!$transaction->getId()) {
            return new Varien_Object(['is_authoritative' => false]);
        }

        $payment = Mage::getModel('sales/order_payment')->load($transaction->getData('payment_id'));
        $order = Mage::getModel('sales/order')->load($transaction->getData('order_id'));

        return $this->buildResult($order, $payment, true);
    }

    /**
     * Resolve via the PayPal order id stored on the quote payment.
     *
     * paypal_correlation_id is a quote-payment attribute, not an order-payment
     * column, so the lookup goes quote payment -> quote -> order.
     *
     * @param string[] $ids
     */
    private function resolveByPaymentCorrelationId(array $ids): Varien_Object
    {
        if ($ids === []) {
            return new Varien_Object(['is_authoritative' => false]);
        }

        $quotePayment = Mage::getModel('sales/quote_payment')->getCollection()
            ->addFieldToFilter('method', 'paypal')
            ->addFieldToFilter('paypal_correlation_id', ['in' => $ids])
            ->setPageSize(1)
            ->getFirstItem();

        $quoteId = $quotePayment->getData('quote_id');
        if (!$quoteId) {
            return new Varien_Object(['is_authoritative' => false]);
        }

        $order = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('quote_id', $quoteId)
            ->setPageSize(1)
            ->getFirstItem();

        if (!$order->getId()) {
            return new Varien_Object(['is_authoritative' => false]);
        }

        return $this->buildResult($order, $order->getPayment(), true);
    }

    /**
     * @param string[] $incrementIds
     */
    private function resolveByIncrementId(array $incrementIds): Varien_Object
    {
        foreach ($incrementIds as $incrementId) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
            if (!$order->getId()) {
                continue;
            }

            return $this->buildResult($order, $order->getPayment(), true);
        }

        return new Varien_Object(['is_authoritative' => false]);
    }

    /**
     * @param string[] $ids
     */
    private function resolveDebugHint(array $ids): Varien_Object
    {
        if ($ids === []) {
            return new Varien_Object(['is_authoritative' => false]);
        }

        $debug = Mage::getModel('paypal/debug')->getCollection()
            ->addFieldToFilter('transaction_id', ['in' => $ids])
            ->setPageSize(1)
            ->getFirstItem();

        if (!$debug->getId()) {
            return new Varien_Object(['is_authoritative' => false]);
        }

        return new Varien_Object([
            'increment_id'      => $debug->getData('increment_id'),
            'is_authoritative'  => false,
        ]);
    }

    private function buildResult(
        Mage_Sales_Model_Order $order,
        ?Mage_Sales_Model_Order_Payment $payment,
        bool $authoritative
    ): Varien_Object {
        if (!$order->getId()) {
            return new Varien_Object(['is_authoritative' => false]);
        }

        return new Varien_Object([
            'order'            => $order,
            'payment'          => $payment,
            'order_id'         => (int) $order->getId(),
            'payment_id'       => $payment?->getId() ? (int) $payment->getId() : null,
            'increment_id'     => (string) $order->getIncrementId(),
            'is_authoritative' => $authoritative,
        ]);
    }

    /**
     * @param  array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function getResourcePayload(array $payload): array
    {
        $resource = $payload['resource'] ?? [];
        return is_array($resource) ? $resource : [];
    }

    /**
     * @param  array<string, mixed>  $resource
     * @return array<string, string>
     */
    private function extractRelatedIds(array $resource): array
    {
        $relatedIds = $resource['supplementary_data']['related_ids'] ?? [];
        if (!is_array($relatedIds)) {
            return [];
        }

        return array_filter(
            array_map(strval(...), $relatedIds),
            static fn(string $value): bool => $value !== '',
        );
    }

    /**
     * @param  array<int, mixed> $values
     * @return string[]
     */
    private function filterUniqueStrings(array $values): array
    {
        $strings = [];
        foreach ($values as $value) {
            if (!is_scalar($value)) {
                continue;
            }

            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            if (in_array($value, $strings, true)) {
                continue;
            }

            $strings[] = $value;
        }

        return $strings;
    }
}
