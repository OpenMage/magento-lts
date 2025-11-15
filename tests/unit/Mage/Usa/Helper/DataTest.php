<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Usa\Helper;

use Mage;
use Mage_Usa_Helper_Data as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Helper\DataTrait;
use PhpUnitsOfMeasure\Exception\UnknownUnitOfMeasure;

final class DataTest extends OpenMageTest
{
    use DataTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('usa/data');
    }

    /**
     * @dataProvider provideConvertMeasureWeightData
     * @group Helper
     */
    public function testConvertMeasureWeight(float $expectedResult, $value, string $sourceWeightMeasure, string $toWeightMeasure): void
    {
        $result = self::$subject->convertMeasureWeight($value, $sourceWeightMeasure, $toWeightMeasure);

        self::assertIsFloat($result);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider provideConvertMeasureDimensionData
     * @group Helper
     */
    public function testConvertMeasureDimension(float $expectedResult, $value, string $sourceWeightMeasure, string $toWeightMeasure): void
    {
        $result = self::$subject->convertMeasureDimension($value, $sourceWeightMeasure, $toWeightMeasure);

        self::assertIsFloat($result);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider provideGetMeasureWeightNameData
     * @group Helper
     */
    public function testGetMeasureWeightName(string $expectedResult, string $eey): void
    {
        try {
            self::assertSame($expectedResult, self::$subject->getMeasureWeightName($eey));
        } catch (UnknownUnitOfMeasure $unitOfMeasure) {
            self::assertSame($expectedResult, $unitOfMeasure->getMessage());
        }
    }

    /**
     * @dataProvider provideGetMeasureDimensionNameData
     * @group Helper
     */
    public function testGetMeasureDimensionName(string $expectedResult, string $key): void
    {
        try {
            self::assertSame($expectedResult, self::$subject->getMeasureDimensionName($key));
        } catch (UnknownUnitOfMeasure $unitOfMeasure) {
            self::assertSame($expectedResult, $unitOfMeasure->getMessage());
        }
    }

    /**
     * @dataProvider provideDisplayGirthValueData
     * @group Helper
     */
    public function testDisplayGirthValue(bool $expectedResult, string $value): void
    {
        self::assertSame($expectedResult, self::$subject->displayGirthValue($value));
    }

    public function testCalidateUpsType(): void
    {
        self::assertIsBool(self::$subject->validateUpsType('invalid'));
    }
}
