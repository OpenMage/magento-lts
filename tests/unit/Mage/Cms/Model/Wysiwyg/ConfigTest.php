<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Model\Wysiwyg;

use Mage;
use Mage_Cms_Model_Wysiwyg_Config as Subject;
use PHPUnit\Framework\TestCase;
use Varien_Object;

class ConfigTest extends TestCase
{
    public const TEST_STRING = '0123456789';

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('cms/wysiwyg_config');
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetConfig(): void
    {
        $this->assertInstanceOf(Varien_Object::class, $this->subject->getConfig());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetSkinImagePlaceholderUrl(): void
    {
        $this->assertIsString($this->subject->getSkinImagePlaceholderUrl());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testGetSkinImagePlaceholderPath(): void
    {
        $this->assertIsString($this->subject->getSkinImagePlaceholderPath());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testIsEnabled(): void
    {
        $this->assertIsBool($this->subject->isEnabled());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Model
     */
    public function testIsHidden(): void
    {
        $this->assertIsBool($this->subject->isHidden());
    }
}
