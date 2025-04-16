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

use Mage;
use Mage_Cms_Block_Widget_Page_Link as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class LinkTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @group Block
     */
    public function testGetHref(): void
    {
        static::assertIsString(self::$subject->getHref());
    }

    /**
     * @group Block
     */
    public function testGetTitle(): void
    {
        static::assertIsString(self::$subject->getTitle());
    }

    /**
     * @group Block
     */
    //    public function testGetAnchorText(): void
    //    {
    //        $this->assertIsString(self::$subject->getAnchorText());
    //    }
}
