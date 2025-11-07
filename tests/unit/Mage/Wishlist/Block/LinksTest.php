<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Wishlist\Block;

use Mage_Wishlist_Block_Links as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class LinksTest extends OpenMageTest
{
    private static Subject $subject;

    protected function setUp(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @covers Mage_Wishlist_Block_Links::initLinkProperties()
     * @group Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testInitLinkProperties(): void
    {
        self::$subject->initLinkProperties();
        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers Mage_Wishlist_Block_Links::addWishlistLink()
     * @group Block
     */
    public function testAddWishlistLink(): void
    {
        self::assertInstanceOf(self::$subject::class, self::$subject->addWishlistLink());
    }

    /**
     * @group Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetCacheTags(): void
    {
        self::assertEquals([0 => 'block_html'], self::$subject->getCacheTags());
    }
}
