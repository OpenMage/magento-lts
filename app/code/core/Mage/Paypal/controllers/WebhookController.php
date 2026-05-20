<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_WebhookController extends Mage_Core_Controller_Front_Action
{
    /**
     * PayPal webhook receiver endpoint: paypal/webhook/index.
     */
    public function indexAction(): void
    {
        if (!$this->getRequest()->isPost()) {
            $this->sendJsonResponse(405, ['error' => 'method_not_allowed']);
            return;
        }

        $config = Mage::getSingleton('paypal/config');
        if (!$config->isWebhookEnabled()) {
            $this->sendJsonResponse(404, ['error' => 'webhook_disabled']);
            return;
        }

        $rawBody = (string) $this->getRequest()->getRawBody();
        $payload = json_decode($rawBody, true);
        if (!is_array($payload)) {
            $this->sendJsonResponse(400, ['error' => 'malformed_json']);
            return;
        }

        try {
            $headers = $this->getPaypalHeaders();
            $verifier = Mage::getModel('paypal/webhook_verifier');
            if (!$verifier->verify($headers, $payload)) {
                $this->sendJsonResponse(401, ['error' => 'signature_verification_failed']);
                return;
            }
        } catch (InvalidArgumentException $exception) {
            $this->sendJsonResponse(400, ['error' => $exception->getMessage()]);
            return;
        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->sendJsonResponse(503, ['error' => 'signature_verification_unavailable']);
            return;
        }

        $eventId = (string) ($payload['id'] ?? '');
        if ($eventId === '' || (string) ($payload['event_type'] ?? '') === '') {
            $this->sendJsonResponse(400, ['error' => 'missing_webhook_event_fields']);
            return;
        }

        $existingEvent = Mage::getModel('paypal/webhook_event')->getCollection()
            ->addWebhookEventIdFilter($eventId)
            ->setPageSize(1)
            ->getFirstItem();
        if ($existingEvent->getId()) {
            $this->sendJsonResponse(200, ['status' => Mage_Paypal_Model_Webhook_Event::STATUS_DUPLICATE]);
            return;
        }

        try {
            $event = Mage::getModel('paypal/webhook_event');
            $event->populateFromPayload($payload, $headers)->save();

            if ($config->shouldProcessWebhooksAsync()) {
                $this->sendJsonResponse(202, ['status' => $event->getData('status')]);
                return;
            }

            Mage::getModel('paypal/webhook_processor')->process($event);
            $this->sendJsonResponse(200, ['status' => $event->getData('status')]);
        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->sendJsonResponse(503, ['error' => 'webhook_processing_unavailable']);
        }
    }

    /**
     * @return array<string, null|string>
     */
    private function getPaypalHeaders(): array
    {
        $headers = [];
        foreach (Mage_Paypal_Model_Webhook_Verifier::REQUIRED_HEADERS as $header) {
            $headers[$header] = $this->getRequest()->getHeader($header);
        }

        return $headers;
    }

    /**
     * @param array<string, mixed> $body
     */
    private function sendJsonResponse(int $statusCode, array $body): void
    {
        $this->getResponse()
            ->setHttpResponseCode($statusCode)
            ->setHeader('Content-Type', 'application/json', true)
            ->setBody((string) json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
