<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Uploader
 * @group Mage_Uploader_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Helper;

use Mage;
use Mage_Uploader_Helper_Data as Subject;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('uploader/data');
    }


    public function testIsModuleEnabled(): void
    {
        $this->assertIsBool($this->subject->isModuleEnabled());
    }
}
