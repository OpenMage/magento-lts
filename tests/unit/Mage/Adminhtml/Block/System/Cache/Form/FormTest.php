<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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

    /**
     * @group Mage_Adminhtml
     */
    public function testInitForm(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->initForm());
    }
}
