<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Fedex\Source;

use Mage;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Source_Unitofmeasure as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class UnitofmeasureTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('usa/shipping_carrier_fedex_source_unitofmeasure');
    }
}
