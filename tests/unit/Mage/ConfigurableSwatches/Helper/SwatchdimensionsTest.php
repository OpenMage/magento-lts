<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ConfigurableSwatches\Helper;

# use Mage;
use Mage_ConfigurableSwatches_Helper_Swatchdimensions as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\ConfigurableSwatches\Helper\SwatchdimensionsTrait;

final class SwatchdimensionsTest extends OpenMageTest
{
    use SwatchdimensionsTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::helper('configurableswatches/swatchdimensions');
        self::markTestSkipped('');
    }
}
