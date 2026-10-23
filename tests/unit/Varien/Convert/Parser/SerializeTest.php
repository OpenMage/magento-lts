<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Convert\Parser;

use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Convert_Parser_Serialize as Subject;

final class SerializeTest extends OpenMageTest
{
    /**
     * @group Varien_Convert
     */
    public function testParseReturnsArrayData(): void
    {
        $subject = new Subject();
        $subject->setData(serialize([['sku' => 'abc', 'qty' => 1]]));

        self::assertSame([['sku' => 'abc', 'qty' => 1]], $subject->parse()->getData());
    }

    /**
     * @group Varien_Convert
     */
    public function testParseDoesNotUnserializeObjects(): void
    {
        SerializeTestFixture::$awakened = false;
        $subject = new Subject();
        $subject->setData(serialize([new SerializeTestFixture()]));

        $subject->parse();

        self::assertFalse(SerializeTestFixture::$awakened);
    }
}
