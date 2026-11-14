<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Simplexml\Config\Cache;

use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Simplexml_Config_Cache_File as Subject;

final class FileTest extends OpenMageTest
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . DS . uniqid('om-sxml-', true);
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        $files = glob($this->dir . DS . '*');
        array_map(unlink(...), $files === false ? [] : $files);
        rmdir($this->dir);
        parent::tearDown();
    }

    /**
     * @group Varien_Simplexml
     */
    public function testLoadDoesNotUnserializeObjectsFromStatFile(): void
    {
        FileTestFixture::$awakened = false;
        $subject = new Subject(['dir' => $this->dir]);
        $subject->setKey('config');
        file_put_contents($subject->getStatFileName(), serialize([__FILE__ => new FileTestFixture()]));

        self::assertFalse($subject->load());
        self::assertFalse(FileTestFixture::$awakened);
    }
}
