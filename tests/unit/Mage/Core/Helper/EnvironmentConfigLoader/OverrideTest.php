<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper\EnvironmentConfigLoader;

# use Mage;
use Mage_Core_Helper_EnvironmentConfigLoader_Override as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\EnvironmentConfigLoader\OverrideTrait;

final class OverrideTest extends OpenMageTest
{
    use OverrideTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::helper('core/environmentconfigloader_override');
        self::markTestSkipped('');
    }
}
