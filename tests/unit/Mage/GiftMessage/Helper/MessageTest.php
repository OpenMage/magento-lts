<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\GiftMessage\Helper;

use Mage;
use Mage_Core_Model_Store;
use Mage_GiftMessage_Helper_Message as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\GiftMessage\GiftMessageTrait;
use Varien_Object;

final class MessageTest extends OpenMageTest
{
    use GiftMessageTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('giftmessage/message');
    }

    /**
     * @dataProvider provideIsMessagesAvailable
     *
     * @group Helper
     */
    public function testIsMessagesAvailable(string $type, Varien_Object $entity, bool|int|Mage_Core_Model_Store|null|string $store = null): void
    {
        self::assertIsBool(self::$subject->isMessagesAvailable($type, $entity, $store));
    }
}
