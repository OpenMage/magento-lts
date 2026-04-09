<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Contacts\Model\System\Config\Backend;

// use Mage;
// use Mage_Contacts_Model_System_Config_Backend_Links as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Contacts\Model\System\Config\Backend\LinksTrait;

final class LinksTest extends OpenMageTest
{
    use LinksTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('contacts/system_config_backend_links');
        self::markTestSkipped('');
    }
}
