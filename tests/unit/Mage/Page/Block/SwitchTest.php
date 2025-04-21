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
use PHPUnit\Framework\TestCase;

class SwitchTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
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
