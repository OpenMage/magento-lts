<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Model\System\Config\Backend\Web\Secure;

use Mage;
use Mage_Adminhtml_Model_System_Config_Backend_Web_Secure_Offloaderheader as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\System\Config\Backend\Web\Secure\OffloaderheaderTrait;

final class OffloaderheaderTest extends OpenMageTest
{
    use OffloaderheaderTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('adminhtml/system_config_backend_web_secure_offloaderheader');
        self::markTestSkipped('');
    }
}
