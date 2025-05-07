<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\GiftMessage;

use Generator;
use Mage_Catalog_Model_Product;
use Mage_GiftMessage_Helper_Message as Subject;
use Varien_Object;

trait GiftMessageTrait
{
    public function provideIsMessagesAvailable(): Generator
    {
        $entity = new Varien_Object();

        yield Subject::TYPE_ADDRESS_ITEM => [
            Subject::TYPE_ADDRESS_ITEM,
            $entity,
        ];
        yield Subject::TYPE_ITEM => [
            Subject::TYPE_ITEM,
            $entity->setData('product', new Mage_Catalog_Model_Product()),
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
