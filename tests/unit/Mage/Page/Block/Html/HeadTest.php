<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Block\Html;

use Mage;
use Mage_Page_Block_Html_Head as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class HeadTest extends OpenMageTest
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
    public function testAddCss(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->addCss('test'));
    }

    /**
     * @group Block
     */
    public function testAddJs(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->addJs('test'));
    }

    /**
     * @group Block
     */
    public function testAddCssIe(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->addCssIe('test'));
    }

    /**
     * @group Block
     */
    public function testAddJsIe(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->addJsIe('test'));
    }

    /**
     * @group Block
     */
    public function testAddLinkRel(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->addLinkRel('test', 'ref'));
    }
}
