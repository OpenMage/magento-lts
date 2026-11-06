<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model;

use Mage_Paypal_Model_Exception as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\ExceptionTrait;

final class ExceptionTest extends OpenMageTest
{
    use ExceptionTrait;

    /**
     * @dataProvider provideIsPayerActionRequiredData
     * @param array<string, mixed> $debugData
     * @group Model
     */
    public function testIsPayerActionRequired(bool $expected, string $message, array $debugData): void
    {
        self::assertSame($expected, (new Subject($message, $debugData))->isPayerActionRequired());
    }
}
