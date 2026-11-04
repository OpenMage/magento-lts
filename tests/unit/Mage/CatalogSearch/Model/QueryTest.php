<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogSearch\Model;

use Exception;
use Mage;
use Mage_CatalogSearch_Model_Query as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogSearch\Model\QueryTrait;

/**
 * @phpstan-import-type ValidateData from QueryTrait
 */
final class QueryTest extends OpenMageTest
{
    use QueryTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalogsearch/query');
    }

    /**
     * @dataProvider provideValidateCatalogSearchQueryData
     * @param string[]|true $expectedResult
     * @phpstan-param ValidateData $data
     * @group Model
     * @throws Exception
     */
    public function testValidate(array|bool $expectedResult, array $data): void
    {
        self::$subject->setData($data);
        self::assertSame($expectedResult, self::$subject->validate());
    }
}
