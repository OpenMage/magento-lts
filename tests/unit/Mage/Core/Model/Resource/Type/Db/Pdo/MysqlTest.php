<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Resource\Type\Db\Pdo;

# use Mage;
# use Mage_Core_Model_Resource_Type_Db_Pdo_Mysql as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Resource\Type\Db\Pdo\MysqlTrait;

final class MysqlTest extends OpenMageTest
{
    use MysqlTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('core/resource_type_db_pdo_mysql');
        self::markTestSkipped('');
    }
}
