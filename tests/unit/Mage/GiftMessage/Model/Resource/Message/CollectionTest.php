<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\GiftMessage\Model\Resource\Message;

# use Mage;
# use Mage_GiftMessage_Model_Resource_Message_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\GiftMessage\Model\Resource\Message\CollectionTrait;

final class CollectionTest extends OpenMageTest
{
    use CollectionTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('giftmessage/resource_message_collection');
        self::markTestSkipped('');
    }
}
