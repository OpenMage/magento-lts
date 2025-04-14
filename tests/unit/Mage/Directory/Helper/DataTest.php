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
 * @copyright  Copyright (c) 2024-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Directory\Helper;

use Generator;
use Mage;
use Mage_Directory_Helper_Data as Subject;
use Mage_Directory_Model_Resource_Country_Collection;
use Mage_Directory_Model_Resource_Region_Collection;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = Mage::helper('directory/data');
    }

    /**
     * @group Mage_Directory
     * @group Mage_Directory_Helper
     */
    public function testGetRegionCollection(): void
    {
        static::assertInstanceOf(Mage_Directory_Model_Resource_Region_Collection::class, self::$subject->getRegionCollection());
    }

    /**
     * @group Mage_Directory
     * @group Mage_Directory_Helper
     */
    public function testGetCountryCollection(): void
    {
        static::assertInstanceOf(Mage_Directory_Model_Resource_Country_Collection::class, self::$subject->getCountryCollection());
    }

    /**
     * @group Mage_Directory
     * @group Mage_Directory_Helper
     */
    public function testGetRegionJsonByStore(): void
    {
        static::assertIsString(self::$subject->getRegionJson());
    }

    /**
     * @group Mage_Directory
     * @group Mage_Directory_Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testCurrencyConvert(): void
    {
        static::assertSame(10, self::$subject->currencyConvert(10, 'USD'));
    }

    /**
     * @covers Mage_Directory_Helper_Data::getCountriesWithOptionalZip()
     * @dataProvider provideGetCountriesWithOptionalZip
     * @group Mage_Directory
     * @group Mage_Directory_Helper
     */
    public function testGetCountriesWithOptionalZip(array|string $expectedResult, bool $asJson): void
    {
        static::assertSame($expectedResult, self::$subject->getCountriesWithOptionalZip($asJson));
    }

    public function provideGetCountriesWithOptionalZip(): Generator
    {
        yield 'as json' => [
            '["HK","IE","MO","PA"]',
            true,
        ];
        yield 'as array' => [
            [
                0 => 'HK',
                1 => 'IE',
                2 => 'MO',
                3 => 'PA',
            ],
            false,
        ];
    }

    /**
     * @covers Mage_Directory_Helper_Data::isZipCodeOptional()
     * @group Mage_Directory
     * @group Mage_Directory_Helper
     */
    public function testIsZipCodeOptional(): void
    {
        static::assertIsBool(self::$subject->isZipCodeOptional(''));
    }

    /**
     * @covers Mage_Directory_Helper_Data::getCountriesWithStatesRequired()
     * @dataProvider provideGetCountriesWithStatesRequired
     * @group Mage_Directory
     * @group Mage_Directory_Helper
     */
    public function testGetCountriesWithStatesRequired(array|string $expectedResult, bool $asJson): void
    {
        $result = self::$subject->getCountriesWithStatesRequired($asJson);
        if (defined('DATA_MAY_CHANGED')) {
            $asJson ? static::assertIsString($result) : static::assertIsArray($result);
        } else {
            static::assertSame($expectedResult, $result);
        }
    }

    public function provideGetCountriesWithStatesRequired(): Generator
    {
        yield 'as json' => [
            '["AT","CA","CH","DE","EE","ES","FI","FR","LT","LV","RO","US"]',
            true,
        ];
        yield 'as array' => [
            [
                0 => 'AT',
                1 => 'CA',
                2 => 'CH',
                3 => 'DE',
                4 => 'EE',
                5 => 'ES',
                6 => 'FI',
                7 => 'FR',
                8 => 'LT',
                9 => 'LV',
                10 => 'RO',
                11 => 'US',
            ],
            false,
        ];
    }

    /**
     * @covers Mage_Directory_Helper_Data::getShowNonRequiredState()
     * @group Mage_Directory
     * @group Mage_Directory_Helper
     */
    public function testGetShowNonRequiredState(): void
    {
        static::assertTrue(self::$subject->getShowNonRequiredState());
    }

    /**
     * @covers Mage_Directory_Helper_Data::getConfigCurrencyBase()
     * @group Mage_Directory
     * @group Mage_Directory_Helper
     */
    public function testGetConfigCurrencyBase(): void
    {
        static::assertSame('USD', self::$subject->getConfigCurrencyBase());
    }
}
