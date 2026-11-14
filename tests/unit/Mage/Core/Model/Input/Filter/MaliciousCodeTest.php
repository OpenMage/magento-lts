<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Input\Filter;

use Mage;
use Mage_Core_Model_Input_Filter_MaliciousCode as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Input\Filter\MaliciousCodeTrait;

final class MaliciousCodeTest extends OpenMageTest
{
    use MaliciousCodeTrait;

    /**
     * The regular expressions are only a first pass; filter() always finishes with HTMLPurifier,
     * which is what actually guarantees no event handler survives. GHSA-5656-5m7v-f3q9 reported
     * shapes that slip past the expressions alone, so lock in that the complete filter removes them.
     *
     * @dataProvider provideEventHandlerPayloads
     * @group Model
     */
    public function testFilterRemovesEventHandlers(string $input): void
    {
        /** @var Subject $subject */
        $subject = Mage::getModel('core/input_filter_maliciousCode');
        $output = $subject->filter($input);
        self::assertIsString($output);

        self::assertDoesNotMatchRegularExpression('/\son[a-z]+\s*=/i', $output);
        self::assertStringNotContainsStringIgnoringCase('javascript:', $output);
        self::assertStringNotContainsStringIgnoringCase('<script', $output);
    }
}
