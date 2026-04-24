<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\GoogleAnalytics\Model\System\Config\Source;

// use Mage;
// use Mage_GoogleAnalytics_Model_System_Config_Source_Type as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\GoogleAnalytics\Model\System\Config\Source\TypeTrait;

final class TypeTest extends OpenMageTest
{
    use TypeTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('googleanalytics/system_config_source_type');
        self::markTestSkipped('');
    }
}
