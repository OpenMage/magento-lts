<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
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

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetBaseUrl(): void
    {
        $this->assertIsString($this->subject->getBaseUrl());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetBaseSecureUrl(): void
    {
        $this->assertIsString($this->subject->getBaseSecureUrl());
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    //    public function testGetCurrentUrl(): void
    //    {
    //        $this->assertIsString($this->subject->getCurrentUrl());
    //    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Block
     */
    public function testGetPrintLogoUrl(): void
    {
        $this->assertIsString($this->subject->getPrintLogoUrl());
    }
}
