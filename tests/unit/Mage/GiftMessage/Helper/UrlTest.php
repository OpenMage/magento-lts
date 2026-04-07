<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\GiftMessage\Helper;

// use Mage;
// use Mage_GiftMessage_Helper_Url as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\GiftMessage\Helper\UrlTrait;

final class UrlTest extends OpenMageTest
{
    use UrlTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::helper('giftmessage/url');
        self::markTestSkipped('');
    }
}
