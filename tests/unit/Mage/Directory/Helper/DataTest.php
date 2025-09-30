<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Directory\Helper;

use Mage;
use Mage_Directory_Helper_Data as Subject;
use Mage_Directory_Model_Resource_Country_Collection;
use Mage_Directory_Model_Resource_Region_Collection;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Directory\DirectoryTrait;

final class DataTest extends OpenMageTest
{
    use DirectoryTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('directory/data');
    }

    /**
     * @group Helper
     */
    public function testGetRegionCollection(): void
    {
        self::assertInstanceOf(Mage_Directory_Model_Resource_Region_Collection::class, self::$subject->getRegionCollection());
    }

    /**
     * @group Helper
     */
    public function testGetCountryCollection(): void
    {
        self::assertInstanceOf(Mage_Directory_Model_Resource_Country_Collection::class, self::$subject->getCountryCollection());
    }

    /**
     * @group Helper
     */
    public function testGetRegionJsonByStore(): void
    {
        self::assertIsString(self::$subject->getRegionJson());
    }

    /**
     * @group Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testCurrencyConvert(): void
    {
        self::assertSame(10, self::$subject->currencyConvert(10, 'USD'));
    }

    /**
     * @covers Mage_Directory_Helper_Data::getCountriesWithOptionalZip()
     * @dataProvider provideGetCountriesWithOptionalZip
     * @group Helper
     */
    public function testGetCountriesWithOptionalZip(array|string $expectedResult, bool $asJson): void
    {
        self::assertSame($expectedResult, self::$subject->getCountriesWithOptionalZip($asJson));
    }

    /**
     * @covers Mage_Directory_Helper_Data::isZipCodeOptional()
     * @group Helper
     */
    public function testIsZipCodeOptional(): void
    {
        self::assertIsBool(self::$subject->isZipCodeOptional(''));
    }

    /**
     * @covers Mage_Directory_Helper_Data::getCountriesWithStatesRequired()
     * @dataProvider provideGetCountriesWithStatesRequired
     * @group Helper
     */
    public function testGetCountriesWithStatesRequired(array|string $expectedResult, bool $asJson): void
    {
        $result = self::$subject->getCountriesWithStatesRequired($asJson);
        if (defined('DATA_MAY_CHANGED')) {
            $asJson ? self::assertIsString($result) : self::assertIsArray($result);
        } else {
            self::assertSame($expectedResult, $result);
        }
    }

    /**
     * @covers Mage_Directory_Helper_Data::getShowNonRequiredState()
     * @group Helper
     */
    public function testGetShowNonRequiredState(): void
    {
        self::assertTrue(self::$subject->getShowNonRequiredState());
    }

    /**
     * @covers Mage_Directory_Helper_Data::getConfigCurrencyBase()
     * @group Helper
     */
    public function testGetConfigCurrencyBase(): void
    {
        self::assertSame('USD', self::$subject->getConfigCurrencyBase());
    }
}
