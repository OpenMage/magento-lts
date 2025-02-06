<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Block\Html;

use Mage;
use Mage_Page_Block_Html_Head as Subject;
use PHPUnit\Framework\TestCase;

class HeadTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testAddCss(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addCss('test'));
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testAddJs(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addJs('test'));
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testAddCssIe(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addCssIe('test'));
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testAddJsIe(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addJsIe('test'));
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testAddLinkRel(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addLinkRel('test', 'ref'));
    }
}
