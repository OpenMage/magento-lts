<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Block;

use Mage_Uploader_Block_Abstract as Subject;
use Mage_Uploader_Model_Config_Browsebutton;
use Mage_Uploader_Model_Config_Misc;
use Mage_Uploader_Model_Config_Uploader;
use OpenMage\Tests\Unit\OpenMageTest;

final class AbstractTest extends OpenMageTest
{
    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = $this->getMockForAbstractClass(Subject::class);
    }

    /**
     * @group Block
     */
    public function testGetMiscConfig(): void
    {
        self::assertInstanceOf(Mage_Uploader_Model_Config_Misc::class, self::$subject->getMiscConfig());
    }

    /**
     * @group Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetUploaderConfig(): void
    {
        self::assertInstanceOf(Mage_Uploader_Model_Config_Uploader::class, self::$subject->getUploaderConfig());
    }

    /**
     * @group Block
     */
    public function testGetButtonConfig(): void
    {
        self::assertInstanceOf(Mage_Uploader_Model_Config_Browsebutton::class, self::$subject->getButtonConfig());
    }

    /**
     * @group Block
     */
    public function testGetElementId(): void
    {
        $suffix = 'test';
        $result = self::$subject->getElementId($suffix);
        self::assertStringStartsWith('id_', $result);
        self::assertStringEndsWith('-' . $suffix, $result);
    }
}
