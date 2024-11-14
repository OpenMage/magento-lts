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
use Mage_Page_Block_Switch;
use PHPUnit\Framework\TestCase;

class SwitchTest extends TestCase
{
    public Mage_Page_Block_Switch $subject;

    public function setUp(): void
    {
        Mage::app();
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Page_Block_Switch();
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetCurrentWebsiteId(): void
    {
        $this->assertIsInt($this->subject->getCurrentWebsiteId());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetCurrentGroupId(): void
    {
        $this->assertIsInt($this->subject->getCurrentGroupId());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetCurrentStoreId(): void
    {
        $this->assertIsInt($this->subject->getCurrentStoreId());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetCurrentStoreCode(): void
    {
        $this->assertIsString($this->subject->getCurrentStoreCode());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetRawGroups(): void
    {
        $this->assertIsArray($this->subject->getRawGroups());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
//    public function testGetRawStores(): void
//    {
//        $this->assertIsArray($this->subject->getRawStores());
//    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
//    public function testGetGroups(): void
//    {
//        $this->assertIsArray($this->subject->getGroups());
//    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
//    public function testGetStores(): void
//    {
//        $this->assertIsArray($this->subject->getStores());
//    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testIsStoreInUrl(): void
    {
        $this->assertIsBool($this->subject->isStoreInUrl());
    }
}
