<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Weee\Model\Config\Source\Fpt;

# use Mage;
# use Mage_Weee_Model_Config_Source_Fpt_Tax as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Weee\Model\Config\Source\Fpt\TaxTrait;

final class TaxTest extends OpenMageTest
{
    use TaxTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('weee/config_source_fpt_tax');
        self::markTestSkipped('');
    }
}
