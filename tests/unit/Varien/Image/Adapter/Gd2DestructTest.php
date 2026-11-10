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
use ReflectionProperty;
use Varien_Image_Adapter_Gd2 as Subject;
use WeakReference;

final class Gd2DestructTest extends TestCase
{
    private static string $fileName;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        self::$fileName = (string) tempnam(sys_get_temp_dir(), 'gd2-destruct-');
        imagepng(imagecreatetruecolor(64, 64), self::$fileName);
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        unlink(self::$fileName);
    }

    public function testDestructReleasesBitmapWhileAdapterRemainsReferenced(): void
    {
        $subject = new Subject();
        $subject->open(self::$fileName);

        $handler = new ReflectionProperty(Subject::class, '_imageHandler');
        $reference = WeakReference::create($handler->getValue($subject));
        self::assertNotNull($reference->get());

        $subject->destruct();

        self::assertNull($handler->getValue($subject));
        self::assertNull($reference->get());
        $subject->destruct();
        self::assertNull($handler->getValue($subject));
    }

    public function testDestructIsSafeWithoutAnOpenedImage(): void
    {
        $subject = new Subject();

        $subject->destruct();
        $subject->destruct();

        self::assertNull((new ReflectionProperty(Subject::class, '_imageHandler'))->getValue($subject));
    }

    public function testAdapterCanOpenResizeAndSaveAfterDestruct(): void
    {
        $subject = new Subject();
        $subject->open(self::$fileName);
        $subject->destruct();

        $target = (string) tempnam(sys_get_temp_dir(), 'gd2-resize-');

        try {
            $subject->open(self::$fileName);
            $subject->resize(32, 32);
            $subject->save(dirname($target), basename($target));

            $size = getimagesize($target);
            self::assertIsArray($size);
            self::assertSame(32, $size[0]);
            self::assertSame(32, $size[1]);
            self::assertSame(IMAGETYPE_PNG, $size[2]);
        } finally {
            $subject->destruct();
            unlink($target);
        }
    }
}
