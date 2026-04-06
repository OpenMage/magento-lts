<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Persistent\Model\Observer;

use Mage;
use Mage_Persistent_Model_Observer_Session as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Persistent\Model\Observer\SessionTrait;

final class SessionTest extends OpenMageTest
{
    use SessionTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('persistent/observer_session');
        self::markTestSkipped('');
    }
}
