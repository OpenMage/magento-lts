<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Cms
 * @group Mage_Cms_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Helper\Wysiwyg;

use Mage;
use Mage_Cms_Helper_Wysiwyg_Images as Subject;
use Mage_Cms_Model_Wysiwyg_Images_Storage;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\CmsTrait;
use PHPUnit\Framework\TestCase;

class ImagesTest extends TestCase
{
    use CmsTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('cms/wysiwyg_images');
    }

    
    public function testGetCurrentPath(): void
    {
        $this->assertIsString($this->subject->getCurrentPath());
    }

    
    public function testGetCurrentUrl(): void
    {
        $this->assertIsString($this->subject->getCurrentUrl());
    }

    
    public function testGetStorage(): void
    {
        $this->assertInstanceOf(Mage_Cms_Model_Wysiwyg_Images_Storage::class, $this->subject->getStorage());
    }

    
    public function testIdEncode(): void
    {
        $this->assertIsString($this->subject->idEncode($this->getTestString()));
    }

    
    public function testIdDecode(): void
    {
        $this->assertIsString($this->subject->idDecode($this->getTestString()));
    }

    /**
     * @dataProvider provideGetShortFilename
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testGetShortFilename(string $expectedResult, string $filename, int $maxLength): void
    {
        $this->assertSame($expectedResult, $this->subject->getShortFilename($filename, $maxLength));
    }
}
