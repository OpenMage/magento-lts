<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model;

# use Mage;
# use Mage_SalesRule_Model_Rule as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\RuleTrait;

final class RuleTest extends OpenMageTest
{
    use RuleTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('salesrule/rule');
        self::markTestSkipped('');
    }
}
