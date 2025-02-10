<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Page
 * @group Mage_Page_Block
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Block;

use Mage;
use Mage_Page_Block_Html as Subject;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }


    public function testGetBaseUrl(): void
    {
        $this->assertIsString($this->subject->getBaseUrl());
    }


    public function testGetBaseSecureUrl(): void
    {
        $this->assertIsString($this->subject->getBaseSecureUrl());
    }


    //    public function testGetCurrentUrl(): void
    //    {
    //        $this->assertIsString($this->subject->getCurrentUrl());
    //    }


    public function testGetPrintLogoUrl(): void
    {
        $this->assertIsString($this->subject->getPrintLogoUrl());
    }
}
