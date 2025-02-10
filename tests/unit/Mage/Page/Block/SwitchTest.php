<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Page
 * @group Mage_Page_Block
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

    
    public function testGetCurrentWebsiteId(): void
    {
        $this->assertIsInt($this->subject->getCurrentWebsiteId());
    }

    
    public function testGetCurrentGroupId(): void
    {
        $this->assertIsInt($this->subject->getCurrentGroupId());
    }

    
    public function testGetCurrentStoreId(): void
    {
        $this->assertIsInt($this->subject->getCurrentStoreId());
    }

    
    public function testGetCurrentStoreCode(): void
    {
        $this->assertIsString($this->subject->getCurrentStoreCode());
    }

    
    public function testGetRawGroups(): void
    {
        $this->assertIsArray($this->subject->getRawGroups());
    }

    
    //    public function testGetRawStores(): void
    //    {
    //        $this->assertIsArray($this->subject->getRawStores());
    //    }

    
    //    public function testGetGroups(): void
    //    {
    //        $this->assertIsArray($this->subject->getGroups());
    //    }

    
    //    public function testGetStores(): void
    //    {
    //        $this->assertIsArray($this->subject->getStores());
    //    }

    
    public function testIsStoreInUrl(): void
    {
        $this->assertIsBool($this->subject->isStoreInUrl());
    }
}
