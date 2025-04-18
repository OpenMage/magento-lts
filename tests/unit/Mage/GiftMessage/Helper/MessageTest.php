<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\GiftMessage\Helper;

use Mage;
use Mage_Core_Model_Store;
use Mage_GiftMessage_Helper_Message as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\GiftMessage\GiftMessageTrait;
use Varien_Object;

class MessageTest extends OpenMageTest
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
     * @group Mage_GiftMessage
     * @group Mage_GiftMessage_Helper
     */
    public function testIsMessagesAvailable(string $type, Varien_Object $entity, bool|int|Mage_Core_Model_Store|null|string $store = null): void
    {
        /** @phpstan-ignore argument.type */
        static::assertIsBool(self::$subject->isMessagesAvailable($type, $entity, $store));
    }
}
