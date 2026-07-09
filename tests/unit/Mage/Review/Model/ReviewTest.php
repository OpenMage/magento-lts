<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Review\Model;

use Mage_Review_Model_Review as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Review\ReviewTrait;

/**
 * @phpstan-import-type ValidateData from ReviewTrait
 */
final class ReviewTest extends OpenMageTest
{
    use ReviewTrait;

    /**
     * @dataProvider provideValidateReviewData
     * @param string[]|true      $expectedResult
     * @psalm-param ValidateData $data
     * @group Model
     */
    public function testValidate(array|bool $expectedResult, array $data): void
    {
        $mock = $this->createPartialMock(Subject::class, []);
        $mock->setData($data);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertSame($expectedResult, $mock->validate());
    }
}
