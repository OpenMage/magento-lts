<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Cms
 * @group Mage_Cms_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Helper;

use Mage;
use Mage_Cms_Helper_Data as Subject;
use PHPUnit\Framework\TestCase;
use Varien_Filter_Template;

class DataTest extends TestCase
{
    public const TEST_STRING = '1234567890';

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('cms/data');
    }


    public function testGetAllowedStreamWrappers(): void
    {
        $this->assertIsArray($this->subject->getAllowedStreamWrappers());
    }


    public function testGetBlockTemplateProcessor(): void
    {
        $this->assertInstanceOf(Varien_Filter_Template::class, $this->subject->getBlockTemplateProcessor());
    }


    public function testGetPageTemplateProcessor(): void
    {
        $this->assertInstanceOf(Varien_Filter_Template::class, $this->subject->getPageTemplateProcessor());
    }


    public function testIsSwfDisabled(): void
    {
        $this->assertTrue($this->subject->isSwfDisabled());
    }
}
