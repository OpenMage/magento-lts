<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Block;

use Mage_Page_Block_Redirect as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class RedirectTest extends OpenMageTest
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
    public function testGetTargetUrl(): void
    {
        self::assertSame('', self::$subject->getTargetURL());
    }

    /**
     * @group Block
     */
    public function testGetMessage(): void
    {
        self::assertSame('', self::$subject->getMessage());
    }

    /**
     * @group Block
     */
    public function testGetRedirectOutput(): void
    {
        self::assertIsString(self::$subject->getRedirectOutput());
    }

    /**
     * @group Block
     */
    public function testGetJsRedirect(): void
    {
        self::assertIsString(self::$subject->getJsRedirect());
    }

    /**
     * @group Block
     */
    public function testGetHtmlFormRedirect(): void
    {
        self::assertIsString(self::$subject->getHtmlFormRedirect());
    }

    /**
     * @group Block
     */
    public function testIsHtmlFormRedirect(): void
    {
        self::assertIsBool(self::$subject->isHtmlFormRedirect());
    }

    /**
     * @group Block
     */
    public function testGetFormId(): void
    {
        self::assertSame('', self::$subject->getFormId());
    }

    /**
     * @group Block
     */
    public function testGetFormMethod(): void
    {
        self::assertSame('POST', self::$subject->getFormMethod());
    }
}
