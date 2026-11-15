<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model;

use Generator;
use Mage;
use Mage_Sales_Model_Quote_Address;
use Mage_SalesRule_Model_Rule as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\RuleTrait;
use PHPUnit\Framework\Attributes\DataProvider;

final class RuleTest extends OpenMageTest
{
    use RuleTrait;

    /**
     * The validation cache is keyed by the address id, which is null for an unsaved quote address
     *
     * @group Model
     */
    #[DataProvider('provideIsValidForAddress')]
    public function testIsValidForAddress(?int $addressId, bool $validationResult): void
    {
        /** @var Subject $subject */
        $subject = Mage::getModel('salesrule/rule');
        /** @var Mage_Sales_Model_Quote_Address $address */
        $address = Mage::getModel('sales/quote_address');
        $address->setId($addressId);

        /** @var list<string> $deprecations */
        $deprecations = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$deprecations): bool {
            $deprecations[] = $errstr;
            return true;
        }, E_DEPRECATED);

        try {
            $hasBefore = $subject->hasIsValidForAddress($address);
            $subject->setIsValidForAddress($address, $validationResult);
            $hasAfter = $subject->hasIsValidForAddress($address);
            $result = $subject->getIsValidForAddress($address);
        } finally {
            restore_error_handler();
        }

        self::assertFalse($hasBefore);
        self::assertTrue($hasAfter);
        self::assertSame($validationResult, $result);
        self::assertSame([], $deprecations);
    }

    public static function provideIsValidForAddress(): Generator
    {
        yield 'unsaved address, valid' => [null, true];
        yield 'unsaved address, invalid' => [null, false];
        yield 'saved address, valid' => [42, true];
    }
}
