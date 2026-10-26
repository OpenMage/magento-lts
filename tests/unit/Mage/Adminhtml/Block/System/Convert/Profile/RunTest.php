<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Convert\Profile;

use Iterator;
use Mage;
use Mage_Adminhtml_Block_System_Convert_Profile_Run as Subject;
use Mage_Core_Model_Config;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\System\Convert\Profile\RunTrait;
use Override;

/**
 * GHSA-m858-x59w-rmp5
 */
final class RunTest extends OpenMageTest
{
    use RunTrait;

    private static Subject $subject;

    private static ?string $originalCryptKey = null;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();

        // The HTMLPurifier definition cache is signed with the encryption key
        $cryptKey = (string) self::getConfig()->getNode('global/crypt/key');
        if ($cryptKey === '') {
            self::$originalCryptKey = $cryptKey;
            self::getConfig()->setNode('global/crypt/key', 'ghsa-m858-test-key');
        }
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        if (self::$originalCryptKey !== null) {
            self::getConfig()->setNode('global/crypt/key', self::$originalCryptKey);
        }

        parent::tearDownAfterClass();
    }

    private static function getConfig(): Mage_Core_Model_Config
    {
        $config = Mage::getConfig();
        self::assertInstanceOf(Mage_Core_Model_Config::class, $config);

        return $config;
    }

    /**
     * @dataProvider providePurifyExceptionMessage
     */
    public function testPurifyExceptionMessage(string $expected, string $message): void
    {
        self::assertSame($expected, self::$subject->purifyExceptionMessage($message));
    }

    public static function providePurifyExceptionMessage(): Iterator
    {
        yield 'plain text is escaped' => [
            'Loaded &lt;b&gt;x&lt;/b&gt; &amp; y',
            'Loaded <b>x</b> & y',
        ];
        yield 'status link is kept' => [
            'Saved.<a href="https://example.com/export.csv" target="_blank" rel="noopener noreferrer">Link</a>',
            'Saved.<a href="https://example.com/export.csv" target="_blank" rel="noopener noreferrer">Link</a>',
        ];
        yield 'javascript href is dropped' => [
            'Saved.<a target="_blank" rel="noopener noreferrer">Link</a>',
            'Saved.<a href="javascript:alert(document.cookie)" target="_blank" rel="noopener noreferrer">Link</a>',
        ];
        yield 'attribute breakout from link var' => [
            'Saved.<a href="x" target="_blank" rel="noreferrer noopener">Link</a>',
            'Saved.<a href="x" onmouseover="alert(1)" target="_blank">Link</a>',
        ];
        yield 'script after link is escaped' => [
            'Saved.<a href="x">Link</a>"&gt;&lt;script&gt;alert(1)&lt;/script&gt;',
            'Saved.<a href="x">Link</a>"><script>alert(1)</script>',
        ];
        yield 'img inside link is escaped' => [
            '<a href="x">&lt;img src="y" onerror="alert(1)" /&gt;</a>',
            '<a href="x"><img src=y onerror=alert(1)></a>',
        ];
    }
}
