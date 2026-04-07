<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Message;

# use Mage;
# use Mage_Core_Model_Message_Error as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Message\ErrorTrait;

final class ErrorTest extends OpenMageTest
{
    use ErrorTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('core/message_error');
        self::markTestSkipped('');
    }
}
