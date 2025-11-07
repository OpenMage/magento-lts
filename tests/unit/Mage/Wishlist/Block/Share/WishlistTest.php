<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Wishlist\Block\Share;

use Mage_Customer_Model_Customer;
use Mage_Wishlist_Block_Share_Wishlist as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class WishlistTest extends OpenMageTest
{
    private static Subject $subject;

    protected function setUp(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @group Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetWishlistCustomer(): void
    {
        self::assertInstanceOf(Mage_Customer_Model_Customer::class, self::$subject->getWishlistCustomer());
    }

    /**
     * @group Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetHeader(): void
    {
        self::assertIsString(self::$subject->getHeader());
    }
}
