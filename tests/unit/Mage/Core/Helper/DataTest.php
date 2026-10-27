<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Iterator;
use Override;
use Mage;
use Mage_Core_Helper_Data as Subject;
use Mage_Core_Model_Encryption;
use Mage_Core_Model_Locale;
use ReflectionMethod;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\DataTrait;
use Varien_Crypt_Mcrypt;

final class DataTest extends OpenMageTest
{
    use DataTrait;

    public const TEST_STRING = '1234567890';

    private static Subject $subject;

    #[Override]
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

        // Random part is 32 hexadecimal characters, unchanged output shape.
        self::assertMatchesRegularExpression('/^[0-9a-f]{32}$/', self::$subject->uniqHash());
        self::assertMatchesRegularExpression('/^' . $prefix . '[0-9a-f]{32}$/', self::$subject->uniqHash($prefix));

        // Cryptographically random: successive calls must not collide.
        self::assertNotSame(self::$subject->uniqHash(), self::$subject->uniqHash());
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
            'php8' => 'php8',
            'phar' => 'phar',
            'phps' => 'phps',
            'htaccess' => 'htaccess',
            'jsp' => 'jsp',
            'pl' => 'pl',
            'py' => 'py',
            'asp' => 'asp',
            'aspx' => 'aspx',
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
     * A saved general/file/protected_extensions value can extend the protected list but not
     * shrink it below the built-in minimum (GHSA-4fxg-469f-r6fc).
     *
     * @dataProvider provideConfiguredProtectedFileExtensions
     * @group Helper
     * @param array<string, string>|string $configured
     */
    public function testProtectedFileExtensionsKeepTheMinimum($configured, string $extra): void
    {
        $method = new ReflectionMethod(Subject::class, '_addMinimumProtectedFileExtensions');
        $result = $method->invoke(self::$subject, $configured);

        self::assertSame(is_string($configured) ? 'string' : 'array', gettype($result));
        $list = is_string($result) ? explode(',', $result) : array_values($result);
        $list = array_map(static fn(string $extension): string => strtolower(trim($extension)), $list);

        foreach (Subject::PROTECTED_FILE_EXTENSIONS_MINIMUM as $extension) {
            self::assertContains($extension, $list);
        }

        self::assertContains($extra, $list);
        self::assertSame(array_unique($list), $list);
    }

    /**
     * @return Iterator<string, array{(array<string, string> | string), string}>
     */
    public static function provideConfiguredProtectedFileExtensions(): Iterator
    {
        yield 'saved string from an older release' => ['php,php3,php4,php5,htaccess,jsp,pl,py,asp,sh,cgi,htm,html,phtml,shtml', 'html'];
        yield 'saved string with spaces and upper case' => [' PHP , Phtml ,exe', 'exe'];
        yield 'saved string with a merchant addition' => ['php,foo', 'foo'];
        yield 'empty saved string' => ['', 'php'];
        yield 'node list missing new entries' => [['php' => 'php', 'exe' => 'exe'], 'exe'];
        yield 'node list with an emptied entry' => [['php' => '', 'html' => 'html'], 'html'];
    }

    public function testProtectedFileExtensionsLeaveACompleteListUnchanged(): void
    {
        $configured = array_combine(Subject::PROTECTED_FILE_EXTENSIONS_MINIMUM, Subject::PROTECTED_FILE_EXTENSIONS_MINIMUM);
        $configured['html'] = 'html';

        $method = new ReflectionMethod(Subject::class, '_addMinimumProtectedFileExtensions');
        self::assertSame($configured, $method->invoke(self::$subject, $configured));
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
