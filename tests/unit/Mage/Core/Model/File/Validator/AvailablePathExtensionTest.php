<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\File\Validator;

use Iterator;
use Mage;
use Mage_Core_Model_File_Validator_AvailablePath as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class AvailablePathExtensionTest extends OpenMageTest
{
    /**
     * A protected extension before the final one is refused whatever its case (GHSA-4fxg-469f-r6fc)
     *
     * @dataProvider provideProtectedDoubleExtensionPaths
     * @group Model
     */
    public function testRefusesProtectedInnerExtension(string $path): void
    {
        self::assertFalse($this->createValidator()->isValid($path));
    }

    /**
     * @return Iterator<string, array{string}>
     */
    public static function provideProtectedDoubleExtensionPaths(): Iterator
    {
        yield 'lower case' => ['/sitemap/sitemap.php.xml'];
        yield 'upper case' => ['/sitemap/sitemap.PHP.xml'];
        yield 'phar' => ['/sitemap/sitemap.Phar.xml'];
    }

    /**
     * @group Model
     */
    public function testAcceptsAvailablePath(): void
    {
        self::assertTrue($this->createValidator()->isValid('/sitemap/sitemap.xml'));
    }

    private function createValidator(): Subject
    {
        /** @var Subject $validator */
        $validator = Mage::getModel('core/file_validator_availablePath');
        $validator->setPaths(['available' => ['xml' => '/sitemap/*.xml']]);

        return $validator;
    }
}
