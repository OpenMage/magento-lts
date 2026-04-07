<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Model\System\Config\Source\Cms\Wysiwyg;

# use Mage;
use Mage_Adminhtml_Model_System_Config_Source_Cms_Wysiwyg_Enabled as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\System\Config\Source\Cms\Wysiwyg\EnabledTrait;

final class EnabledTest extends OpenMageTest
{
    use EnabledTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('adminhtml/system_config_source_cms_wysiwyg_enabled');
        self::markTestSkipped('');
    }
}
