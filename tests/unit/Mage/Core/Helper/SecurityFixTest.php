<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage_Core_Model_File_Validator_Image;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Image;
use Exception;

final class SecurityFixTest extends OpenMageTest
{
    public function testVarienImageBlocksPhar()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("File 'phar://test.jpg' is not readable.");
        new Varien_Image('phar://test.jpg');
    }

    public function testImageValidatorBlocksPhar()
    {
        // Mock helper to avoid translation issues if not set up
        // But OpenMageTest::setUpBeforeClass calls Mage::app() which might load generic helpers.
        // We expect Mage_Core_Exception or similar.

        $validator = new Mage_Core_Model_File_Validator_Image();

        try {
            $validator->validate('phar://test.jpg');
            self::fail('Should have thrown exception for phar path');
        } catch (Exception $exception) {
            self::assertStringContainsString('Invalid image path', $exception->getMessage());
        }
    }

    public function testWysiwygStorageBlocksPhar()
    {
        $this->markTestIncomplete(
            'Pending a real WYSIWYG storage test that invokes getFilesCollection() and asserts PHAR paths are blocked.',
        );
    }

    public function testImageValidatorRejectsIco()
    {
        // ICO files are no longer supported - they should be rejected as invalid MIME type
        $tempFile = tempnam(sys_get_temp_dir(), 'test_ico_');

        // Valid ICO header
        $icoHeader = "\x00\x00\x01\x00\x01\x00";
        file_put_contents($tempFile, $icoHeader . str_repeat("\x00", 100));

        $validator = new Mage_Core_Model_File_Validator_Image();

        try {
            $validator->validate($tempFile);
            self::fail('Should have thrown exception for ICO file');
        } catch (Exception $exception) {
            self::assertStringContainsString('Invalid', $exception->getMessage());
        } finally {
            @unlink($tempFile);
        }
    }
}
