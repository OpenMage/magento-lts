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
use Mage_Page_Block_Switch as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class SwitchTest extends OpenMageTest
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
    public function testGetCurrentWebsiteId(): void
    {
        static::assertIsInt(self::$subject->getCurrentWebsiteId());
    }

    /**
     * @group Block
     */
    public function testGetCurrentGroupId(): void
    {
        static::assertIsInt(self::$subject->getCurrentGroupId());
    }

    /**
     * @group Block
     */
    public function testGetCurrentStoreId(): void
    {
        static::assertIsInt(self::$subject->getCurrentStoreId());
    }

    /**
     * @group Block
     */
    public function testGetCurrentStoreCode(): void
    {
        static::assertIsString(self::$subject->getCurrentStoreCode());
    }

    /**
     * @group Block
     */
    public function testGetRawGroups(): void
    {
        static::assertIsArray(self::$subject->getRawGroups());
    }

    /**
     * @group Block
     */
    //    public function testGetRawStores(): void
    //    {
    //        $this->assertIsArray(self::$subject->getRawStores());
    //    }

    /**
     * @group Block
     */
    //    public function testGetGroups(): void
    //    {
    //        $this->assertIsArray(self::$subject->getGroups());
    //    }

    /**
     * @group Block
     */
    //    public function testGetStores(): void
    //    {
    //        $this->assertIsArray(self::$subject->getStores());
    //    }

    /**
     * @group Block
     */
    public function testIsStoreInUrl(): void
    {
        static::assertIsBool(self::$subject->isStoreInUrl());
    }
}
