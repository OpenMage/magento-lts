<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Model\System\Config\Backend\Admin;

// use Mage;
// use Mage_Adminhtml_Model_System_Config_Backend_Admin_Usecustompath as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\System\Config\Backend\Admin\UsecustompathTrait;

final class UsecustompathTest extends OpenMageTest
{
    use UsecustompathTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('adminhtml/system_config_backend_admin_usecustompath');
        self::markTestSkipped('');
    }
}
