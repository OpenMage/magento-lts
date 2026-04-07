<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Widget\Model\Resource\Widget;

# use Mage;
# use Mage_Widget_Model_Resource_Widget_Instance as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Widget\Model\Resource\Widget\InstanceTrait;

final class InstanceTest extends OpenMageTest
{
    use InstanceTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('widget/resource_widget_instance');
        self::markTestSkipped('');
    }
}
