<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper\File\Storage;

// use Mage;
// use Mage_Core_Helper_File_Storage_Database as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\File\Storage\DatabaseTrait;

final class DatabaseTest extends OpenMageTest
{
    use DatabaseTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::helper('core/file_storage_database');
        self::markTestSkipped('');
    }
}
