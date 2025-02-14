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
