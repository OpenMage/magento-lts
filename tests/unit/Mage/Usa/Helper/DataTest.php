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
use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;
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
    public function testConvertMeasureWeight(float|string $expectedResult, $value, ?string $sourceWeightMeasure, ?string $toWeightMeasure): void
    {
        try {
            self::assertSame($expectedResult, self::$subject->convertMeasureWeight($value, $sourceWeightMeasure, $toWeightMeasure));
        } catch (NonNumericValue|NonStringUnitName|UnknownUnitOfMeasure $unitOfMeasure) {
            self::assertSame($expectedResult, $unitOfMeasure->getMessage());
        }
    }

    /**
     * @dataProvider provideConvertMeasureDimensionData
     * @group Helper
     */
    public function testConvertMeasureDimension(float|string $expectedResult, $value, ?string $sourceWeightMeasure, ?string $toWeightMeasure): void
    {
        try {
            self::assertSame($expectedResult, self::$subject->convertMeasureDimension($value, $sourceWeightMeasure, $toWeightMeasure));
        } catch (NonNumericValue|NonStringUnitName|UnknownUnitOfMeasure $unitOfMeasure) {
            self::assertSame($expectedResult, $unitOfMeasure->getMessage());
        }
    }

    /**
     * @dataProvider provideGetMeasureWeightNameData
     * @group Helper
     */
    public function testGetMeasureWeightName(string $expectedResult, string $key): void
    {
        try {
            self::assertSame($expectedResult, self::$subject->getMeasureWeightName($key));
        } catch (UnknownUnitOfMeasure $unknownUnitOfMeasure) {
            self::assertSame($expectedResult, $unknownUnitOfMeasure->getMessage());
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
        } catch (UnknownUnitOfMeasure $unknownUnitOfMeasure) {
            self::assertSame($expectedResult, $unknownUnitOfMeasure->getMessage());
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
