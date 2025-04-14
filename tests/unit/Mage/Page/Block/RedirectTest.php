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
use Mage_Page_Block_Redirect as Subject;
use PHPUnit\Framework\TestCase;

class RedirectTest extends TestCase
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Subject();
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetTargetUrl(): void
    {
        static::assertSame('', self::$subject->getTargetURL());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetMessage(): void
    {
        static::assertSame('', self::$subject->getMessage());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetRedirectOutput(): void
    {
        static::assertIsString(self::$subject->getRedirectOutput());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetJsRedirect(): void
    {
        static::assertIsString(self::$subject->getJsRedirect());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetHtmlFormRedirect(): void
    {
        static::assertIsString(self::$subject->getHtmlFormRedirect());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testIsHtmlFormRedirect(): void
    {
        static::assertIsBool(self::$subject->isHtmlFormRedirect());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetFormId(): void
    {
        static::assertSame('', self::$subject->getFormId());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetFormMethod(): void
    {
        static::assertSame('POST', self::$subject->getFormMethod());
    }
}
