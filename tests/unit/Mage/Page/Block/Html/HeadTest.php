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
use Mage_Page_Block_Html_Head;
use PHPUnit\Framework\TestCase;

class HeadTest extends TestCase
{
    /**
     * @var Mage_Page_Block_Html_Head
     */
    public Mage_Page_Block_Html_Head $subject;

    public function setUp(): void
    {
        Mage::app();
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Page_Block_Html_Head();
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Model
     */
    public function testAddCss(): void
    {
        $this->assertInstanceOf(Mage_Page_Block_Html_Head::class, $this->subject->addCss('test'));
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Model
     */
    public function testAddJs(): void
    {
        $this->assertInstanceOf(Mage_Page_Block_Html_Head::class, $this->subject->addJs('test'));
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Model
     */
    public function testAddCssIe(): void
    {
        $this->assertInstanceOf(Mage_Page_Block_Html_Head::class, $this->subject->addCssIe('test'));
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Model
     */
    public function testAddJsIe(): void
    {
        $this->assertInstanceOf(Mage_Page_Block_Html_Head::class, $this->subject->addJsIe('test'));
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Model
     */
    public function testAddLinkRel(): void
    {
        $this->assertInstanceOf(Mage_Page_Block_Html_Head::class, $this->subject->addLinkRel('test', 'ref'));
    }
}
