<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage_Core_Helper_Abstract as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\AbstractTrait;

final class AbstractTest extends OpenMageTest
{
    use AbstractTrait;

    private static Subject $subject;

    protected function setUp(): void
    {
        self::$subject = $this->getMockBuilder(Subject::class)->getMock();
    }

    /**
     * @dataProvider provideEscapeHtmlData
     * @group Helper
     */
    public function testEscapeHtml($expectedResult, $data, ?array $allowedTags): void
    {
        self::assertSame($expectedResult, self::$subject->escapeHtml($data, $allowedTags));
    }

    /**
     * @dataProvider provideStripTagsData
     * @group Helper
     */
    public function testStripTags($expectedResult, $data, null|array|string $allowedTags, bool $escape): void
    {
        self::assertSame($expectedResult, self::$subject->stripTags($data, $allowedTags, $escape));
    }
}
