<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Security;

use Mage_Core_Model_Security_HtmlEscapedString as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\SecurityTrait;

class HtmlEscapedStringTest extends OpenMageTest
{
    use SecurityTrait;

    private static Subject $subject;

    /**
     * @dataProvider provideHtmlEscapedStringAsStringData
     * @param array<int, string> $allowedTags
     *
     * @group Model
     */
    public function testToSting(string $expectedResult, string $string, ?array $allowedTags): void
    {
        self::$subject = new Subject($string, $allowedTags);
        static::assertSame($expectedResult, self::$subject->__toString());
    }

    /**
     * @dataProvider provideHtmlEscapedStringGetUnescapedValueData
     * @param array<int, string> $allowedTags
     *
     * @group Model
     */
    public function testGetUnescapedValue(string $expectedResult, string $string, ?array $allowedTags): void
    {
        self::$subject = new Subject($string, $allowedTags);
        static::assertSame($expectedResult, self::$subject->getUnescapedValue());
    }
}
