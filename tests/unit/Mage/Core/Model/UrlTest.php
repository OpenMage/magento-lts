<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Core
 * @group Mage_Core_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Url as Subject;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('core/url');
    }

    
    public function testEscape(): void
    {
        $this->assertSame('%22%27%3E%3C', $this->subject->escape('"\'><'));
    }

    
    public function testGetSecure(): void
    {
        $this->assertIsBool($this->subject->getSecure());
    }
}
