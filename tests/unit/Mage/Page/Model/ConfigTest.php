<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Page
 * @group Mage_Page_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Model;

use Mage;
use Mage_Page_Model_Config as Subject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('page/config');
    }

    
    public function testGetPageLayoutHandles(): void
    {
        $this->assertIsArray($this->subject->getPageLayoutHandles());
    }
}
