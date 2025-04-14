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

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Generator;
use Mage;
use Mage_Core_Helper_Data as Subject;
use Mage_Core_Model_Encryption;
use Mage_Core_Model_Locale;
use PHPUnit\Framework\TestCase;
use Varien_Crypt_Mcrypt;

class DataTest extends TestCase
{
    public const TEST_STRING = '1234567890';

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = Mage::helper('core/data');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetEncryptor(): void
    {
        static::assertInstanceOf(Mage_Core_Model_Encryption::class, self::$subject->getEncryptor());
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testEncrypt(): void
    {
        static::assertIsString(self::$subject->encrypt('test'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testDecrypt(): void
    {
        static::assertIsString(self::$subject->decrypt('test'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testValidateKey(): void
    {
        static::assertInstanceOf(Varien_Crypt_Mcrypt::class, self::$subject->validateKey('test'));
    }

    /**
     * @dataProvider provideFormatTimezoneDate
     * @group Mage_Core
     * @group Mage_Core_Helper
     * @group Dates
     */
    public function testFormatTimezoneDate(
        string $expectedResult,
        ?string $data,
        string $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT,
        bool $showTime = false,
        bool $useTimezone = false # disable timezone by default for tests
    ): void {
        static::assertSame($expectedResult, self::$subject->formatTimezoneDate($data, $format, $showTime, $useTimezone));
    }

    public function provideFormatTimezoneDate(): Generator
    {
        $date           = date_create()->getTimestamp();
        $dateShort      = date('n/j/Y', $date);
        $dateLong       = date('F j, Y', $date);
        $dateShortTime  = date('n/j/Y g:i A', $date);

        yield 'null' => [
            $dateShort,
            null,
        ];
        yield 'empty date' => [
            $dateShort,
            '',
        ];
        yield 'string date' => [
            $dateShort,
            'now',
        ];
        yield 'numeric date' => [
            $dateShort,
            '0',
        ];
        yield 'invalid date' => [
            '',
            'invalid',
        ];
        yield 'invalid format' => [
            (string) $date,
            $date,
            'invalid',
        ];
        yield 'date short' => [
            $dateShort,
            $date,
        ];
        yield 'date long' => [
            $dateLong,
            $date,
            'long',
        ];
        //        yield 'date short w/ time' => [
        //            $dateShortTime,
        //            $date,
        //            'short',
        //            true,
        //        ];
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetRandomString(): void
    {
        static::assertIsString(self::$subject->getRandomString(5));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetHash(): void
    {
        static::assertIsString(self::$subject->getHash('test'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetHashPassword(): void
    {
        static::assertIsString(self::$subject->getHashPassword('test', 1));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testValidateHash(): void
    {
        static::assertIsBool(self::$subject->validateHash('test', '1'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetStoreId(): void
    {
        static::assertIsInt(self::$subject->getStoreId());
    }

    /**
     * @covers Mage_Core_Helper_Data::removeAccents()
     * @dataProvider provideRemoveAccents
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testRemoveAccents(string $expectedResult, string $string, bool $german): void
    {
        static::assertSame($expectedResult, self::$subject->removeAccents($string, $german));
    }

    public function provideRemoveAccents(): Generator
    {
        $string = 'Ae-Ä Oe-Ö Ue-Ü ae-ä oe-ö ue-ü';

        yield 'german false' => [
            'Ae-A Oe-O Ue-U ae-a oe-o ue-u',
            $string,
            false,
        ];
        yield 'german true' => [
            'Ae-Ae Oe-Oe Ue-Ue ae-ae oe-oe ue-ue',
            $string,
            true,
        ];
    }

    /**
     * @covers Mage_Core_Helper_Data::isDevAllowed()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testIsDevAllowed(): void
    {
        static::assertIsBool(self::$subject->isDevAllowed());
        static::markTestIncomplete('add tests for IPS');
    }

    /**
     * @covers Mage_Core_Helper_Data::getCacheTypes()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetCacheTypes(): void
    {
        $expectedResult = [
            'config' => 'Configuration',
            'layout' => 'Layouts',
            'block_html' => 'Blocks HTML output',
            'translate' => 'Translations',
            'collections' => 'Collections Data',
            'eav' => 'EAV types and attributes',
            'config_api' => 'Web Services Configuration',
            'config_api2' => 'Web Services Configuration',

        ];
        static::assertSame($expectedResult, self::$subject->getCacheTypes());
    }
    /**
     * @covers Mage_Core_Helper_Data::getCacheBetaTypes()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */

    public function testGetCacheBetaTypes(): void
    {
        $expectedResult = [];
        static::assertSame($expectedResult, self::$subject->getCacheBetaTypes());
    }

    /**
     * @covers Mage_Core_Helper_Data::uniqHash()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testUniqHash(): void
    {
        $prefix = 'string';
        static::assertStringStartsWith($prefix, self::$subject->uniqHash($prefix));
    }

    /**
     * @covers Mage_Core_Helper_Data::getDefaultCountry()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetDefaultCountry(): void
    {
        static::assertSame('US', self::$subject->getDefaultCountry());
    }

    /**
     * @covers Mage_Core_Helper_Data::getProtectedFileExtensions()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetProtectedFileExtensions(): void
    {
        $expectedResult = [
            'php' => 'php',
            'php3' => 'php3',
            'php4' => 'php4',
            'php5' => 'php5',
            'php7' => 'php7',
            'htaccess' => 'htaccess',
            'jsp' => 'jsp',
            'pl' => 'pl',
            'py' => 'py',
            'asp' => 'asp',
            'sh' => 'sh',
            'cgi' => 'cgi',
            'htm' => 'htm',
            'html' => 'html',
            'pht' => 'pht',
            'phtml' => 'phtml',
            'shtml' => 'shtml',
        ];
        static::assertSame($expectedResult, self::$subject->getProtectedFileExtensions());
    }

    /**
     * @covers Mage_Core_Helper_Data::getPublicFilesValidPath()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetPublicFilesValidPath(): void
    {
        $expectedResult = [
            'protected' => [
                'app' => '/app/*/*',
                'dev' => '/dev/*/*',
                'errors' => '/errors/*/*',
                'js' => '/js/*/*',
                'lib' => '/lib/*/*',
                'shell' => '/shell/*/*',
                'skin' => '/skin/*/*',
            ],
        ];
        static::assertSame($expectedResult, self::$subject->getPublicFilesValidPath());
    }

    /**
     * @covers Mage_Core_Helper_Data::useDbCompatibleMode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testUseDbCompatibleMode(): void
    {
        static::assertTrue(self::$subject->useDbCompatibleMode());
    }

    /**
     * @covers Mage_Core_Helper_Data::getMerchantCountryCode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetMerchantCountryCode(): void
    {
        static::assertIsString(self::$subject->getMerchantCountryCode());
    }

    /**
     * @covers Mage_Core_Helper_Data::getMerchantCountryCode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetMerchantVatNumber(): void
    {
        static::assertIsString(self::$subject->getMerchantVatNumber());
    }

    /**
     * @covers Mage_Core_Helper_Data::getMerchantCountryCode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testIsCountryInEU(): void
    {
        static::assertTrue(self::$subject->isCountryInEU('DE'));
        static::assertFalse(self::$subject->isCountryInEU('XX'));
        static::markTestIncomplete('add better tests');
    }
}
