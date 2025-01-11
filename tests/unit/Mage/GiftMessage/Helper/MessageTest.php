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

use Generator;
use Mage;
use Mage_Catalog_Model_Product;
use Mage_GiftMessage_Helper_Message as Subject;
use PHPUnit\Framework\TestCase;
use Varien_Object;

class MessageTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('giftmessage/message');
    }

    /**
     * @dataProvider provideIsMessagesAvailable
     *
     * @group Mage_GiftMessage
     * @group Mage_GiftMessage_Helper
     */
    public function testIsMessagesAvailable(string $type, Varien_Object $entity, $store = null): void
    {
        $this->assertIsBool($this->subject->isMessagesAvailable($type, $entity, $store));
    }

    public function provideIsMessagesAvailable(): Generator
    {
        $entity = new Varien_Object();

        yield Subject::TYPE_ADDRESS_ITEM => [
            Subject::TYPE_ADDRESS_ITEM,
            $entity,
        ];
        yield Subject::TYPE_ITEM => [
            Subject::TYPE_ITEM,
            $entity->setProduct(new Mage_Catalog_Model_Product()),
        ];
        yield Subject::TYPE_ITEMS => [
            Subject::TYPE_ITEMS,
            $entity,
        ];
        yield Subject::TYPE_ORDER => [
            Subject::TYPE_ORDER,
            $entity,
        ];
        yield Subject::TYPE_ORDER_ITEM => [
            Subject::TYPE_ORDER_ITEM,
            $entity,
        ];
        yield 'invalid type' => [
            'quote',
            $entity,
        ];
    }
}
