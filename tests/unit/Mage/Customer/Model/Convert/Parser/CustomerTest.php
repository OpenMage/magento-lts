<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model\Convert\Parser;

// use Mage;
// use Mage_Customer_Model_Convert_Parser_Customer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\Model\Convert\Parser\CustomerTrait;

final class CustomerTest extends OpenMageTest
{
    use CustomerTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('customer/convert_parser_customer');
        self::markTestSkipped('');
    }
}
