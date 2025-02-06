<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testGetAllowedStreamWrappers(): void
    {
        $this->assertIsArray($this->subject->getAllowedStreamWrappers());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testGetBlockTemplateProcessor(): void
    {
        $this->assertInstanceOf(Varien_Filter_Template::class, $this->subject->getBlockTemplateProcessor());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testGetPageTemplateProcessor(): void
    {
        $this->assertInstanceOf(Varien_Filter_Template::class, $this->subject->getPageTemplateProcessor());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testIsSwfDisabled(): void
    {
        $this->assertTrue($this->subject->isSwfDisabled());
    }
}
