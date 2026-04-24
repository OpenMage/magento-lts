<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogInventory\Model\System\Config\Backend;

// use Mage;
// use Mage_CatalogInventory_Model_System_Config_Backend_Minsaleqty as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogInventory\Model\System\Config\Backend\MinsaleqtyTrait;

final class MinsaleqtyTest extends OpenMageTest
{
    use MinsaleqtyTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('cataloginventory/system_config_backend_minsaleqty');
        self::markTestSkipped('');
    }
}
