<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Data;

use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Data_Collection as Subject;

final class CollectionTest extends OpenMageTest
{
    public function testGetFlagReturnsFalseForExplicitlyDisabledFlag(): void
    {
        $subject = new Subject();

        $subject->setFlag('disabled_flag', false);

        self::assertFalse($subject->getFlag('disabled_flag'));
        self::assertTrue($subject->hasFlag('disabled_flag'));
    }
}
