<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage_Core_Exception;
use Mage_Core_Model_Resource_Store_Collection;
use Mage_Core_Model_Resource_Store_Group_Collection;
use Mage_Core_Model_Store;
use Mage_Core_Model_Website;
use Mage_Directory_Model_Currency;
use PHPUnit\Framework\TestCase;
use Varien_Db_Select;

class WebsiteTest extends TestCase
{
    /**
     * @var Mage_Core_Model_Website
     */
    public Mage_Core_Model_Website $subject;

    public function setUp(): void
    {
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Core_Model_Website();
    }

    /**
     * @return void
     */
    public function testLoad(): void
    {
        $this->assertInstanceOf(Mage_Core_Model_Website::class, $this->subject->load(1));
        $this->assertInstanceOf(Mage_Core_Model_Website::class, $this->subject->load('default'));
    }

    /**
     * @return void
     */
    public function testLoadConfig(): void
    {
        $this->assertInstanceOf(Mage_Core_Model_Website::class, $this->subject->loadConfig(1));
        $this->assertInstanceOf(Mage_Core_Model_Website::class, $this->subject->loadConfig('default'));
    }

    /**
     * @return void
     * @throws Mage_Core_Exception
     */
    public function testGetStoreCollection(): void
    {
        $this->assertInstanceOf(Mage_Core_Model_Resource_Store_Collection::class, $this->subject->getStoreCollection());
    }

    /**
     * @return void
     * @throws Mage_Core_Exception
     */
    public function testGetGroupCollection(): void
    {
        $this->assertInstanceOf(Mage_Core_Model_Resource_Store_Group_Collection::class, $this->subject->getGroupCollection());
    }

    /**
     * @return void
     * @throws Mage_Core_Exception
     */
    public function testGetStores(): void
    {
        $this->assertIsArray($this->subject->getStores());
    }

    /**
     * @return void
     * @throws Mage_Core_Exception
     */
    public function testGetStoreIds(): void
    {
        $this->assertIsArray($this->subject->getStoreIds());
    }

    /**
     * @return void
     * @throws Mage_Core_Exception
     */
    public function testGetStoreCodes(): void
    {
        $this->assertIsArray($this->subject->getStoreCodes());
    }

    /**
     * @return void
     * @throws Mage_Core_Exception
     */
    public function testGetStoresCount(): void
    {
        $this->assertIsInt($this->subject->getStoresCount());
    }

    /**
     * @return void
     */
    public function testGetGroups(): void
    {
        $this->assertIsArray($this->subject->getGroups());
    }

    /**
     * @return void
     */
    public function testGetGroupIds(): void
    {
        $this->assertIsArray($this->subject->getGroupIds());
    }

    /**
     * @return void
     */
    public function testGetGroupsCount(): void
    {
        $this->assertIsInt($this->subject->getGroupsCount());
    }

    /**
     * @return void
     */
    public function testGetBaseCurrency(): void
    {
        $this->assertIsObject($this->subject->getBaseCurrency());
        $this->assertInstanceOf(Mage_Directory_Model_Currency::class, $this->subject->getBaseCurrency());
    }

//    /**
//     * @return void
//     */
//    public function testGetDefaultStore(): void
//    {
//        $this->assertIsObject($this->subject->getDefaultStore());
//        $this->assertInstanceOf(Mage_Core_Model_Store::class, $this->subject->getDefaultStore());
//    }

    /**
     * @return void
     */
    public function testGetDefaultStoresSelect(): void
    {
        $this->assertIsObject($this->subject->getDefaultStoresSelect());
        $this->assertInstanceOf(Varien_Db_Select::class, $this->subject->getDefaultStoresSelect('true'));
    }

    /**
     * @return void
     */
    public function testIsReadOnly(): void
    {
        $this->assertFalse($this->subject->isReadOnly());
        $this->assertTrue($this->subject->isReadOnly('true'));
    }
}
