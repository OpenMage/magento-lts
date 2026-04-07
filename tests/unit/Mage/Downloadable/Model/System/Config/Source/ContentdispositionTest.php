<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Model\System\Config\Source;

# use Mage;
use Mage_Downloadable_Model_System_Config_Source_Contentdisposition as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Downloadable\Model\System\Config\Source\ContentdispositionTrait;

final class ContentdispositionTest extends OpenMageTest
{
    use ContentdispositionTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('downloadable/system_config_source_contentdisposition');
        self::markTestSkipped('');
    }
}
