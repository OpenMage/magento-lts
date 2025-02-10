<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Admin
 * @group Mage_Admin_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Mage;
use Mage_Admin_Model_Acl;
use Mage_Admin_Model_Config as Subject;
use PHPUnit\Framework\TestCase;
use Varien_Simplexml_Config;

class ConfigTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('admin/config');
    }


    public function testGetAclAssert(): void
    {
        $this->assertFalse($this->subject->getAclAssert(''));
    }


    public function testGetAclPrivilegeSet(): void
    {
        $this->assertFalse($this->subject->getAclPrivilegeSet());
    }


    public function testLoadAclResources(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->loadAclResources(new Mage_Admin_Model_Acl()));
    }


    public function testGetAdminhtmlConfig(): void
    {
        $this->assertInstanceOf(Varien_Simplexml_Config::class, $this->subject->getAdminhtmlConfig());
    }
}
