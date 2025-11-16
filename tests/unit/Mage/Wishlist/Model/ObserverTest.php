<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Wishlist\Model;

use Mage;
use Mage_Wishlist_Model_Observer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Event;
use Varien_Event_Observer;
use Varien_Object;

final class ObserverTest extends OpenMageTest
{
    private static Subject $subject;

    protected function setUp(): void
    {
        Mage::app();
        self::$subject = Mage::getModel('wishlist/observer');
    }

    /**
     * @covers Mage_Wishlist_Model_Observer::processCartUpdateBefore()
     * @group Model
     */
    public function testProcessCartUpdateBefore(): void
    {
        $observer = new Varien_Event_Observer();

        $event = new Varien_Event();

        $event->setCart(new Varien_Event(['quote' => new Varien_Object()]));
        $event->setInfo(new Varien_Event());

        $observer->setEvent($event);

        self::assertInstanceOf(self::$subject::class, self::$subject->processCartUpdateBefore($observer));
    }

    /**
     * @covers Mage_Wishlist_Model_Observer::processAddToCart()
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testProcessAddToCart(): void
    {
        #$observer = new Varien_Event_Observer();
        #self::$subject->processAddToCart($observer);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers Mage_Wishlist_Model_Observer::customerLogin()
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testCustomerLogin(): void
    {
        $observer = new Varien_Event_Observer();

        self::assertInstanceOf(self::$subject::class, self::$subject->customerLogin($observer));
    }

    /**
     * @covers Mage_Wishlist_Model_Observer::customerLogout()
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testCustomerLogout(): void
    {
        $observer = new Varien_Event_Observer();

        self::assertInstanceOf(self::$subject::class, self::$subject->customerLogout($observer));
    }
}
