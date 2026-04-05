<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Resource\Report\Rule;

use Mage;
use Mage_SalesRule_Model_Resource_Report_Rule_Createdat as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class CreatedatTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('salesrule/resource_report_rule_createdat');
    }
}
