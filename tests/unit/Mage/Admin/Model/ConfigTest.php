<?php

/**
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Mage;
use Mage_Admin_Model_Acl;
use Mage_Admin_Model_Config;
use PHPUnit\Framework\TestCase;
use Varien_Simplexml_Config;

class ConfigTest extends TestCase
{
    public Mage_Admin_Model_Config $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('admin/config');
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testGetAclAssert(): void
    {
        $this->assertFalse($this->subject->getAclAssert(''));
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testGetAclPrivilegeSet(): void
    {
        $this->assertFalse($this->subject->getAclPrivilegeSet());
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testLoadAclResources(): void
    {
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->assertInstanceOf(Mage_Admin_Model_Config::class, $this->subject->loadAclResources(new Mage_Admin_Model_Acl()));
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testGetAdminhtmlConfig(): void
    {
        $this->assertInstanceOf(Varien_Simplexml_Config::class, $this->subject->getAdminhtmlConfig());
    }
}
