<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Url as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class UrlTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/url');
    }

    /**
     * @group Model
     */
    public function testEscape(): void
    {
        static::assertSame('%22%27%3E%3C', self::$subject->escape('"\'><'));
    }

    /**
     * @group Model
     */
    public function testGetSecure(): void
    {
        static::assertIsBool(self::$subject->getSecure());
    }
}
