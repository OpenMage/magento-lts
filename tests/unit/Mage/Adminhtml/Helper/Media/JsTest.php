<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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

    /**
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testDecodeGridSerializedInput(): void
    {
        $this->assertIsString($this->subject->getTranslatorScript());
    }
}
