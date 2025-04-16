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

namespace OpenMage\Tests\Unit\Mage\Cms\Block\Widget\Page;

use Mage_Cms_Block_Widget_Page_Link as Subject;
use Mage_Core_Model_Store_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\Block\Widget\Page\LinkTrait;

class LinkTest extends OpenMageTest
{
    use LinkTrait;

    private static Subject $subject;

    protected function setUp(): void
    {
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
        static::assertSame($expectedResult, $result);
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
        static::assertSame($expectedResult, $result);
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
        static::assertSame($expectedResult, $result);
    }
}
