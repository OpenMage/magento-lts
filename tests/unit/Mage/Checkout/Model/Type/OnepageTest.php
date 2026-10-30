<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Checkout\Model\Type;

use Mage;
use Mage_Checkout_Model_Type_Onepage as Subject;
use Mage_Customer_Model_Session;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Checkout\Model\Type\OnepageTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

final class OnepageTest extends OpenMageTest
{
    use OnepageTrait;

    /**
     * The onepage checkout must not call the deprecated Mage_Sales_Model_Quote::getCheckoutMethod()
     *
     * @param array<string, int|string> $quoteData
     * @group Model
     */
    #[DataProvider('provideQuoteData')]
    public function testGetCheckoutMethodWithoutDeprecatedQuoteMethod(array $quoteData, string $expected): void
    {
        $customerSession = $this->getMockWithCalledMethods(Mage_Customer_Model_Session::class, [
            'isLoggedIn' => false,
        ]);

        $quote = Mage::getModel('sales/quote');
        $quote->setData($quoteData);

        $subject = $this->getMockWithCalledMethods(Subject::class, [
            'getCustomerSession' => $customerSession,
            'getQuote'           => $quote,
        ]);
        (new ReflectionProperty(Subject::class, '_helper'))->setValue($subject, Mage::helper('checkout'));

        $deprecations = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$deprecations): bool {
            $deprecations[] = $errstr;
            return true;
        }, E_USER_DEPRECATED | E_DEPRECATED);

        try {
            $method = $subject->getCheckoutMethod();
        } finally {
            restore_error_handler();
        }

        self::assertSame($expected, $method);
        self::assertSame([], $deprecations);
    }
}
