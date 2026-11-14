<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Rss\Helper;

use Mage;
use Mage_Rss_Helper_Order as Subject;
use Mage_Sales_Model_Order;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Rss\Helper\OrderTrait;

final class OrderTest extends OpenMageTest
{
    use OrderTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('rss/order');
    }

    private function order(string $protectCode): Mage_Sales_Model_Order
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order');
        $order->setData([
            'entity_id' => 1234,
            'increment_id' => '100001234',
            'customer_id' => null,
            'protect_code' => $protectCode,
        ]);

        return $order;
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(string $key): array
    {
        return json_decode((string) base64_decode($key, true), true);
    }

    /**
     * @group Helper
     */
    public function testStatusUrlKeyCarriesADigest(): void
    {
        $data = $this->decode(self::$subject->getStatusUrlKey($this->order('abcdefghijklmnop')));

        self::assertArrayHasKey('hash', $data);
        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $data['hash']);
    }

    /**
     * The feed URL travels through Referer headers, browser history and feed
     * readers. It must authenticate the request without handing over the secret
     * that also unlocks the guest order view.
     *
     * @group Helper
     */
    public function testStatusUrlKeyDoesNotLeakTheProtectCode(): void
    {
        $protectCode = 'abcdefghijklmnop';
        $key = self::$subject->getStatusUrlKey($this->order($protectCode));

        self::assertStringNotContainsString($protectCode, (string) $key);
        self::assertStringNotContainsString($protectCode, (string) base64_decode($key, true));
    }

    /**
     * The whole point of the change: identical order identifiers no longer
     * produce an identical, derivable key.
     *
     * @group Helper
     */
    public function testStatusUrlKeyDependsOnTheProtectCode(): void
    {
        $first  = $this->decode(self::$subject->getStatusUrlKey($this->order('abcdefghijklmnop')));
        $second = $this->decode(self::$subject->getStatusUrlKey($this->order('ponmlkjihgfedcba')));

        self::assertSame($first['order_id'], $second['order_id']);
        self::assertSame($first['increment_id'], $second['increment_id']);
        self::assertNotSame($first['hash'], $second['hash']);
    }

    /**
     * A key built the old way -- order_id, increment_id and customer_id with no
     * secret -- is exactly the forgery this advisory is about.
     *
     * @group Helper
     */
    public function testAKeyWithoutADigestIsRejected(): void
    {
        $forged = base64_encode((string) json_encode([
            'order_id' => 1234,
            'increment_id' => '100001234',
            'customer_id' => 0,
        ]));

        self::assertNull(self::$subject->getOrderByStatusUrlKey($forged));
    }

    /**
     * @group Helper
     */
    public function testMalformedKeysAreRejected(): void
    {
        self::assertNull(self::$subject->getOrderByStatusUrlKey(''));
        self::assertNull(self::$subject->getOrderByStatusUrlKey('not base64 at all !!'));
        self::assertNull(self::$subject->getOrderByStatusUrlKey(base64_encode('not json')));
        self::assertNull(self::$subject->getOrderByStatusUrlKey(base64_encode('[1,2,3]')));
    }
}
