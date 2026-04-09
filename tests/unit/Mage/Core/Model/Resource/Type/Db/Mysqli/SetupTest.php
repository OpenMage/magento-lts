<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Resource\Type\Db\Mysqli;

// use Mage;
// use Mage_Core_Model_Resource_Type_Db_Mysqli_Setup as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Resource\Type\Db\Mysqli\SetupTrait;

final class SetupTest extends OpenMageTest
{
    use SetupTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('core/resource_type_db_mysqli_setup');
        self::markTestSkipped('');
    }
}
