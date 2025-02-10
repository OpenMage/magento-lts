<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Core
 * @group Mage_Core_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Resource_Store_Collection;
use Mage_Core_Model_Resource_Store_Group_Collection;
use Mage_Core_Model_Website as Subject;
use Mage_Directory_Model_Currency;
use PHPUnit\Framework\TestCase;
use Varien_Db_Select;

class WebsiteTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('core/website');
    }

    
    public function testLoad(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->load(1));
        $this->assertInstanceOf(Subject::class, $this->subject->load('default'));
    }

    
    public function testLoadConfig(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->loadConfig('1'));
        $this->assertInstanceOf(Subject::class, $this->subject->loadConfig('default'));
    }

    
    public function testGetStoreCollection(): void
    {
        $this->assertInstanceOf(Mage_Core_Model_Resource_Store_Collection::class, $this->subject->getStoreCollection());
    }

    
    public function testGetGroupCollection(): void
    {
        $this->assertInstanceOf(Mage_Core_Model_Resource_Store_Group_Collection::class, $this->subject->getGroupCollection());
    }

    
    public function testGetStores(): void
    {
        $this->assertIsArray($this->subject->getStores());
    }

    
    public function testGetStoreIds(): void
    {
        $this->assertIsArray($this->subject->getStoreIds());
    }

    
    public function testGetStoreCodes(): void
    {
        $this->assertIsArray($this->subject->getStoreCodes());
    }

    
    public function testGetStoresCount(): void
    {
        $this->assertIsInt($this->subject->getStoresCount());
    }

    
    public function testGetGroups(): void
    {
        $this->assertIsArray($this->subject->getGroups());
    }

    
    public function testGetGroupIds(): void
    {
        $this->assertIsArray($this->subject->getGroupIds());
    }

    
    public function testGetGroupsCount(): void
    {
        $this->assertIsInt($this->subject->getGroupsCount());
    }

    
    public function testGetBaseCurrency(): void
    {
        $this->assertIsObject($this->subject->getBaseCurrency());
        $this->assertInstanceOf(Mage_Directory_Model_Currency::class, $this->subject->getBaseCurrency());
    }

    //    /**
    //     * @group Mage_Core
    //     * @group Mage_Core_Model
    //     */
    //    public function testGetDefaultStore(): void
    //    {
    //        $this->assertIsObject($this->subject->getDefaultStore());
    //        $this->assertInstanceOf(Mage_Core_Model_Store::class, $this->subject->getDefaultStore());
    //    }

    /**
     * @group Mage_Core
     */
    public function testGetDefaultStoresSelect(): void
    {
        $this->assertIsObject($this->subject->getDefaultStoresSelect());
        $this->assertInstanceOf(Varien_Db_Select::class, $this->subject->getDefaultStoresSelect(true));
    }

    
    public function testIsReadOnly(): void
    {
        $this->assertFalse($this->subject->isReadOnly());
        $this->assertTrue($this->subject->isReadOnly(true));
    }
}
