<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Purifier as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\PurifierTrait;

final class PurifierTest extends OpenMageTest
{
    use PurifierTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/purifier');
    }

    /**
     * @dataProvider providePurify
     * @group Helper
     */
    public function testPurify(array|string $expectedResult, array|string $content): void
    {
        self::assertSame($expectedResult, self::$subject->purify($content));
    }
}
