<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Adminhtml
 * @group Mage_Adminhtml_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper\Media;

use Mage;
use Mage_Adminhtml_Helper_Media_Js as Subject;
use PHPUnit\Framework\TestCase;

class JsTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('adminhtml/media_js');
    }


    public function testDecodeGridSerializedInput(): void
    {
        $this->assertIsString($this->subject->getTranslatorScript());
    }
}
