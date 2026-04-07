<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\PaypalUk\Model\Express;

# use Mage;
# use Mage_PaypalUk_Model_Express_Pro as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\PaypalUk\Model\Express\ProTrait;

final class ProTest extends OpenMageTest
{
    use ProTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('paypaluk/express_pro');
        self::markTestSkipped('');
    }
}
