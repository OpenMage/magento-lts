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
use Mage_Page_Block_Switch as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class SwitchTest extends OpenMageTest
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
