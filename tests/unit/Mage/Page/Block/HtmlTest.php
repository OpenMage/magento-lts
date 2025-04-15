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

namespace OpenMage\Tests\Unit\Mage\Page\Block;

use Mage;
use Mage_Page_Block_Html as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class HtmlTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetBaseUrl(): void
    {
        static::assertIsString(self::$subject->getBaseUrl());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetBaseSecureUrl(): void
    {
        static::assertIsString(self::$subject->getBaseSecureUrl());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    //    public function testGetCurrentUrl(): void
    //    {
    //        $this->assertIsString(self::$subject->getCurrentUrl());
    //    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetPrintLogoUrl(): void
    {
        static::assertIsString(self::$subject->getPrintLogoUrl());
    }
}
