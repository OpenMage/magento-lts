<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Resource\Collection;

use Mage;
use Mage_Core_Exception;
use Mage_Sales_Model_Resource_Order_Collection;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Resource\Collection\AbstractTrait;
use ReflectionMethod;

final class AbstractTest extends OpenMageTest
{
    use AbstractTrait;

    /**
     * @dataProvider provideAttributeToFieldData
     * @group Model
     */
    public function testAttributeToField(string $expected, mixed $attribute): void
    {
        self::assertSame($expected, $this->attributeToField($attribute));
    }

    /**
     * The exception names what was given, so the caller can be found
     *
     * @dataProvider provideAttributeToFieldExceptionData
     * @group Model
     */
    public function testAttributeToFieldException(string $expectedMessage, mixed $attribute): void
    {
        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->attributeToField($attribute);
    }

    private function attributeToField(mixed $attribute): mixed
    {
        /** @var Mage_Sales_Model_Resource_Order_Collection $subject */
        $subject = Mage::getResourceModel('sales/order_collection');

        return (new ReflectionMethod($subject, '_attributeToField'))->invoke($subject, $attribute);
    }
}
