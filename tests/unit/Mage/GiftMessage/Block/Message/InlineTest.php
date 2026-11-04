<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\GiftMessage\Block\Message;

use Generator;
use Mage_GiftMessage_Block_Message_Inline as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\GiftMessage\Block\Message\InlineTrait;

final class InlineTest extends OpenMageTest
{
    use InlineTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * GHSA-xprp-q2jp-9fcx: the default (a customer or address name) is escaped like a stored value
     *
     * @dataProvider provideGetEscaped
     * @group Block
     */
    public function testGetEscaped(string $expected, ?string $value, string $default): void
    {
        self::assertSame($expected, self::$subject->getEscaped($value, $default));
    }

    public static function provideGetEscaped(): Generator
    {
        yield 'stored value' => ['a &quot;b&quot; &lt;c&gt;', ' a "b" <c> ', 'ignored'];
        yield 'null falls back to escaped default' => ['&quot;&gt;&lt;x&gt;', null, '"><x>'];
        yield 'empty falls back to escaped default' => ['&quot;&gt;&lt;x&gt;', '', '"><x>'];
        yield 'empty without default' => ['', '', ''];
    }
}
