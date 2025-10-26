<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Block\Widget\Page;

use Mage_Cms_Block_Widget_Page_Link as Subject;
use Mage_Core_Model_Store_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\Block\Widget\Page\LinkTrait;

final class LinkTest extends OpenMageTest
{
    use LinkTrait;

    private static Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        self::$subject = new Subject();
    }

    /**
     * @dataProvider provideGetHrefData
     * @group Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetHref(string $expectedResult, array $data): void
    {
        self::$subject->setData($data);

        $result = self::$subject->getHref();
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider provideGetTitleData
     * @group Block
     * @throws Mage_Core_Model_Store_Exception
     */
    public function testGetTitle(string $expectedResult, array $data): void
    {
        self::$subject->setData($data);

        $result = self::$subject->getTitle();
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider provideGetAnchorTextData
     * @group Block
     * @throws Mage_Core_Model_Store_Exception
     */
    public function testGetAnchorText(bool|string|null $expectedResult, array $data): void
    {
        self::$subject->setData($data);

        $result = self::$subject->getAnchorText();
        self::assertSame($expectedResult, $result);
    }
}
