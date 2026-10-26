<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Helper;

use Mage;
use Mage_Customer_Helper_Data as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\Helper\DataTrait;

final class DataTest extends OpenMageTest
{
    use DataTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('customer/data');
    }

    /**
     * @covers Mage_Customer_Helper_Data::generateResetPasswordLinkToken()
     * @group Helper
     */
    public function testGenerateResetPasswordLinkToken(): void
    {
        $firstToken = self::$subject->generateResetPasswordLinkToken();
        $secondToken = self::$subject->generateResetPasswordLinkToken();

        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $firstToken);
        self::assertNotSame($firstToken, $secondToken);
    }

    /**
     * @covers Mage_Customer_Helper_Data::generateResetPasswordLinkCustomerId()
     * @group Helper
     */
    public function testGenerateResetPasswordLinkCustomerId(): void
    {
        $firstId = self::$subject->generateResetPasswordLinkCustomerId(1);
        $secondId = self::$subject->generateResetPasswordLinkCustomerId(1);

        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $firstId);
        self::assertNotSame($firstId, $secondId);
    }
}
