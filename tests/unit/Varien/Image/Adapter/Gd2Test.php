<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Image\Adapter;

use Override;
use PHPUnit\Framework\TestCase;
use Varien_Image_Adapter_Gd2 as Subject;
use WeakReference;

final class Gd2Test extends TestCase
{
    private static string $fileName;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        self::$fileName = (string) tempnam(sys_get_temp_dir(), 'gd2test');
        $image = imagecreatetruecolor(1500, 1300);
        imagepng($image, self::$fileName);
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        unlink(self::$fileName);
    }

    /**
     * @group Varien_Image
     */
    public function testSubclassCanCallParentConstructor(): void
    {
        $subject = new class extends Subject {
            public bool $initialized = false;

            public function __construct()
            {
                parent::__construct();
                $this->initialized = true;
            }
        };

        self::assertTrue($subject->initialized);
    }

    /**
     * @group Varien_Image
     */
    public function testAdapterIsReleasedAfterUnset(): void
    {
        $subject = new Subject();
        $subject->open(self::$fileName);

        $reference = WeakReference::create($subject);

        unset($subject);

        self::assertNull($reference->get());
    }

    /**
     * @group Varien_Image
     */
    public function testMemoryIsFreedAfterOpeningImages(): void
    {
        $memoryBefore = memory_get_usage();

        for ($i = 0; $i < 5; $i++) {
            $subject = new Subject();
            $subject->open(self::$fileName);
            unset($subject);
        }

        // a single decoded 1500x1300 truecolor image takes about 7.5 MB
        self::assertLessThan(1024 * 1024, memory_get_usage() - $memoryBefore);
    }
}
