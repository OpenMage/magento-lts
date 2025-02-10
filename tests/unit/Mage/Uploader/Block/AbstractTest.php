<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Uploader
 * @group Mage_Uploader_Block
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Block;

use Mage;
use Mage_Uploader_Block_Abstract as Subject;
use Mage_Uploader_Model_Config_Browsebutton;
use Mage_Uploader_Model_Config_Misc;
use Mage_Uploader_Model_Config_Uploader;
use PHPUnit\Framework\TestCase;

class AbstractTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = $this->getMockForAbstractClass(Subject::class);
    }


    public function testGetMiscConfig(): void
    {
        $this->assertInstanceOf(Mage_Uploader_Model_Config_Misc::class, $this->subject->getMiscConfig());
    }

    /**
     * @group Mage_Uploader
     * @group Mage_Uploader_Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetUploaderConfig(): void
    {
        $this->assertInstanceOf(Mage_Uploader_Model_Config_Uploader::class, $this->subject->getUploaderConfig());
    }


    public function testGetButtonConfig(): void
    {
        $this->assertInstanceOf(Mage_Uploader_Model_Config_Browsebutton::class, $this->subject->getButtonConfig());
    }


    public function testGetElementId(): void
    {
        $suffix = 'test';
        $result = $this->subject->getElementId($suffix);
        $this->assertStringStartsWith('id_', $result);
        $this->assertStringEndsWith('-' . $suffix, $result);
    }
}
