<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab;

# use Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Properties as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\PropertiesTrait;

final class PropertiesTest extends OpenMageTest
{
    use PropertiesTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = new Subject();
        self::markTestSkipped('');
    }
}
