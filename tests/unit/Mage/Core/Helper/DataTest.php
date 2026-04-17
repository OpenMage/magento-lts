<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Data as Subject;
use Mage_Core_Model_Encryption;
use Mage_Core_Model_Locale;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\DataTrait;
use Varien_Crypt_Mcrypt;

final class DataTest extends OpenMageTest
{
    use DataTrait;

    public const TEST_STRING = '1234567890';

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/data');
    }

    /**
     * @group Helper
     */
    public function testGetEncryptor(): void
    {
        self::assertInstanceOf(Mage_Core_Model_Encryption::class, self::$subject->getEncryptor());
    }

    /**
     * @group Helper
     */
    public function testEncrypt(): void
    {
        self::assertIsString(self::$subject->encrypt('test'));
    }

    /**
     * @group Helper
     */
    public function testDecrypt(): void
    {
        self::assertIsString(self::$subject->decrypt('test'));
    }

    /**
     * @group Helper
     */
    public function testValidateKey(): void
    {
        self::assertInstanceOf(Varien_Crypt_Mcrypt::class, self::$subject->validateKey('test'));
    }

    /**
     * @dataProvider provideFormatTimezoneDate
     * @group Helper
     */
    public function testFormatTimezoneDate(
        string $expectedResult,
        null|int|string $data,
        string $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT,
        bool $showTime = false,
        bool $useTimezone = false # disable timezone by default for tests
    ): void {
        self::assertSame($expectedResult, self::$subject->formatTimezoneDate($data, $format, $showTime, $useTimezone));
    }

    /**
     * @group Helper
     */
    public function testGetRandomString(): void
    {
        self::assertIsString(self::$subject->getRandomString(5));
    }

    /**
     * @group Helper
     */
    public function testGetHash(): void
    {
        self::assertIsString(self::$subject->getHash('test'));
    }

    /**
     * @group Helper
     */
    public function testGetHashPassword(): void
    {
        self::assertIsString(self::$subject->getHashPassword('test', 1));
    }

    /**
     * @group Helper
     */
    public function testValidateHash(): void
    {
        self::assertIsBool(self::$subject->validateHash('test', '1'));
    }

    /**
     * @group Helper
     */
    public function testGetStoreId(): void
    {
        self::assertIsInt(self::$subject->getStoreId());
    }

    /**
     * @covers Mage_Core_Helper_Data::removeAccents()
     * @dataProvider provideRemoveAccents
     * @group Helper
     */
    public function testRemoveAccents(string $expectedResult, string $string, bool $german): void
    {
        self::assertSame($expectedResult, self::$subject->removeAccents($string, $german));
    }

    /**
     * @covers Mage_Core_Helper_Data::isDevAllowed()
     * @group Helper
     */
    public function testIsDevAllowed(): void
    {
        self::assertIsBool(self::$subject->isDevAllowed());
        self::markTestIncomplete('add tests for IPS');
    }

    /**
     * @covers Mage_Core_Helper_Data::getCacheTypes()
     * @group Helper
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
        self::assertSame($expectedResult, self::$subject->getCacheTypes());
    }

    /**
     * @covers Mage_Core_Helper_Data::getCacheBetaTypes()
     * @group Helper
     */

    public function testGetCacheBetaTypes(): void
    {
        $expectedResult = [];
        self::assertSame($expectedResult, self::$subject->getCacheBetaTypes());
    }

    /**
     * @covers Mage_Core_Helper_Data::uniqHash()
     * @group Helper
     */
    public function testUniqHash(): void
    {
        $prefix = 'string';
        self::assertStringStartsWith($prefix, self::$subject->uniqHash($prefix));
    }

    /**
     * @covers Mage_Core_Helper_Data::getDefaultCountry()
     * @group Helper
     */
    public function testGetDefaultCountry(): void
    {
        self::assertSame('US', self::$subject->getDefaultCountry());
    }

    /**
     * @covers Mage_Core_Helper_Data::getProtectedFileExtensions()
     * @group Helper
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
        self::assertSame($expectedResult, self::$subject->getProtectedFileExtensions());
    }

    /**
     * @covers Mage_Core_Helper_Data::getPublicFilesValidPath()
     * @group Helper
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
        self::assertSame($expectedResult, self::$subject->getPublicFilesValidPath());
    }

    /**
     * @covers Mage_Core_Helper_Data::useDbCompatibleMode()
     * @group Helper
     */
    public function testUseDbCompatibleMode(): void
    {
        self::assertTrue(self::$subject->useDbCompatibleMode());
    }

    /**
     * @covers Mage_Core_Helper_Data::getMerchantCountryCode()
     * @group Helper
     */
    public function testGetMerchantCountryCode(): void
    {
        self::assertIsString(self::$subject->getMerchantCountryCode());
    }

    /**
     * @covers Mage_Core_Helper_Data::getMerchantCountryCode()
     * @group Helper
     */
    public function testGetMerchantVatNumber(): void
    {
        self::assertIsString(self::$subject->getMerchantVatNumber());
    }

    /**
     * @covers Mage_Core_Helper_Data::getMerchantCountryCode()
     * @dataProvider provideIsCountryInEUData
     * @group Helper
     */
    public function testIsCountryInEU(bool $expectedResult, string $value): void
    {
        self::assertSame($expectedResult, self::$subject->isCountryInEU($value));
    }
}
