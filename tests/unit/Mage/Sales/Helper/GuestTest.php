<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Helper;

use Mage;
use Mage_Core_Controller_Request_Http;
use Mage_Customer_Model_Session;
use Override;
use ReflectionProperty;
use Mage_Sales_Helper_Guest as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class GuestTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('sales/guest');
    }

    /**
     * @covers Mage_Sales_Helper_Guest::getCookieName()
     * @group Helper
     */
    public function testGetCookieName(): void
    {
        self::assertIsString(self::$subject->getCookieName());
    }

    /**
     * A client inside the timeframe of an earlier failed lookup is refused before
     * any order is loaded (GHSA-rqc2-p2h7-6hw9). Before the fix the guess was
     * still evaluated and only the error message depended on the limiter.
     *
     * Runs in a separate process: starting a real session fails under the
     * 20.18.x PHPUnit 9 harness once output has been sent.
     *
     * @covers Mage_Sales_Helper_Guest::loadValidOrder()
     * @group Helper
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testLoadValidOrderRefusesLookupWhenRateLimited(): void
    {
        $this->useRemoteAddr('203.0.113.10');
        Mage::helper('core')->recordRateLimitHit();
        self::assertTrue(Mage::app()->testCache($this->getRateLimitCacheId('203.0.113.10')) !== false);

        $this->setLookupPost([
            'oar_order_id'         => '100000001',
            'oar_type'             => 'zip',
            'oar_billing_lastname' => 'Doe',
            'oar_email'            => '',
            'oar_zip'              => '12345',
        ]);

        Mage::getSingleton('core/session')->getMessages(true);

        self::assertFalse(self::$subject->loadValidOrder());
        self::assertNull(Mage::registry('current_order'));

        // Refused by the limiter, not evaluated and rejected as a wrong guess.
        $messages = array_map(
            static fn($message) => $message->getText(),
            Mage::getSingleton('core/session')->getMessages(true)->getItems(),
        );
        self::assertCount(1, $messages);
        self::assertStringStartsWith('Too Soon', $messages[0]);
    }

    /**
     * A failed form lookup records a rate-limit hit; before the fix only a failed
     * cookie lookup did, so the form could be retried without limit.
     *
     * Runs in a separate process: starting a real session fails under the
     * 20.18.x PHPUnit 9 harness once output has been sent.
     *
     * @covers Mage_Sales_Helper_Guest::loadValidOrder()
     * @group Helper
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testLoadValidOrderRecordsHitOnFailedFormLookup(): void
    {
        $this->useRemoteAddr('203.0.113.11');
        self::assertFalse(Mage::app()->testCache($this->getRateLimitCacheId('203.0.113.11')));

        // Missing last name: rejected by validation, so no database access is needed.
        $this->setLookupPost([
            'oar_order_id'         => '100000001',
            'oar_type'             => 'zip',
            'oar_billing_lastname' => '',
            'oar_email'            => '',
            'oar_zip'              => '12345',
        ]);

        self::assertFalse(self::$subject->loadValidOrder());
        self::assertNotFalse(Mage::app()->testCache($this->getRateLimitCacheId('203.0.113.11')));
    }

    #[Override]
    protected function tearDown(): void
    {
        foreach (['203.0.113.10', '203.0.113.11'] as $ip) {
            Mage::app()->removeCache($this->getRateLimitCacheId($ip));
        }

        Mage::unregister('_singleton/customer/session');
        $this->useRemoteAddr(null);
        Mage::app()->getRequest()->setPost([]);
        parent::tearDown();
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        // The unit suite has no database or website, so stand in a logged-out customer session.
        $customerSession = $this->createPartialMock(Mage_Customer_Model_Session::class, ['isLoggedIn']);
        $customerSession->method('isLoggedIn')->willReturn(false);
        Mage::unregister('_singleton/customer/session');
        Mage::register('_singleton/customer/session', $customerSession);
    }

    private function getRateLimitCacheId(string $remoteAddr): string
    {
        return 'rate_limit_' . $remoteAddr;
    }

    private function useRemoteAddr(?string $remoteAddr): void
    {
        $property = new ReflectionProperty(Mage::helper('core/http'), '_remoteAddr');
        $property->setValue(Mage::helper('core/http'), $remoteAddr);
    }

    private function setLookupPost(array $post): void
    {
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = Mage::app()->getRequest();
        $request->setPost($post);
    }
}
