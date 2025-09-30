<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Block;

use Mage;
use Mage_Page_Block_Html as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class HtmlTest extends OpenMageTest
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
    public function testGetBaseUrl(): void
    {
        self::assertIsString(self::$subject->getBaseUrl());
    }

    /**
     * @group Block
     */
    public function testGetBaseSecureUrl(): void
    {
        self::assertIsString(self::$subject->getBaseSecureUrl());
    }

    /**
     * @group Block
     */
    //    public function testGetCurrentUrl(): void
    //    {
    //        $this->assertIsString(self::$subject->getCurrentUrl());
    //    }

    /**
     * @group Block
     */
    public function testGetPrintLogoUrl(): void
    {
        self::assertIsString(self::$subject->getPrintLogoUrl());
    }
}
