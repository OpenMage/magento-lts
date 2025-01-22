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

namespace OpenMage\Tests\Unit\Mage\Cms\Block\Widget\Page;

use Mage;
use Mage_Cms_Block_Widget_Page_Link as Subject;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Block
     */
    public function testGetHref(): void
    {
        $this->assertIsString($this->subject->getHref());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Block
     */
    public function testGetTitle(): void
    {
        $this->assertIsString($this->subject->getTitle());
    }

    /**
     * @group Mage_Cms
     * @group Mage_Cms_Block
     */
    //    public function testGetAnchorText(): void
    //    {
    //        $this->assertIsString($this->subject->getAnchorText());
    //    }
}
