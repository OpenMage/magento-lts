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
use Mage_Core_Helper_Data;
use Mage_Core_Model_Encryption;
use Mage_Core_Model_Locale;
use PHPUnit\Framework\TestCase;
use Varien_Crypt_Mcrypt;
use Varien_Date;

class DataTest extends TestCase
{
    public const TEST_STRING = '1234567890';

    public Mage_Core_Helper_Data $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/data');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetEncryptor(): void
    {
        $this->assertInstanceOf(Mage_Core_Model_Encryption::class, $this->subject->getEncryptor());
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testEncrypt(): void
    {
        $this->assertIsString($this->subject->encrypt('test'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testDecrypt(): void
    {
        $this->assertIsString($this->subject->decrypt('test'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testValidateKey(): void
    {
        $this->assertInstanceOf(Varien_Crypt_Mcrypt::class, $this->subject->validateKey('test'));
    }

    /**
     * @dataProvider provideFormatTimezoneDate
     * @group Mage_Core
     * @group Mage_Core_Helper
     * @group Dates
     */
    public function testFormatTimezoneDate(
        string $expectedResult,
        $data,
        string $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT,
        bool $showTime = false,
        bool $useTimezone = false # disable timezone by default for tests
    ): void {
        $this->assertSame($expectedResult, $this->subject->formatTimezoneDate($data, $format, $showTime, $useTimezone));
    }

    public function provideFormatTimezoneDate(): Generator
    {
        $date           = date_create()->getTimestamp();
        $dateShort      = date('m/j/Y', $date);
        $dateLong       = date('F j, Y', $date);
        $dateShortTime  = date('m/j/Y g:i A', $date);

        yield 'null' => [
            $dateShort,
            null
        ];
        yield 'empty date' => [
            $dateShort,
            ''
        ];
        yield 'string date' => [
            $dateShort,
            'now'
        ];
        yield 'numeric date' => [
            $dateShort,
            '0'
        ];
        yield 'invalid date' => [
            '',
            'invalid'
        ];
        yield 'invalid format' => [
            (string)$date,
            $date,
            'invalid',
        ];
        yield 'date short' => [
            $dateShort,
            $date
        ];
        yield 'date long' => [
            $dateLong,
            $date,
            'long'
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
        $this->assertIsString($this->subject->getRandomString(5));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetHash(): void
    {
        $this->assertIsString($this->subject->getHash('test'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetHashPassword(): void
    {
        $this->assertIsString($this->subject->getHashPassword('test', 1));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testValidateHash(): void
    {
        $this->assertIsBool($this->subject->validateHash('test', '1'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetStoreId(): void
    {
        $this->assertIsInt($this->subject->getStoreId());
    }

    /**
     * @covers Mage_Core_Helper_Data::removeAccents()
     * @dataProvider provideRemoveAccents
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testRemoveAccents(string $expectedResult, string $string, bool $german): void
    {
        $this->assertSame($expectedResult, $this->subject->removeAccents($string, $german));
    }

    public function provideRemoveAccents(): Generator
    {
        $string = 'Ae-Ä Oe-Ö Ue-Ü ae-ä oe-ö ue-ü';

        yield 'german false' => [
            'Ae-A Oe-O Ue-U ae-a oe-o ue-u',
            $string,
            false
        ];
        yield 'german true' => [
            'Ae-Ae Oe-Oe Ue-Ue ae-ae oe-oe ue-ue',
            $string,
            true
        ];
    }

    /**
     * @covers Mage_Core_Helper_Data::isDevAllowed()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testIsDevAllowed(): void
    {
        $this->assertIsBool($this->subject->isDevAllowed());
        $this->markTestIncomplete('add tests for IPS');
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
        $this->assertSame($expectedResult, $this->subject->getCacheTypes());
    }
    /**
     * @covers Mage_Core_Helper_Data::getCacheBetaTypes()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */

    public function testGetCacheBetaTypes(): void
    {
        $expectedResult = [];
        $this->assertSame($expectedResult, $this->subject->getCacheBetaTypes());
    }

    /**
     * @covers Mage_Core_Helper_Data::uniqHash()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testUniqHash(): void
    {
        $prefix = 'string';
        $this->assertStringStartsWith($prefix, $this->subject->uniqHash($prefix));
    }

    /**
     * @covers Mage_Core_Helper_Data::getDefaultCountry()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetDefaultCountry(): void
    {
        $this->assertSame('US', $this->subject->getDefaultCountry());
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
        $this->assertSame($expectedResult, $this->subject->getProtectedFileExtensions());
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
            ]
        ];
        $this->assertSame($expectedResult, $this->subject->getPublicFilesValidPath());
    }

    /**
     * @covers Mage_Core_Helper_Data::useDbCompatibleMode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testUseDbCompatibleMode(): void
    {
        $this->assertTrue($this->subject->useDbCompatibleMode());
    }

    /**
     * @covers Mage_Core_Helper_Data::getMerchantCountryCode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetMerchantCountryCode(): void
    {
        $this->assertIsString($this->subject->getMerchantCountryCode());
    }

    /**
     * @covers Mage_Core_Helper_Data::getMerchantCountryCode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetMerchantVatNumber(): void
    {
        $this->assertIsString($this->subject->getMerchantVatNumber());
    }

    /**
     * @covers Mage_Core_Helper_Data::getMerchantCountryCode()
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testIsCountryInEU(): void
    {
        $this->assertTrue($this->subject->isCountryInEU('DE'));
        $this->assertFalse($this->subject->isCountryInEU('XX'));
        $this->markTestIncomplete('add better tests');
    }
}
