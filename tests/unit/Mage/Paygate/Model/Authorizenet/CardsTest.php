<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paygate\Model\Authorizenet;

# use Mage;
# use Mage_Paygate_Model_Authorizenet_Cards as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paygate\Model\Authorizenet\CardsTrait;

final class CardsTest extends OpenMageTest
{
    use CardsTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('paygate/authorizenet_cards');
        self::markTestSkipped('');
    }
}
