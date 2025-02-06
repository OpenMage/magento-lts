<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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
