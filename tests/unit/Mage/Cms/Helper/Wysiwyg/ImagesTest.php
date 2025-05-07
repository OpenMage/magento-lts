<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Helper\Wysiwyg;

use Mage;
use Mage_Cms_Helper_Wysiwyg_Images as Subject;
use Mage_Cms_Model_Wysiwyg_Images_Storage;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\CmsTrait;
use OpenMage\Tests\Unit\OpenMageTest;

class ImagesTest extends OpenMageTest
{
    use CmsTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('cms/wysiwyg_images');
    }

    /**
     * @group Helper
     */
    public function testGetCurrentPath(): void
    {
        static::assertIsString(self::$subject->getCurrentPath());
    }

    /**
     * @group Helper
     */
    public function testGetCurrentUrl(): void
    {
        static::assertIsString(self::$subject->getCurrentUrl());
    }

    /**
     * @group Helper
     */
    public function testGetStorage(): void
    {
        static::assertInstanceOf(Mage_Cms_Model_Wysiwyg_Images_Storage::class, self::$subject->getStorage());
    }

    /**
     * @group Helper
     */
    public function testIdEncode(): void
    {
        static::assertIsString(self::$subject->idEncode($this->getTestString()));
    }

    /**
     * @group Helper
     */
    public function testIdDecode(): void
    {
        static::assertIsString(self::$subject->idDecode($this->getTestString()));
    }

    /**
     * @dataProvider provideGetShortFilename
     * @group Helper
     */
    public function testGetShortFilename(string $expectedResult, string $filename, int $maxLength): void
    {
        static::assertSame($expectedResult, self::$subject->getShortFilename($filename, $maxLength));
    }
}
