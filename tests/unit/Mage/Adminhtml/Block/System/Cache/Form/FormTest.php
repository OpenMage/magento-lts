<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Adminhtml
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Cache\Form;

use Mage;
use Mage_Adminhtml_Block_System_Cache_Form as Subject;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }


    public function testInitForm(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->initForm());
    }
}
