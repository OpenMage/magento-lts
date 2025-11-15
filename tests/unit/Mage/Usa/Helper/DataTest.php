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
    public function testConvertMeasureWeight($expectedResult, $value, $sourceWeightMeasure, $toWeightMeasure): void
    {
        $result = self::$subject->convertMeasureWeight($value, $sourceWeightMeasure, $toWeightMeasure);
        #self::assertIsNotFloat($result);
        self::assertIsString($result);
        #self::assertSame($expectedResult, $result);
        self::assertStringStartsWith($expectedResult, $result);
    }

    /**
     * @dataProvider provideConvertMeasureDimensionData
     * @group Helper
     */
    public function testConvertMeasureDimension($expectedResult, $value, $sourceWeightMeasure, $toWeightMeasure): void
    {
        $result = self::$subject->convertMeasureDimension($value, $sourceWeightMeasure, $toWeightMeasure);
        #self::assertIsNotFloat($result);
        self::assertIsString($result);
        #self::assertSame($expectedResult, $result);
        self::assertStringStartsWith($expectedResult, $result);
    }

    /**
     * @dataProvider provideGetMeasureWeightNameData
     * @group Helper
     */
    public function testGetMeasureWeightName($expectedResult, $value): void
    {
        $result = self::$subject->getMeasureWeightName($value);
        self::assertIsString($result);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider provideGetMeasureDimensionNameData
     * @group Helper
     */
    public function testGetMeasureDimensionName($expectedResult, $value): void
    {
        $result = self::$subject->getMeasureDimensionName($value);
        self::assertIsString($result);
        self::assertSame($expectedResult, $result);
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
