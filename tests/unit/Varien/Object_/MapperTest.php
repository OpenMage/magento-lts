<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Object_;

use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Varien\Object_\MapperTrait;
use Varien_Object;
use Varien_Object_Mapper as Subject;

final class MapperTest extends OpenMageTest
{
    use MapperTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @dataProvider provideAccumulateByMapData
     * @group Varien_Object
     */
    public function testAccumulateByMap($expectedResult, $source, $target, array $map, array $defaults = []): void
    {
        $result = self::$subject::accumulateByMap($source, $target, $map, $defaults);
        if ($target instanceof Varien_Object) {
            self::assertSame($expectedResult, $result->getData());
        } else {
            self::assertSame($expectedResult, $result);
        }
    }
}
