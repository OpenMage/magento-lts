<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogInventory\Model\System\Config\Backend;

use Mage;
use Mage_CatalogInventory_Model_System_Config_Backend_Minqty as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogInventory\Model\System\Config\Backend\MinqtyTrait;

final class MinqtyTest extends OpenMageTest
{
    use MinqtyTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('cataloginventory/system_config_backend_minqty');
        self::markTestSkipped('');
    }
}
