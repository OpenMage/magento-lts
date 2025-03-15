<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Page
 * @group Mage_Page_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Model\Source;

use Mage;
use Mage_Page_Model_Source_Layout as Subject;
use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('page/source_layout');
    }


    public function testToOptionArray(): void
    {
        $this->assertIsArray($this->subject->toOptionArray(true));
    }
}
