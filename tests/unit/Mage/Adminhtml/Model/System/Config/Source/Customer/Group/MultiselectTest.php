<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Model\System\Config\Source\Customer\Group;

// use Mage;
// use Mage_Adminhtml_Model_System_Config_Source_Customer_Group_Multiselect as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\System\Config\Source\Customer\Group\MultiselectTrait;

final class MultiselectTest extends OpenMageTest
{
    use MultiselectTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('adminhtml/system_config_source_customer_group_multiselect');
        self::markTestSkipped('');
    }
}
